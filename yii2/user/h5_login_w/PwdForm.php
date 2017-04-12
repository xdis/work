<?php
namespace company\modules\shop\models;

use common\models\User;

use company\modules\shop\models\LoginForm;

use common\validators\PhoneValidator;
use yii\base\InvalidParamException;

class PwdForm extends LoginForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password', 'user_id','company_id'], 'required'],
            ['password', 'validatePassword'],
            //['username', 'validateUsername'],
        ];
    }
    
    public function validatePassword()
    {
    	$user = $this->getUser();
    	$flag = true;
    	if ($user) {
    		try {
    			$flag = $user->validatePassword($this->password);
    		} catch (InvalidParamException $e) {
    			$flag = false;
    		}
    	}
    	if(!$user || !$flag){
    		$this->addError('password', '用户或密码错误!');
    	}
    }
    
    /**
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
        	$this->user = User::find()->andWhere(['user_type'=>User::USER_TYPE_PERSON])
        							->andWhere(['or', ['username'=>$this->username], ['mobile'=>$this->username]])->one();
        }
        return $this->user;
    }
}
