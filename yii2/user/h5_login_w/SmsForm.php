<?php
namespace company\modules\shop\models;

use common\models\User;
use ihacklog\sms\models\Sms;
use company\modules\shop\models\LoginForm;
use Yii;
use common\validators\PhoneValidator;

class SmsForm extends LoginForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['mobile', 'required'],
            [['sms_verify_code', 'user_id', 'company_id'], 'required', 'on' => 'sms_login'],
            ['mobile', PhoneValidator::className()],//common\validators\PhoneValidator
            ['sms_verify_code','string','max' => 4,'min'=>4, 'on' => 'sms_login'],
            ['sms_verify_code', 'validateVeifyCode', 'on' => 'sms_login'],
            ['mobile', 'validateUser', 'on' => 'sms_login'],
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
    }
    /**
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::find()->where(['mobile'=> $this->mobile,'user_type'=>User::USER_TYPE_PERSON])->one();
        }

        return $this->user;
    }
}
