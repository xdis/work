<?php
namespace company\modules\shop\models;

use common\models\User;
use common\models\Store;

use Yii;
use yii\base\Model;
use common\models\DpStore;
use common\models\UserCompany;
use common\models\OneCardPass;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $user_id = 0;
    public $company_id = 0;
    public $username = '';
    public $mobile	 = '';
    public $sms_verify_code;
    public $password;
    
    public $is_passed_card = 0;

    protected $user = false;

	public function validateUser($attribute){
		if($this->getUser() && $this->user->status != User::STATUS_ACTIVE){
			$this->addError($attribute, '账户已被冻结');
		}
	}

    public function scenarios()
    {
        return [
            'pwd_login' => ['username', 'password', 'user_id', 'company_id'],
            'sms_login_pre' => ['mobile'],
            'sms_login' => ['mobile', 'sms_verify_code', 'company_id', 'user_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => '手机号码',
            'sms_verify_code' => '短信验证码',
            'username' => '用户名',
            'password' => '密码',
            'company_id' => '公司ID',
            'user_id' => '店主ID',
        ];
    }
    
    /**
     * 登录
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     * @throws ForbiddenHttpException
     */
    public function doLogin()
    {
        $user = is_null($this->user) ? $this->doReg() : $this->user;
        if (Yii::$app->shopUser->login($user)) {
			Yii::$app->getSession()->set('mobile', $user->mobile);
        	Yii::$app->getSession()->set('is_shopkeeper', 0);

       	if ($user->id == $this->user_id){
       	    //判断当前店铺是不是店主
		        $store = UserCompany::find()->where(['company_id' => $this->company_id, 'user_id'=>$user->id, 'is_opened_store'=>1])->asArray()->one();
				if ($store){
		        	Yii::$app->getSession()->set('is_shopkeeper', $store['id']);
		        	Yii::$app->getSession()->set('store_company_id', $this->company_id);
		        	Yii::$app->getSession()->set('store_owner_id', $user->id);
		        }
      	}else{
            //看看该用户是不是店主，不限于当前公司
            $query = UserCompany::find();
            $store = $query->where(['user_id'=>$user->id, 'is_opened_store'=>1,'is_deleted'=>0])->asArray()->one();
            //dp($store);
            if ($store){
                Yii::$app->getSession()->set('is_shopkeeper', $store['id']);
                Yii::$app->getSession()->set('store_company_id', $store['company_id']);
                Yii::$app->getSession()->set('store_owner_id',$store['user_id']);
            }
        }

        	//获取一卡通
        	if (OneCardPass::find()->where([
        			'applicant_id'=>$user->id,
        			'audit_status'=>1, 
        			'send_status'=>1, 
        			'is_deleted'=>0])->one())
        	{
        		$this->is_passed_card = 1;
        	}else{
        		$this->is_passed_card = 0;
        	}

        	return true;
        }
        return false;
    }
    private function doReg(){
    	$user = new User();
    	$user->username = $this->vdingNo();
    	$user->mobile 	= $this->mobile;
    	$user->status 	= User::STATUS_ACTIVE;
    	$user->user_type= User::USER_TYPE_PERSON;
    	
    	if (!$user->addUser()) {
    		throw new \Exception("User couldn't be  saved");
    	};
    	$user->afterSignup();
    	$this->user = $user;
    	return $user;
    }
    private function vdingNo(){
    	$randNo = 'yk'.rand(1000000000, 9999999999);
    	if (User::find()->where(['username' => $randNo])->one()){
    		$this->vdingNo();
    	}
    	return $randNo;
    }
}
