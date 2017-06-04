<?php
/**
 * /v1/user/view/1?access-token=access_token
 */
namespace rest\versions\v1\controllers;

use rest\versions\v1\models\LoginForm;
use rest\versions\v1\models\SignupForm;
use common\controllers\BaseRestController;
use Yii;
use yii\rest\Controller;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\web\BadRequestHttpException;
use rest\actions\GetSmsAction;
use yii\filters\VerbFilter;

/**
 * Class UserController
 * @package rest\versions\v1\controllers
 */
class UserController extends BaseRestController
{
    public $modelClass = 'common\models\User';

    /**
     * get-sms mobile username password
     * @return array
     */
    public function actions()
    {
        //禁用默认方法
        return [
            'get-register-sms' => [
                'class' => GetSmsAction::className(),
                'mobile' => Yii::$app->getRequest()->post('mobile'),
                'beforeCallback' => [$this, 'registerSmsBeforeCallback'],
            ],
            'get-sms' => [
                'class' => GetSmsAction::className(),
                'mobile' => Yii::$app->getRequest()->post('mobile'),
                'beforeCallback' => [$this, 'loginSmsBeforeCallback'],
            ],
        ];
    }

    /**
     * 手机注册获取短信前验证
     * @param $action
     * @return bool
     */
    public function registerSmsBeforeCallback($action)
    {
        $model = new SignupForm();
        $model->setScenario(SignupForm::SCENARIO_GET_SMS);
        $model->load(Yii::$app->getRequest()->post(), '');
        $model->user_type = User::USER_TYPE_PERSON;
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }

    /**
     * 手机登录获取短信前验证
     * @param $action
     * @return bool
     */
    public function loginSmsBeforeCallback($action)
    {
        $model = new LoginForm();
        $model->setScenario(LoginForm::SC_SMS_LOGIN_PRE);
        $model->load(Yii::$app->getRequest()->post(), '');
        //前置操作，短信登录
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param \yii\base\Model $model the model to be accessed. If `null`, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws \yii\web\ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // check if the user can access $action and $model
        // throw ForbiddenHttpException if access should be denied
        if ($action === 'view' || $action === 'update' || $action === 'delete') {
            if ($model->id !== \Yii::$app->user->id)
                throw new BadRequestHttpException(sprintf('%s Your are NOT allowed to view other users ~~', $action));
        }
    }

    protected function chooseScenario($model)
    {
        $loginType = Yii::$app->getRequest()->post('login_type');
        switch ($loginType) {
            case LoginForm::SC_PASSWORD_LOGIN:
                $model->setScenario(LoginForm::SC_PASSWORD_LOGIN);
                break;
            case LoginForm::SC_SMS_LOGIN_PRE:
                $model->setScenario(LoginForm::SC_SMS_LOGIN_PRE);
                break;
            case LoginForm::SC_SMS_LOGIN:
            default:
                $model->setScenario(LoginForm::SC_SMS_LOGIN);
                break;
        }
    }

    protected function getTheAccessToken($debug = false)
    {
        $token = \Yii::$app->user->identity->getAccessToken();
        $accessTokenEnc = Yii::$app->rsa->privateEncrypt($token);
        if (!$debug) {
            return ['token' => strtoupper(bin2hex($accessTokenEnc)), 'id' => \Yii::$app->user->identity->id];
        }
        $accessTokenClient = Yii::$app->rsa->publicEncrypt($token);
        return [
            'token' => strtoupper(bin2hex($accessTokenEnc)),
            'token_client' => strtoupper(bin2hex($accessTokenClient)),
            'token_plain' => $token,
            'id' => \Yii::$app->user->identity->id
        ];
    }

    /**
     * 登录接口
     * 接收参数：mobile or username password loginType (password or sms)
     * @return string AuthKey or model with errors
     * @throws BadRequestHttpException
     */
    public function actionLoginTest()
    {
        $model = new LoginForm();
        $this->chooseScenario($model);
        if ($model->load(\Yii::$app->getRequest()->post(), '') && $model->login()) {
            return $this->getTheAccessToken(true);
        } else {
            throw new BadRequestHttpException(current($model->getErrors())[0]);
        }
    }

