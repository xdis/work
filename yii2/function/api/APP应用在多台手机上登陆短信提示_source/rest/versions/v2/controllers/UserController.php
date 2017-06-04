<?php
namespace rest\versions\v2\controllers;

use rest\versions\v1\controllers\UserController as V1UserController;
use rest\versions\v2\models\LoginForm;
use rest\versions\v1\models\SignupForm;
use common\controllers\BaseRestController;
use Yii;
use yii\rest\Controller;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\web\BadRequestHttpException;
use rest\actions\GetSmsAction;
use yii\filters\VerbFilter;

class UserController extends V1UserController
{
    //设备型号
    public $device_model;

    /**
     * v2 版的getTheAccessToken
     * @param bool $debug
     * @author HuangYeWuDeng
     * @return array
     */
    protected function getTheAccessToken($debug = false)
    {
        //生成新的token
        $newToken = \Yii::$app->getSecurity()->generateRandomString(40);
        //保存上一次的token
        $token = \Yii::$app->user->identity->getAccessToken();
        \Yii::$app->user->identity->access_token_prev = $token;ee
        \Yii::$app->user->identity->access_token .                        z    = $newToken;
        \Yii::$app->user->identity->save();

        $accessTokenEnc = Yii::$app->rsa->privateEncrypt($newToken);
        if (!$debug) {
            return ['token' => strtoupper(bin2hex($accessTokenEnc)), 'id' => \Yii::$app->user->identity->id];
        }
        $accessTokenClient = Yii::$app->rsa->publicEncrypt($newToken);
        return [
            'token' => strtoupper(bin2hex($accessTokenEnc)),
            'token_client' => strtoupper(bin2hex($accessTokenClient)),
            'token_plain' => $token,
            'id' => \Yii::$app->user->identity->id
        ];
    }

    /**
     * 登录接口 v2 登录时校验唯一设备登录
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
}
