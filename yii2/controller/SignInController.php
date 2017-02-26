<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/2/14
 * Time: 11:20 AM
 *
 * 注册/登录/找回密码
 */

namespace company\controllers;

use company\models\LoginForm;
use company\models\ResetAdminPasswordForm;
use company\models\SignupForm;
use company\models\ResetPasswordForm;
use common\actions\GetSmsAction;
use common\models\User;
use common\models\Company;
use company\models\UserCompany;
use Yii;
use yii\base\Exception;
use yii\base\Response;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use common\controllers\BaseController;
use yii\helpers\Url;

class SignInController extends BaseController
{

    public $defaultAction = 'login';

    /*    public function behaviors()
        {
            return [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'logout' => ['post']
                    ]
                ]
            ];
        }*/

    public function actions()
    {

        return [
            'get-sms' => [
                'class' => GetSmsAction::className(),
//              'mobile' => Yii::$app->getRequest()->post((new SignupForm)->formName())['mobile'],
                'beforeCallback' => [$this, 'registerSmsBeforeCallback'],
                'initCallback' => [$this, 'registerSmsInitCallback'],
            ],
            'get-reset-pwd-sms' => [
                'class' => GetSmsAction::className(),
                'beforeCallback' => [$this, 'resetPwdBeforeCallback'],
                'initCallback' => [$this, 'resetSmsInitCallback'],
            ],
            'get-login-sms' => [
                'class' => GetSmsAction::className(),
                'beforeCallback' => [$this, 'loginSmsBeforeCallback'],
                'initCallback' => [$this, 'loginSmsInitCallback'],
            ],
        ];
    }

    public function registerSmsInitCallback($action)
    {
        $mobile = Yii::$app->getRequest()->post((new SignupForm)->formName())['mobile'];
        $action->mobile = $mobile;
    }

    public function resetSmsInitCallback($action)
    {
        $mobile = Yii::$app->getRequest()->post((new ResetPasswordForm)->formName())['mobile'];
        $action->mobile = $mobile;
    }

    public function loginSmsInitCallback($action)
    {
        $mobile = Yii::$app->getRequest()->post((new LoginForm)->formName())['mobile'];
        $action->mobile = $mobile;
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
        $model->load(Yii::$app->getRequest()->post());
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }

    /**
     * 手机找回密码获取短信验证码前验证
     * @param $action
     * @return bool
     */
    public function resetPwdBeforeCallback($action)
    {
        $model = new ResetPasswordForm();
        $model->setScenario(ResetPasswordForm::SCENARIO_GET_SMS);
        $model->load(Yii::$app->getRequest()->post());
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }

    /**
     * 手机注册获取短信前验证
     * @param $action
     * @return bool
     */
    public function loginSmsBeforeCallback($action)
    {
        $model = new LoginForm();
        $model->setScenario('sms_login_pre');
        $model->load(Yii::$app->getRequest()->post());
        //前置操作，短信登录
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }

    public function actionLogin()
    {
        $this->layout = 'base';
        $returnUrl = Yii::$app->getUrlManager()->createUrl(['index/index']);
        $model = new LoginForm();
        $post = Yii::$app->getRequest()->post($model->formName());
        if (!Yii::$app->user->isGuest && !isset($post['company_id'])) {
            return $this->ajaxSuccess('已经登录', $returnUrl);
        }
        if (Yii::$app->getRequest()->isGet) {
            return $this->render('login', [
                'model' => $model
            ]);
        }

        if (Yii::$app->getRequest()->isPost && Yii::$app->user->isPreLoginPassed()) {
            $model->setScenario('company_login');
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                Yii::$app->session->remove('login_user_ids');
                return $this->ajaxSuccess('登录成功', $returnUrl);
            } else {
                $error = $model->getErrors();
                return $this->ajaxFail('登录失败 ' . current($error)[0]);
            }
        }

