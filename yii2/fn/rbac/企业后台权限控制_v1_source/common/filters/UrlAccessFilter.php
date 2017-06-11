<?php
/**
 * Created by PhpStorm.
 * Author: HuangYeWuDeng
 * Date: 3/5/17
 * Time: 8:58 PM
 */

namespace common\filters;

use Yii;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;
use company\models\AuthAssign;
use yii\di\Instance;
use yii\caching\Cache;

class UrlAccessFilter extends ActionFilter
{
    public $db = 'db';

    public $cache;

    public $cacheKey = 'vding_rbac';

    /**
     * @var Item[] all auth items (name => Item)
     */
    protected $items;

    public $denyCallback;

    public $rules;

    /**
     * 需要登录但是不需要rbac控制的
     * @var array
     */
    public $noRbacController = [
        'site', // site/error default error handle action
        'debug',
        'util',
        'admin-notice',
        'company-notice',
        'alipay',

    ];

    /**
     * 不需要登录的
     * @var array
     */
    public $publicController= [
        'sign-in',
        'payment',
        'binding',
    ];

    public $publicModules = [
        'shop',
    ];

    public $noRbacModules = [
        'debug',
        'ucenter',
        'admin',
    ];

    public $noRbacAction = [
        'index/index',
        'company-account/check-condition',//检测提现条件是否满足  例如：实名认证，设置支付密码，綁卡
        'car/requisition/validate-form',
        'auth/load-auth-date',//异步加载权限数据
        'product-line/list',//线路管理--列表--view
        'product-line/preset-setting',//线路管理--预定设置--view
        'product-line/price-date',//线路管理--价格班期--view
        'product-line/delivery',//线路管理--分销商须知--view
        'product-line/detail',//线路管理--详情--view
        'product-line/to-do-list',
        'product-line/procurement',
        'product-line/order-list',//线路订单管理列表
        'product-line/order-detail',//线路订单详情

        'file-storage/upload-imperavi',//编辑器上传图片
        'product-line/product-route-view',//线路详情页面
        'product-line/product-trip-list',//行程列表
        'product-line/base-info',//基本信息--view
        'product-line/travel',//行程安排--view
        'line-order/order-list',//线路订单列表
        'index/admin-pwd-set',//首次登录企业设置管理密码
    ];

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $module_id = $action->controller->module->id;
        $url = $action->getUniqueId();
        $controller = $action->controller->id;
        $user = Yii::$app->user;

        //fix module layout
        if (in_array($module_id, ['sms', 'settings', 'lookup', 'log', 'key-storage', 'cache', 'file-manager', 'system-information'])) {
            Yii::$app->name = '平台管理后台';
            $action->controller->layout = '@company/modules/admin/views/layouts/main'; //your layout name
        }

        if (in_array($module_id, $this->publicModules)) {
            $action->controller->layout = 'main'; //your layout name
        }

        if (in_array($module_id, $this->publicModules)) {
            return true;
        }
        if (in_array($controller, $this->publicController)) {
            return true;
        }
        //登录检测
        if ($user->getIsGuest()) {
            $user->loginRequired();
            return false;
        }
        if (in_array($controller, $this->noRbacController)) {
            return true;
        }
        if (in_array($module_id, $this->noRbacModules)) {
            return true;
        }
        if (in_array($url, $this->noRbacAction)) {
            return true;
        }

        $userId = Yii::$app->user->getId();
        $compnayId = Yii::$app->user->getCompanyId();
        if ($compnayId) {
            if (Yii::$app->user->isCompanySuperUser()) {
                //超级管理员不检测
                return true;
            }
            $items = AuthAssign::findByUserId($userId, $compnayId);
            foreach ($items as $item) {
                foreach($item->authItem->children as $node) {
                    if ($node) {
                        if ($url == trim($node->url, '/')) {
                            return true;
                        }
                    }
                }
            }
        }
        //必须是 company_id = 1 的用户才能进这里
        if (in_array($module_id, ['admin', 'sms', 'lookup']) && $compnayId && Yii::$app->user->isAdminCompany()) {
            if (Yii::$app->user->isCompanySuperUser()) {
                //超级管理员不检测
                return true;
            }
            $items = AuthAssign::findByUserId($userId, 1);
            foreach ($items as $item) {
                foreach($item->authItem->children as $node) {
                    if ($node) {
                        if ($url == trim($node->url, '/')) {
                            return true;
                        }
                    }
                }
            }
        }
        if ($this->denyCallback !== null) {
            call_user_func($this->denyCallback, null, $action);
        } else {
            $this->denyAccess($url);
        }
        return false;
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($url)
    {
        $user = Yii::$app->user;
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}