    /**
     * 登录接口
     * 接收参数：mobile or username password loginType (password or sms)
     * @return string AuthKey or model with errors
     * @throws BadRequestHttpException
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $this->chooseScenario($model);
        if ($model->load(\Yii::$app->getRequest()->post(), '') && $model->login()) {
            return $this->getTheAccessToken();
        } else {
            throw new BadRequestHttpException(current($model->getErrors())[0]);
        }
    }

    /**
     * 开发时测试token是否正确解密用
     * @return mixed 返回解密后的token
     */
    public function actionTokenTest()
    {
        $accessToken = Yii::$app->request->getQueryParam('access-token');
        $accessTokenBin = hex2bin($accessToken);
        $accessTokenPlain = Yii::$app->rsa->privateDecrypt($accessTokenBin);
        return $accessTokenPlain;
    }

    /**
     * 个人用户注册
     * @return LoginForm|string
     */
    public function actionRegister()
    {
        $model = new SignupForm();
        $model->setScenario(SignupForm::SCENARIO_CREATE);
        if ($model->load(Yii::$app->request->post(), '')) {
            try {
                $model->user_type = User::USER_TYPE_PERSON;
                $user = $model->signup();
                if ($user) {
                    unset($user->email, $user->password_hash, $user->auth_key, $user->pay_auth_key, $user->pay_pwd_hash, $user->reference_id, $user->logged_at);
                    $user->access_token = strtoupper(bin2hex(Yii::$app->rsa->privateEncrypt($user->access_token)));
                    return $user;
                } else {
                    $err = $model->getFirstError('username') . $model->getFirstError('mobile') . $model->getFirstError('password') . $model->getFirstError('sms_verify_code');
                    throw new BadRequestHttpException($err);
                }
            } catch (\Exception $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        }
        throw new BadRequestHttpException('bad request.');
    }

    /**
     * 终端用户注册服务条款
     * @return string
     */
    public function actionEndUserLicense()
    {
        return \common\widgets\DbText::widget([
            'key' => 'end_user_license'
        ]);
    }


    /**
     * 国旅企业用户注册
     * 参数：
     * 公司类型
     * 公司名称 company_name
     * 经营许可证
     * 省 province_id 市 city_id
     * 联系人姓名 contact_name
     * 联系人电话 contact_phone
     * 办公地址 address
     * @return LoginForm|string
     * @throws BadRequestHttpException
     */
    public function actionRegisterComp()
    {
        $curUserId = Yii::$app->user->getId();
        $user = User::findOne($curUserId);
        //除登录权限外，还要额外检测合作者身份，不是合作者不允许调用此接口
        if (1 != $user->is_partner) {
            throw new BadRequestHttpException('not partner.');
        }
        $model = new SignupForm();
        $model->setScenario(SignupForm::SCENARIO_CREATE_COMP);
        if ($model->load(Yii::$app->request->post(), '')) {
            try {
                $model->user_type = User::USER_TYPE_COMPANY;
                $model->reference_id = $curUserId;
                $user = $model->signupComp();
                if ($user) {
                    unset($user->email, $user->password_hash, $user->auth_key, $user->pay_auth_key, $user->pay_pwd_hash, $user->reference_id, $user->logged_at);
                    $user->access_token = strtoupper(bin2hex(Yii::$app->rsa->privateEncrypt($user->access_token)));
                    return [
                        'id' => $user->id,
                        'username' => $user->username,
                        'mobile' => $user->mobile,
                        'status' => $user->status,
                        'user_type' => $user->user_type,
                        'access_token' => $user->access_token,
                        'company_id' => $user->companyInfo->id,
                    ];
                } else {
                    $err = current($model->getErrors())[0];
                    throw new BadRequestHttpException($err);
                }
            } catch (\Exception $e) {
                $err = current($model->getErrors())[0];
                throw new BadRequestHttpException($err);
            }
        }
        $err = current($model->getErrors())[0];
        throw new BadRequestHttpException($err);
    }
}
