<?php
/**
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/13/16
 * Time: 10:14 AM
 * API接口基类控制器
 * 状态码参考： http://mp.weixin.qq.com/wiki/17/fa4e1434e57290788bde25603fa2fcbd.html
 */

namespace common\controllers;
use yii\rest\ActiveController;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\web\ForbiddenHttpException;
use rest\filters\auth\SecureTokenAuthV2;

class BaseRestController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => SecureTokenAuthV2::className(),
            //注意，这个只能限制到action,不能指定controller
            'except' => ['login', 'login-test', 'register', 'get-sms', 'get-register-sms', 'end-user-license','forget-pass'],
        ];
        return $behaviors;
    }
}