        $post = Yii::$app->getRequest()->post();
        if (!isset($post['LoginForm']['loginType'])) {
            return $this->ajaxFail('参数错误!');
        }
        if ('sms_login' == $post['LoginForm']['loginType']) {
            $model->setScenario('sms_login');
        } else {
            $model->setScenario('login');
        }
        try {
            if ($model->load(Yii::$app->request->post()) && $model->preLogin()) {
                $returnUrl = '';
                if (Yii::$app->user->getCompanyId()) {
                    $returnUrl = Yii::$app->getUrlManager()->createUrl(['index/index']);
                } elseif (Yii::$app->user->getId()) {
                    $returnUrl = Yii::$app->getUrlManager()->createUrl(['ucenter/index']);
                    $user = User::findOne(Yii::$app->user->getId());
                    if ($user->getCompanies()->count() > 0) {
                        $returnUrl = '';
                    }
                }
                return $this->ajaxSuccess('验证成功', $returnUrl);
            } else {
                $error = $model->getErrors();
                return $this->ajaxFail('登录失败 ' . current($error)[0]);
            }
        } catch (Exception $e) {
            return $this->ajaxFail('登录失败 ' . $e->getMessage());
        }
    }

    public function actionGetCompanies()
    {
        if (!Yii::$app->user->isPreLoginPassed()) {
            return $this->ajaxFail('PreLogin not passed!');
        }
        $model = new LoginForm();
        $model->username = Yii::$app->user->getPreLoginMobile();
        $users = User::findAll(Yii::$app->session->get('login_user_ids'));
        $companies = [];
        $ownCompany = [];
        $hasPerson = false;
        $hasCompany = false;
        foreach ($users as $user) {
            $mycompany = Company::findOne(['user_id' => $user->id]);
            if ($mycompany) {
                $uc = UserCompany::find()->where(['user_id' => $user->id, 'company_id'=> $mycompany->id])->one();
                $isAdminPwdSet = $uc && $uc->isAdminPwdSet();
                $ownCompany[] = [
                    'user_id' => $user->id,
                    'company_id' => $mycompany->id,
                    'name' => $mycompany->name,
                    'user_type' => $user->user_type,
                    'is_owner' => (int)($user->id == $mycompany->user_id),
                    'logo_base_url' => $mycompany->logo_base_url,
                    'logo_path' => $mycompany->logonail_path,
                    'is_admin_pwd_set' => (int) $isAdminPwdSet
                ];
            }
            foreach ($user->companies as $company) {
                $hasCompany = true;
                $uc = UserCompany::find()->where(['user_id' => $user->id, 'company_id'=> $company->id, 'staff_status' => 1])->one();
                if (!$uc) {
                    //跳过离职的员工
                    continue;
                }
                $isAdminPwdSet = $uc && $uc->isAdminPwdSet();
                $companies[] = [
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'name' => $company->name,
                    'user_type' => $user->user_type,
                    'is_owner' => (int)($user->id == $company->user_id),
                    'logo_base_url' => $company->logo_base_url,
                    'logo_path' => $company->logonail_path,
                    'is_admin_pwd_set' => (int) $isAdminPwdSet
                ];
            }
            if ($user->user_type == User::USER_TYPE_PERSON) {
                $hasPerson = true;
                $companies[] = [
                    'user_id' => $user->id,
                    'company_id' => 0,
                    'name' => '个人中心',
                    'user_type' => $user->user_type,
                    'is_owner' => 0,
                    'logo_base_url' => '',
                    'logo_path' => '',
                ];
            }
        }
        if (count($ownCompany) > 0) {
            $companies = array_merge($companies, $ownCompany);
        }
        //@TODO 如果取到的用户只有一个
        //是企业用户则直接登录企业后台
        //是个人用户则直接登录个人后台
        return $this->ajaxSuccess('succ', '', $companies);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * 注册
     * 支持个人和企业注册，企业和个人均需设置微叮号、登录密码，并通过手机号码验证。
     * 保证微叮号在系统中唯一；微叮号不能为纯数字
     * @return string|Response
     */
    public function actionSignup()
    {
        $this->layout = 'base';
//        Yii::$app->session->setFlash('forceUpdateLocale');
        //return $this->refresh();

        $model = new SignupForm();
        $model->setScenario(SignupForm::SCENARIO_CREATE);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $user = $model->signup();
                if ($user) {
                    //自动登录
                    Yii::$app->getUser()->login($user);
                    //公司选择,因为是注册，因此资料是全新的。
                    //公司用户只有一个公司，而个人用户刚注册的还不存在公司.
                    $returnUrl = '';
                    $company = Company::find()->where(['user_id' => $user->id])->one();
                    if ($company) {
                        Yii::$app->getUser()->switchCompany($company->id);
                        $returnUrl = Yii::$app->getUrlManager()->createUrl(['index/index']);
                    } else {
                        $returnUrl = Yii::$app->getUrlManager()->createUrl(['ucenter/index']);
                    }
                    return $this->ajaxSuccess('创建成功', $returnUrl);
                } else {
                    $err = $model->getFirstError('username') . $model->getFirstError('mobile') . $model->getFirstError('password') . $model->getFirstError('sms_verify_code');
                    return $this->ajaxFail('创建失败,请检查输入.' . $err);
                }
            } catch (\Exception $e) {
                return $this->ajaxFail('创建失败,系统错误' . $e->getMessage());
            }
        }
        return $this->render('register', [
            'model' => $model
        ]);
    }

    /**
     * 找回登录密码 - 获取短信验证码
     * @throws BadRequestHttpException
     */
    public function actionResetPasswordSms()
    {
        $this->layout = 'base';
        $model = new ResetPasswordForm();
        $model->setScenario(ResetPasswordForm::SCENARIO_GET_SMS);
        return $this->render('resetPasswordSms', [
            'model' => $model,
        ]);
    }

    /**
     * 找回登录密码 -校验短信验证码
     * @throws BadRequestHttpException
     */
    public function actionResetPasswordVerify()
    {
        $this->layout = 'base';
        $model = new ResetPasswordForm();
        $model->setScenario(ResetPasswordForm::SCENARIO_VALIDATE_SMS);
        $model->load(Yii::$app->getRequest()->post());
        if ($model->validate()) {
            return $this->ajaxSuccess('校验成功');
        }
        return $this->ajaxFail('短信验证码校验失败');
    }

    /**
     * 找回登录密码 - 选择用户
     * @author HuangYeWuDeng
     */
    public function actionResetPasswordUsers()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //只能是手机号
        $mobile = Yii::$app->getSession()->get((new ResetPasswordForm())->formName() . '.mobile');
