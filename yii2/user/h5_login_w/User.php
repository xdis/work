<?php
namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserQuery;
use common\models\SupplierV2;
use yii\data\ActiveDataProvider;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use company\models\Withdraw;
use company\models\BankCard;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $mobile
 * @property string $password_hash
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property integer $user_type
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property string $logged_ip
 * @property string $password write-only password
 * @property string $device_name 登录设备名称
 * @property string $device_model 设备型号
 * @property string $access_token_prev 上次的登录token
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMINISTRATOR = 'administrator';

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    const USER_TYPE_COMPANY = 1;
    const USER_TYPE_PERSON = 2;

    const LOGIN_TYPE_PASSWORD = 1;
    const LOGIN_TYPE_SMS = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],
            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'unique'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['pay_pwd_hash', 'logged_ip'],'safe'],
            [['device_name', 'device_model'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '微叮号',
            'mobile' => '手机号码',
            'email' => Yii::t('common', 'E-mail'),
            'status' => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at' => '注册时间',
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => '最近登录',
            'logged_ip' => '最近登录IP',
        ];
    }

    /**
     * 关联个人资料
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }
    
    /**
     * 关联提现
     */
    public function getWithdraw()
    {
    	return $this->hasOne(Withdraw::className(), ['request_by' => 'id']);
    }
    
    /**
     * 关联公司
     */
    public function getCompanies2()
    {
        return $this->hasMany(Company::className(), ['id' => 'company_id'])
            ->viaTable('user_company', ['user_id' => 'id']);
    }

    /**
     * 关联公司
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::className(), ['id' => 'company_id'])
            ->via('userCompanies');
    }

    /**
     * 关联公司员工
     */
    public function getUserCompanies()
    {
        return $this->hasMany(UserCompany::className(), ['user_id' => 'id']);
    }
    
    /**
     * 关联公司
     */
    public function getCompanyInfo()
    {
        return $this->hasOne(Company::className(), ['user_id' => 'id']);
    }

    /**
     * 关联银行卡
     */
    public function getBankCard()
    {
    	return $this->hasOne(BankCard::className(), ['request_by' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessTokenPrev($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token_prev' => $token, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['username' => $username, 'status' => self::STATUS_ACTIVE])
            ->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    /**
     * 根据手机号查找个人用户
     *
     * @param string $mobile
     * @return static|null
     */
    public static function findPersonUserByMobile($mobile)
    {
        return static::find()
            ->active()
            ->person()
            ->andWhere(['mobile' => $mobile])
            ->one();
    }

    /**
     * 根据手机号查找用户（很可能是多个）
     *
     * @param string $mobile
     * @return static|null
     */
    public static function findUsersByMobile($mobile)
    {
        return static::find()
            ->active()
            ->andWhere(['mobile' => $mobile])
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }
    
    /**
     * @inheritdoc
     */
    public function getAccount()
    {
    	return $this->hasOne(Account::className(), ['id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }
     /*
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePayPassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->pay_pwd_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPaypassword($password)
    {
       return $this->pay_pwd_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETED => Yii::t('common', 'Deleted')
        ];
    }

    /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);
        $account = new Account();
        $account->id = $this->getId();
        $this->link('account', $account);
        if ($this->user_type == self::USER_TYPE_COMPANY) {
            //添加公司
            $company = new Company();
            $company->name = $this->username;
            $company->brand_name = $this->username;
            $company->user_id = $this->getId();
            $company->city_name = '1';
            $company->address = '';
            $company->contact_name = '';
            $company->contact_phone = '';
            $company->delegate_name = '';
            $company->delegate_idcard = '';
            $company->license_path = '';
            $company->business_license = '';
            $company->request_by = $this->getId();
            $this->link('companyInfo', $company);
            //插入公司后自动初始化产品默认产品分类数据
            if ($this->companyInfo && is_object($this->companyInfo)) {
                $company_id = $this->companyInfo->id;
                $result = Yii::$app->db->createCommand('CALL p_add_com_product_category_a(:company_id)')
                    ->bindValue(':company_id', $company_id)
                    ->execute();
            }
/*            $userCompany = new UserCompany();
            $userCompany->user_id = $this->getId();
            $userCompany->company_id = '';*/
        }

        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
//        $auth = Yii::$app->authManager;
//        $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->userProfile && $this->userProfile->nickname) {
            return $this->userProfile->nickname;
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }

    /**
     * 创建用户的时候，创建账户,没有校验
     * @return bool
     */
    public function addUser()
    {
        $trans = Yii::$app->db->beginTransaction();
        try {
            $this->setIsNewRecord(true);
            $user_id = $this->save();
            if (!$user_id) {
                throw new \Exception('failed to create user.');
            }
            $account = new Account();
            $account->createAccount($user_id);
            $trans->commit();
            return $user_id;
        } catch (\Exception $e) {
            $trans->rollBack();
            return false;
        }
    }
    
    /**
     * 根据user_id 返回用户信息
     * @param unknown $user_id
     * @param string $type   true 返回用户手机  false 返回用户微叮号
     */
    static function getUserName($user_id , $type = ''){
    	$arr =  self::find()->where(['id'=>$user_id])->select("username,mobile")->one();
    	if($type){
    		return $arr->mobile;
    	}else {
    		return $arr->username;
    	}
    }
    
    /**
     * 根据user_id 返回用户姓名
     */
    static function getUserReal($user_id){
    	$arr =  UserProfile::find()->where(['user_id'=>$user_id])->select("real_name")->one();
    	return $arr->real_name;
    }

    public function getCompanyLogo($view, $bundle)
    {
        $logo = '';
        $company_id = Yii::$app->user->getCompanyId();
        if ($company_id) {
            $company = Company::findOne($company_id);
            $logo = $company->logo_base_url . '/' . $company->logonail_path;
        }
        if (empty($logo)) {
            $logo = Yii::$app->user->identity->userProfile->getAvatar($view->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg'));
        }
        return $logo;
    }

    /**
     * 用户类型是否是公司类型的
     * @author HuangYeWuDeng
     */
    public function getIsCompanyUserType()
    {
        return $this->user_type == self::USER_TYPE_COMPANY;
    }

    /**
     * 获取认证企业
     * @param $type 1为供应商、2为客户
     * @return array
     */
    public static function getCompanyUserType($type,$id=null)
    {
        $company_id = Yii::$app->user->getCompanyId();
        if($type==1){ //查供应商
            $where['cus_company_id'] = $company_id;
            $cusSupData = CusSup::find()->select('sup_company_id')->where($where)
                                        ->andFilterWhere(['<>','sup_company_id',$id])
                                        ->asArray()->all();
            $ids = array_column($cusSupData, 'sup_company_id');
        }else{//查供客户管理
            $where['sup_company_id'] = $company_id;
            $cusSupData = CusSup::find()->select('cus_company_id')->where($where)
                                        ->andFilterWhere(['<>','cus_company_id',$id])
                                        ->asArray()->all();
            $ids = array_column($cusSupData, 'cus_company_id');
        }

        $data = Company::find()->leftJoin('user AS u','u.id = company.user_id')
                                ->select("company.id,company.name as company_name,company.audit_status,u.username")
                                ->where(['u.user_type'=>self::USER_TYPE_COMPANY,'company.audit_status'=>1])
                                ->andWhere(['not in','company.id',$ids])
                                ->andWhere(['<>','company.id',$company_id])
                                ->asArray()->all();

        foreach ($data as $val) {
            $rows[$val['id']] = "{$val['company_name']} / （{$val['username']}）";
        }
    return empty($rows) ? [] : ['' => '输入企业名称/微叮号搜索'] + $rows;
    }


    public static function getCompanyUser()
    {
        $data = Company::find()->leftJoin('user AS u','u.id = company.user_id')
            ->select("company.id,company.name as company_name,u.username")
            ->where(['u.user_type'=>self::USER_TYPE_COMPANY,'company.audit_status'=>1])
            ->asArray()->all();
        foreach ($data as $val) {
            $rows[$val['id']] = "{$val['company_name']} / （{$val['username']}）";
        }
        return empty($rows) ? [] : ['' => '输入企业名称/微叮号搜索'] + $rows;
    }
}
