<?php

/**
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/28/16
 * Time: 4:49 PM
 */

namespace rest\filters\auth;

use yii\filters\auth\AuthMethod;
use Yii;
use yii\web\HttpException;

/**
 * SecureTokenAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 * access-token 是rsa加密后的十六进制ASCII字符串
 * 这是原版auth认证的一个改进版，增加了rsa加密
 * @TODO 增加timestamp 和 singature
 * @author HuangYeWuDeng
 */
class SecureTokenAuthV2 extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';

    /**
     * Code 498 indicates an expired or otherwise invalid token
     * @inheritdoc
     */
    public function handleFailureTokenExpired($response, $identity)
    {
        $login_type = $identity->last_login_type == 1 ? '密码' : '短信';
        $login_fail_msg  = [
          1 => '你的密码可能已泄露，请尽快修改密码。',
          2 => '你的验证码可能已泄露。请勿转发验证码。',
        ];
        $message = sprintf('您的微叮账号于%s在另一个%s设备上通过%s登录,如果非本人操作,%s',
            date('Y-m-d H:i:s', $identity->logged_at), $identity->device_name, $login_type, $login_fail_msg[$identity->last_login_type]);
        throw new HttpException(498, $message);
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (empty($accessToken)) {
            $this->handleFailure($response);
        }
        $accessTokenBin = hex2bin(strtolower($accessToken));
        $accessTokenPlain = Yii::$app->rsa->privateDecrypt($accessTokenBin);
        if (is_string($accessTokenPlain)) {
            $identity = $user->loginByAccessToken($accessTokenPlain, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
            $identity = $user->loginByAccessTokenPrev($accessTokenPlain, get_class($this));
            if ($identity !== null) {
                $this->handleFailureTokenExpired($response, $identity);
            }
        }
        if ($accessTokenPlain !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
