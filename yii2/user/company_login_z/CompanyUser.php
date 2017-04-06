<?php

/**
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/20/16
 * Time: 11:13 AM
 */

namespace company\components;

use common\models\Company;
use yii\web\User;
use Yii;

class CompanyUser extends User
{
    private $_companyIdentity = false;

    public $companyIdParam = '__company_id';

    /**
     * 这个代码在父类已经有了,一模一样
     * @param string $token
     * @param null   $type
     * @author cmk
     * @return null
     */
    public function loginByAccessToken($token, $type = null)
    {
        /* @var $class IdentityInterface */
        $class = $this->identityClass;
        $identity = $class::findIdentityByAccessToken($token, $type);
        if ($identity && $this->login($identity)) {
            return $identity;
        } else {
            return null;
        }
    }

    /**
     *  登陆时将状态与手机号码保存入session
     * 前置登录操作,保存手机号,手机号登录时第一步不能确定用户名
     * 以用户名/手机号 + 密码 or  手机号 + 验证码
     */
    public function preLogin($mobile)
    {
        Yii::$app->session->set('prelogin_status', 1);
        Yii::$app->session->set('prelogin_mobile', $mobile);
    }

    /**
     * 清session
     * @author cmk
     */
    public function cleanPreLogin()
    {
        foreach (['prelogin_status', 'prelogin_mobile'] as $key) {
            Yii::$app->session->remove($key);
        }
    }

    /** 登陆之前是否使用密码,根据之前的状态
     * @author cmk
     * @return bool
     */
    public function isPreLoginPassed()
    {
        $preLoginSt = Yii::$app->session->get('prelogin_status');
        return $preLoginSt == 1;
    }

    /**
     * 通过session来获取登陆时手机号码
     * @author cmk
     * @return mixed
     */
    public function getPreLoginMobile()
    {
        return Yii::$app->session->get('prelogin_mobile');
    }

    /** 判断是否为公司用户
     * @author cmk
     * @return bool
     */
    public function getIsCompanyUser()
    {
        return $this->getCompanyId() > 0;
    }

    /**
     * 是否个人用户 user_type == 2
     * @author HuangYeWuDeng
     * @return bool
     */
    public function getIsPerson()
    {
        return Yii::$app->getSession()->get('user_type') == 2;
    }

    /**
     * 判断为公司登陆,将公司ID保存起来,对该company赋值
     * @param $companyIdentity
     * @author cmk
     * @return bool
     */
    public function companyLogin($companyIdentity)
    {
        $session = Yii::$app->getSession();
        $session->set($this->companyIdParam, $companyIdentity->getId());
        $this->_companyIdentity = $companyIdentity;
        return true;
    }

    /**
     *选择不同的公司进入,将该公司ID更新到session
     * @param $companyId
     * @author cmk
     */
    public function switchCompany($companyId)
    {
        $session = Yii::$app->getSession();
        $session->set($this->companyIdParam, $companyId);
    }

    /**
     * 获取公司的ID
     * @author cmk
     * @return mixed
     */
    public function getCompanyId()
    {
        $session = Yii::$app->getSession();
        return $session->get($this->companyIdParam);
    }

    /** 获取公司的记录 [company]
     * @author cmk
     * @return static
     */
    public function getCompany()
    {
        $company = Company::findOne($this->getCompanyId());
        return $company;
    }

    /**
     * 判断公司id 为1 的就是vding, 可管理 admin
     * @author HuangYeWuDeng
     * @return bool
     */
    public function isAdminCompany()
    {
        return 1 == $this->getCompanyId();
    }

    /** 判断user.id 是否与 company.user_id 相同,一样为超级用户,否则就不是
     * @author cmk
     * @return bool
     */
    public function isCompanySuperUser()
    {
        return $this->getId() == $this->getCompany()->user_id;
    }

    /**
     * 退出
     * @param bool $destroySession
     * @author cmk
     */
    public function logout($destroySession = true)
    {
        parent::logout($destroySession);
        foreach ([$this->companyIdParam, 'prelogin_status', 'prelogin_mobile'] as $key) {
            Yii::$app->session->remove($key);
        }
    }
}