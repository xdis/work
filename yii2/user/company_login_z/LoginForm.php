<?php
namespace company\models;

use cheatsheet\Time;
use common\models\User;
use common\models\UserCompany;
use common\models\Company;
use ihacklog\sms\models\Sms;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\Model;
use yii\web\ForbiddenHttpException;
use company\models\AuthAssign;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $mobile;
    public $sms_verify_code;
    public $company_id;
    public $admin_password;
    public $loginType = 'login';

    private $user = false;
    private $onlyOnePasswdMatched = false;
    private $onlyOneCompanyMatched = false;
    private $_company_count = 0;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'mobile', 'sms_verify_code', 'password', 'company_id'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            [['mobile', 'number', 'max' => 11,'min'=>11],'required'],
            [['sms_verify_code','string','max' => 4,'min'=>4], 'required'],
            ['sms_verify_code', 'validateVeifyCode'],
            [['company_id', 'number'], 'required', 'message' => '请选择公司!'],
            ['company_id', 'validateCompanyId'],
            ['admin_password', 'validateAdminPassword'],
            ['mobile', 'validateMobileGetSms', 'on' => 'sms_login_pre'],
            ['loginType', 'safe'],
        ];
    }

    /**
     * Validates theVeifyCode.
     * This method serves as the inline validation for password.
     */
    public function validateVeifyCode($attribute)
    {
        $sms = new Sms();
        if (!$sms->verify($this->mobile, $this->sms_verify_code, Yii::$app->sms->verifyTemplateId)) {
            $this->addError('sms_verify_code', '短信验证码错误');
        }
        $this->username = $this->mobile;
        $users = $this->getUsers();
        $user_ids = [];
        if (!$this->hasErrors()) {
            $users = $this->getUsers();
            if ($users) {
                foreach ($users as $user) {
                        $user_ids[] = $user->id;
                }
            }
        }
        Yii::$app->session->set('login_user_ids', $user_ids);

        if (count($users) == 1) {
            $this->onlyOnePasswdMatched = true;
            $this->user = $users[0];
            if ($this->user->companies && count($this->user->companies) == 1) {
                $this->onlyOneCompanyMatched = true;
                $this->company_id = $this->user->companies[0]->id;
                $this->_company_count = count($this->user->companies);
            }
        } else {
            $this->user = null;
        }
    }

    /**
     * company_id校验
     * @param $attr
     * @author cmk
     * @return bool
     */
    public function validateCompanyId($attr)
    {
        $usernameOrMobile = Yii::$app->user->getPreLoginMobile();
        $this->username = $usernameOrMobile;
        //do not validate ucenter login
        if ($this->company_id == 0) {
            $user = $this->getPersonUser();
            $this->user = $user;
            return true;
        }
        $user = $this->getCompanyUser();
        if (!$user) {
            $this->addError($attr, '公司登录错误!');
        }
        $company = Company::findOne($this->$attr);
        if (!$company) {
            $this->addError($attr, '请选择公司!');
        }
        $dimissioned = UserCompany::find()->where(['user_id' => $user->id, 'company_id' => $company->id, 'staff_status' => 0])->count() > 0;
        if ($dimissioned) {
            $this->addError($attr, '已经离职，不能登录!');
        }
        if (!AuthAssign::userHasRole($user->id, $company->id, 4) && $user->id != $company->user_id) {
            $this->addError($attr, '您没有权限登录此公司!');
        }
        $this->user = $user;
    }

    public function validateAdminPassword($attr)
    {
        $usernameOrMobile = Yii::$app->user->getPreLoginMobile();
        $this->username = $usernameOrMobile;
        //do not validate ucenter login
        if ($this->company_id == 0) {
            $user = $this->getPersonUser();
            $this->user = $user;
            return true;
        } else {
            $user = $this->getCompanyUser();
        }
        if (!$user) {
            $this->addError($attr, '系统错误!');
            return false;
        }
        $company = Company::findOne($this->company_id);
        if (!$company) {
            $this->addError($attr, '请选择公司!');
            return false;
        }
        //自己创建的公司，不要管理密码
        if ($company->user_id == $user->id) {
            return true;
        }
        if (!$user) {
            $this->addError('company_id', '公司登录错误!');
        }
        $this->user = $user;
        $uc = UserCompany::find()->where(['user_id' => $user->id, 'company_id' => $this->company_id])->one();
        if (!$uc->validateAdminPassword($this->$attr)) {
            $this->addError($attr, '管理密码错误!');
            Yii::$app->user->logout();
        }
    }

    public function validateMobileGetSms($attr)
    {
        $users = User::findUsersByMobile($this->$attr);
        if (!$users || count($users) <= 0) {
            $this->addError($attr, '该手机号码未注册！请仔细检查输入.');
        }
    }

    public function scenarios()
    {
        return [
            'login' => ['username', 'password', 'rememberMe'],
            'sms_login_pre' => ['mobile'],
            'sms_login' => ['mobile', 'sms_verify_code'],
            'company_login' => ['company_id', 'admin_password'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'rememberMe' => '记住我'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        $hasValidateOne = false;
        $matched = 0;
        $user_ids = [];
        if (!$this->hasErrors()) {
            $users = $this->getUsers();
            if ($users) {
                foreach ($users as $user) {
                    if ($user->validatePassword($this->password)) {
                        $hasValidateOne = true;
                        $this->user = $user;
                        $user_ids[] = $user->id;
                        $matched++;
                    }
                }
            }
        }
        if (!$hasValidateOne) {
            $this->addError('password', '用户名或密码错误!');
        }
        Yii::$app->session->set('login_user_ids', $user_ids);
        if (1 == $matched) {
            $this->onlyOnePasswdMatched = true;
            if ($this->user->companies && count($this->user->companies) == 1) {
                $this->onlyOneCompanyMatched = true;
                $this->company_id = $this->user->companies[0]->id;
                $this->_company_count = count($this->user->companies);
            }
        } else {
            $this->user = null;
        }
    }

    /**
     * 前置登录
     * @return bool
     */
    public function preLogin()
    {
        if (!$this->validate()) {
            return false;
        }
        if ('sms_login' ==  $this->loginType) {
            Yii::$app->user->preLogin($this->mobile);
        } else {
            Yii::$app->user->preLogin($this->username);
        }
        if (empty($this->username)) {
            $this->username = $this->mobile;
        }
        //如果只找到一个用户
        if ($this->onlyOnePasswdMatched) {
            //个人类型的用户
            if ($this->user->user_type == User::USER_TYPE_PERSON) {
                //没有加入任何公司
                if ($this->user->getCompanies()->count() == 0) {
                    $this->loginUcenter();
                }
                if (Yii::$app->request->get('login_ucenter_only') == 1) {
                    $this->loginUcenter();
                }

                //个人用户一定要有管理密码，不能自动登录
/*                if ($this->onlyOneCompanyMatched) {
                    //只有创始人不需要管理密码，然而个人用户不可能是创始人
                    $company = Company::findOne($this->company_id);
                    if (!$company) {
                        throw new ErrorException('公司数据错误1!');
                    }
                    if (Yii::$app->user->getId() == $company->user_id) {
                        Yii::$app->user->companyLogin($company);
                    }
                }*/
            } else {
                //对于创始人，不要求有user_company关联，而且，也不能加入别的公司
                if ($this->user->getCompanies()->count() == 0) {
                    $this->loginUcenter();
                    //企业用户,只能登录它自己的公司
                    //只有创始人不需要管理密码
                    $company = Company::findOne(['user_id' => $this->user->id]);
                    if (!$company) {
                        throw new ErrorException('公司数据错误2!');
                    }
//                $company = Company::findOne($this->company_id);
                    Yii::$app->user->companyLogin($company);
                }
            }

        }
        return true;
    }

    /**
     * 真正登录
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     * @throws ForbiddenHttpException
     */
    public function login($user = null)
    {
        $this->setScenario('company_login');
        if (!$this->validate()) {
            return false;
        }
        if ($this->company_id != 0) {
            Yii::$app->user->companyLogin(Company::findOne($this->company_id));
        }
        return $this->loginUcenter($user);
    }

    /**
     * 只登录个人中心
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     * @throws ForbiddenHttpException
     */
    public function loginUcenter($user = null)
    {
        $user = is_null($user) ? $this->getUser() : $user;
        $duration = $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0;
        if (Yii::$app->user->login($user, $duration)) {
/*            if (!Yii::$app->user->can('loginToBackend')) {
                Yii::$app->user->logout();
                throw new ForbiddenHttpException;
            }*/
            return true;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::find()
                ->andWhere(['username'=>$this->username])
                ->one();
        }

        return $this->user;
    }

    /**
     * get person user (only one)
     * @author HuangYeWuDeng
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getPersonUser()
    {
        return User::find()
            ->person()
            ->andWhere(['or', ['username'=>$this->username], ['mobile'=>$this->username]])
            ->one();
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUsers()
    {
        if ($this->user === false) {
            if (is_numeric($this->username)) {
                $where = ['mobile'=>$this->username];
            } else {
                //only one
                $where = ['username'=>$this->username];
            }
            $this->user = User::find()
                ->andWhere($where)
                ->all();
        }

        return $this->user;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getCompanyUser()
    {
        $users = User::find()
           ->andWhere(['or', ['username'=>$this->username], ['mobile'=>$this->username]])
           ->all();

        foreach ($users as $user) {
            if ($user->user_type == User::USER_TYPE_COMPANY) {
                $company = Company::find()->where(['user_id' => $user->id])->one();
                if ($company) {
                    return $user;
                }
            } else {
                foreach ($user->companies as $company) {
                    if ($company->id == $this->company_id) {
                        return $user;
                    }
                }
            }
        }
        return null;
    }
}