//        $mobile = '18575508763';
        if (!$mobile) {
            return $this->ajaxFail('请先验证短信');
        }
        $users = User::find()->select('id,username,user_type')->where(['mobile' => $mobile])->all();
        return $this->ajaxSuccess('succ', '', $users);
    }

    /**
     * 找回登录密码 - 修改密码 接收参数： id, password
     * @return string|Response
     * @throws BadRequestHttpException
     */
    public function actionResetPassword()
    {
        $this->layout = 'base';
        $model = new ResetPasswordForm();
        $model->setScenario(ResetPasswordForm::SCENARIO_RESET_PWD);

        if (Yii::$app->getRequest()->isGet) {
            return $this->render('resetPassword', [
                'model' => $model,
            ]);
        }
        $mobile = Yii::$app->getSession()->get((new ResetPasswordForm())->formName() . '.mobile');
        if (!$mobile) {
            return $this->ajaxFail('请先验证短信');
        }
        $model->load(Yii::$app->getRequest()->post());
        if ($model->resetPassword()) {
            $body = '修改成功';
            $options = ['class' => 'alert-success'];
            return $this->ajaxSuccess($body);
        } else {
            $body = '修改失败：' . $model->getFirstError('id');
            $options = ['class' => 'alert-success'];
            return $this->ajaxFail($body);
        }
    }

    /**
     * 重设管理密码请求,只有个人用户需要（企业用户不需要管理密码）
     * @author HuangYeWuDeng
     */
    public function actionAdminPwdResetReq()
    {
        $userId = Yii::$app->request->post('user_id');
        $companyId = Yii::$app->request->post('company_id');
        $uc = \common\models\UserCompany::findOne(['user_id' => $userId, 'company_id' => $companyId, 'staff_status' => 1, 'is_deleted' => 0]);
        if ($uc) {
            //请求重置管理密码
            $uc->apply_reset_pwd = 1;
            if ($uc->save()) {
                return $this->ajaxSuccess('您将发送重置管理密码申请，企业管理员同意后会发送新密码至您的注册手机号。');
            }
            return $this->ajaxFail('系统错误，重置管理密码，请稍后再试.');
        }
        return $this->ajaxFail('员工不存在或不是在职状态或已经被删除.');
    }

    /**
     * 首次登录，设置6位数字的管理密码... (为什么要规定人家用6位数字？你以为你家是银行啊？)
     * 只有个人用户需要（企业用户不需要管理密码）
     * @author HuangYeWuDeng
     */
    public function actionAdminPwdSet()
    {
        if (!Yii::$app->user->isPreLoginPassed()) {
            return $this->ajaxFail('未经过验证不能修改管理密码...');
        }
        //'修改管理密码成功!'
        $model = new ResetAdminPasswordForm();
        $model->load(Yii::$app->getRequest()->post(), '');
        if ($model->resetAdminPassword()) {
            return $this->ajaxSuccess('修改管理密码成功!');
        }
        return $this->ajaxFail(current($model->getErrors())[0]);
    }
}
