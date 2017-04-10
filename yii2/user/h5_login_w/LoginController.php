<?php

namespace company\modules\shop\controllers;

use company\modules\shop\models\SmsForm;
use company\modules\shop\models\PwdForm;

use common\actions\GetSmsAction;

use common\models\User;
use Yii;

use company\modules\shop\controllers\DpBaseController;

class LoginController extends DpBaseController {

    public function actions() {
        return [
            'smscode' => [
                'class' => GetSmsAction::className(),
                'beforeCallback' => [$this, 'smsBeforeCallback'],
                'initCallback' => [$this, 'smsInitCallback'],
            ],
        ];
    }

    public function smsInitCallback($action) {
        $mobile = Yii::$app->getRequest()->post('mobile');
        $action->mobile = $mobile;
    }

    public function smsBeforeCallback($action) {
        if (\Yii::$app->shopUser->isGuest) {
            $model = new SmsForm();
            $model->setScenario('sms_login_pre');
            $model->mobile = Yii::$app->getRequest()->post('mobile');

            if ($model->validate()) {
                return true;
            }
            $action->error = current($model->getErrors())[0];
        } else {
            $action->error = '用户已登录';
        }
        return false;
    }

    /**
     * 短信验证码登陆
     * @return multitype:unknown string |Ambigous <string, string>
     */
    public function actionSms() {
        if (!Yii::$app->shopUser->isGuest) {
            return $this->ajaxSuccess('已经登录');
        }
        $model = new SmsForm();
        $model->setScenario('sms_login');
        $model->company_id = $this->store_company_id;
        $model->user_id = $this->store_owner_id;
        $model->mobile = Yii::$app->getRequest()->post('mobile');
        $model->sms_verify_code = Yii::$app->getRequest()->post('sms_verify_code');

        if ($model->validate() && $model->doLogin()) {
            return $this->ajaxSuccess('登录成功', '', [
                'is_shopkeeper' => \Yii::$app->getSession()->get('is_shopkeeper'),
                'is_passed_card' => $model->is_passed_card,
                'store_company_id' => Yii::$app->getSession()->get('store_company_id'),
                'store_owner_id' => Yii::$app->getSession()->get('store_owner_id'),
            ]);
        } else {
            $error = $model->getErrors();
            return $this->ajaxFail('登录失败 ' . current($error)[0]);
        }
    }

    /**
     * 密码登陆
     * @return multitype:unknown string
     */
    public function actionPwd() {
        if (!Yii::$app->shopUser->isGuest) {
            return $this->ajaxSuccess('已经登录');
        }
        $model = new PwdForm();
        $model->setScenario('pwd_login');
        $model->company_id = $this->store_company_id;
        $model->user_id = $this->store_owner_id;
        $model->username = Yii::$app->getRequest()->post('username');
        $model->password = Yii::$app->getRequest()->post('password');


        if ($model->validate() && $model->doLogin()) {
            return $this->ajaxSuccess('登录成功', '', [
                'is_shopkeeper' => \Yii::$app->getSession()->get('is_shopkeeper'),
                'is_passed_card' => $model->is_passed_card,
                'store_company_id' => Yii::$app->getSession()->get('store_company_id'),
                'store_owner_id' => Yii::$app->getSession()->get('store_owner_id'),
            ]);
        } else {
            $error = $model->getErrors();
            return $this->ajaxFail('登录失败 ' . current($error)[0]);
        }
    }

    public function actionCheck() {
        if (!Yii::$app->shopUser->isGuest) {
            return $this->ajaxSuccess('已经登录', '', ['is_shopkeeper' => \Yii::$app->getSession()->get('is_shopkeeper')]);
        } else {
            return $this->ajaxFail('未登录或登录超时 ');
        }
    }

    public function actionLogout() {
        Yii::$app->shopUser->logout();
        return $this->ajaxSuccess('退出成功');
    }
}
