<?php
/**
 * Created by PhpStorm.
 * Author: HuangYeWuDeng
 * Date: 3/15/17
 * Time: 9:30 PM
 */

namespace rest\components;

use yii\web\User;

class WebUser extends User
{
    /**
     * 根据上一次的token查找用户，如果找到证明此用户已经被在新设备登录
     * @param $token
     * @param null $type
     * @author HuangYeWuDeng
     * @return IdentityInterface|null the identity associated with the given access token. Null is returned if
     * the access token is invalid or [[login()]] is unsuccessful.
     */
    public function loginByAccessTokenPrev($token, $type = null)
    {
        if (empty($token)) {
            return null;
        }
        /* @var $class IdentityInterface */
        $class = $this->identityClass;
        $identity = $class::findIdentityByAccessTokenPrev($token, $type);
        if ($identity && $this->login($identity)) {
            return $identity;
        } else {
            return null;
        }
    }
}