<?php
/**
 * @author: CodeLighter
 * @version: 2.0
 * @Date: 2017/5/24 0024
 */
namespace company\models;

use Yii;
use yii\base\Model;
use company\models\AuthItem;
use company\models\UserCompany;
use common\models\Company;
use common\models\CompanyApp;
use company\components\CompanyUser;
use common\helpers\TreeHelper;
use yii\data\ActiveDataProvider;
use company\models\search\AuthItem as AuthItemSearch;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use company\models\AuthAssign;
use yii\data\Pagination;
use yii\db\Expression;
use yii\web\ForbiddenHttpException;
use yii\helpers\Url;


class RoleManageForm extends Model
{
    const S_ROLE_LIST = "S_ROLE_LIST";
    const S_ROLE_DETAIL = "S_ROLE_DETAIL";
    const S_ROLE_DELETE = "S_ROLE_DELETE";
    const APP_ID_BASE = 8;
    const APP_ID_DIJIE = 1;
    const APP_ID_DRIVER = 3;
    const CACHE_KEY_APP_BASE = "__APP_BASE_MENU"; //基础模块菜单缓存
    const CACHE_KEY_APP_DIJIE = "__APP_DI_JIE_MENU"; //地接应用模块菜单缓存
    const CACHE_KEY_APP_AGENT = "__APP_AGENT_MENU"; //代理商应用模块菜单缓存
    const CACHE_KEY_APP_DRIVER = "__APP_DRIVER_MENU"; //车调应用模块菜单缓存
    const CACHE_KEY_APP_PURCHASE = "__APP_PURCHASE_MENU";//批发商应用模块菜单缓存
    //将顶级菜单包含的所有权限进行一一映射，在已知某个权限的情况下，快速判断属于哪个顶级菜单
    const CACHE_KEY_PERMISSION_MENU_COMPANY = "__base_menu_company_permission";//企业管理
    const CACHE_KEY_PERMISSION_MENU_ACCOUNT = "__base_menu_account_permission";//企业账户
    const CACHE_KEY_PERMISSION_DIJIE = "__dijie_app_permission"; //地接应用ID
    const CACHE_KEY_PERMISSION_DRIVER = "__driver_app_permission"; //车调应用ID
    const CACHE_KEY_PERMISSION_BASE = "__base_app_permission"; //基础应用ID

    public $company_id;
    public $role_name;
    public $page_index;
    public $page_size;

    public function scenarios()
    {
        $s = parent::scenarios();
        $s[self::S_ROLE_LIST] = ['company_id','role_name','page_index','page_size'];
    }
    public function rules()
    {
        return [
            [['company_id', 'role_name', 'page_index', 'page_size'],
                'required', 'on' => self::S_ROLE_LIST],
            [['company_id', 'role_name', 'page_index', 'page_size'], 'filter',
                'filter' => 'trim', 'on' => self::S_ROLE_LIST]
        ];
    }

    //从session中获取company_id
    public function getCompanyId(){
        return Yii::$app->user->getCompanyId();
    }
    //从session中获取user_id
    public function getUserId(){
        return Yii::$app->user->id;
    }
    //判断当前用户是否是超级管理员
    public function isSuperUser(){
        return Yii::$app->user->isCompanySuperUser();
    }

    //车调应用是3, 地接应用是1, 基础模块是8,批发商应用是11
    public function getCompanyAppId($company_id)
    {
        $company_app = CompanyApp::find()
            ->select(['id','industry_app_id'])
            ->where(['company_id'=>$company_id,'audit_status'=>2])
            ->all();
        $app_id = $company_app?ArrayHelper::getColumn($company_app,'industry_app_id'):[];
        $app_id = array_unique($app_id);
        return $app_id;
    }
    //获取每种应用拥有的所有权限ID
    public function getCacheAppMenuId($app_id)
    {
        $cache_key = '';
        if($app_id==self::APP_ID_DIJIE){
            $cache_key = self::CACHE_KEY_PERMISSION_DIJIE;
        }else if($app_id == self::APP_ID_DRIVER){
            $cache_key = self::CACHE_KEY_PERMISSION_DRIVER;
        }else if($app_id == self::APP_ID_BASE){
            $cache_key = self::CACHE_KEY_PERMISSION_BASE;
        }
        if($cache_key !=''){
            $cache = Yii::$app->menu_cache;
            $ids = false; //$cache->get($cache_key);
            if($ids== false) {
                $data = AuthItem::find()->select('id')
                    ->where(['status' => 1])
                    ->andWhere(['in','type',[4]])
                    ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                    ->andWhere(['in', 'industry_appid', $app_id])//得到应用的权限节点
                    ->orderBy('parent_id,sort')->asArray()->all();
                $ids = ArrayHelper::getColumn($data,'id');
                $cache->set($cache_key, $ids);
            }
            return $ids;
        }
        return [];
    }
    //权限验证
    //获取地接应用的白名单
    public function urlWhiteList(){
        $list = [
            '/dijie/common/config', '/dijie/common/supplier',
            '/dijie/common/customer','/dijie/common/line',
            '/dijie/common/company-user','/dijie/common/get-scenic',
            '/dijie/common/staff','/dijie/common/department',
            '/dijie/common/file-storage','/dijie/dj-group-list/group-list',
            '/dijie/dj-group-list/group-guide','/dijie/order/index',
            '/dijie/dj-resource/show-resource',

            '/ucenter/person-account/fund-flow','/ucenter/person-account/fund-flow',
            '/ucenter/person-account/detail','/ucenter/person-account/fund-flow',
            '/ucenter/person-account/recharge','/ucenter/person-account/withdraw',
            '/ucenter/person-account/do-withdraw-check','/ucenter/person-account/do-withdraw',
            //地接应用白名单
            /*'/product-line/product-route-view',
            '/product-line/product-trip-list',
            '/product-line/file-storage',
            '/product-price/get-pricelist',
            '/product-price/get-oppoint-date-pricelist',
            '/product-line/base-info',
            '/product-line/travel-arrange',
            '/product-line/attention-matters',
            '/product-line/basic-info',
            '/product-line/edit-basic-info',
            '/product-line/get-basic-info',
            '/product-line/trip',
            '/product-line/get-trip',
            '/product-line/notice-list',
            '/product-line/get-notice-list',
            '/product-line/index',
            '/product-line/price-date',
            '/purchase-confirm/index',
            '/product-price/get-base-info-price',
            '/product-price/get-depart-city',
            '/product-price/get-base-price-list',
            '/product-price/get-pricelist',
            '/product-price/get-product-model-list',
            '/line-order-detail/load-order-detail',
            '/line-order-detail/load-order-tourist',
            '/line-order-detail/load-order-operation',
            '/product-line-order/refund-detail',
            '/line-order/order-list',*/
        ];
        return $list;
    }
    /*
     * 基础应用白名单，如果用户已经登录了，请求下面的URL，将通过验证
     * 否则弹出
     */
    public function baseWhiteList(){
        return ['/role-manage/role-menu','/index/switch-company',
            '/index/company-list','/supplier/select-company',
            '/customer/select-company','/staff-manage/get-roles',
            '/department/get-department','/staff-manage/get-department',
            '/contacts/save','/supplier/sign-log','/customer/sign-log',
            '/supplier/operation-log','/customer/operation-log',
            '/role-manage/auth-list','/staff-manage/search-staff',
            '/security/get-editpay-sms','/bank-card/is-bind',
            '/aptitude/company-apps','/company-account/recharge-order-check',
            '/bank-card/isvaildate','/contacts/contacts-list',
            '/company/basic-info', '/product-line/product-route-view',
            '/product-line/product-trip-list',
            '/product-line/file-storage',
            '/product-price/get-pricelist',
            '/product-price/get-oppoint-date-pricelist',
            '/product-line/base-info',
            '/product-line/travel-arrange',
            '/product-line/attention-matters',
            '/product-line/basic-info',
            '/product-line/edit-basic-info',
            '/product-line/get-basic-info',
            '/product-line/trip',
            '/product-line/get-trip',
            '/product-line/notice-list',
            '/product-line/get-notice-list',
            '/product-line/index',
            '/product-line/price-date',
            '/purchase-confirm/index',
            '/product-price/get-base-info-price',
            '/product-price/get-depart-city',
            '/product-price/get-base-price-list',
            '/product-price/get-pricelist',
            '/product-price/get-product-model-list',
            '/product-price/get-base-price-edit',
            '/product-price/del-depart-city',
            '/line-order-detail/load-order-detail',
            '/line-order-detail/load-order-tourist',
            '/line-order-detail/load-order-operation',
            '/product-line-order/refund-detail',
            '/line-order/order-list',
            '/product-price/set-base-info-price',
            '/product-price/get-base-price-add',
            '/product-price/get-special-price-edit-all',
            '/product-price/get-special-price-detail',
            '/product-price/set-depart-city',
        ];
    }
    public function equalPermissionList()
    {
        return ['/staff-manage/view'=>'/staff-manage/list',
                '/role-manage/view'=>'/role-manage/list',
                '/department/view'=>'/department/list',
                '/supplier/auditing'=>'/supplier/application',
                '/customer/auditing'=>'/customer/application',
                '/customer/view'=>'/customer/index',
                '/supplier/view'=>'/supplier/index',
                '/company-account/fund-flow'=>'/company-account/index',
                '/company-account/detail'=>'/company-account/index',
                '/company-account/do-withdraw'=>'/company-account/withdraw',
                '/company-account/do-withdraw-check'=>'/company-account/withdraw',
                '/dj-group-list/view'=>'/dijie/dj-group-list/index',
                '/line-order-detail/load-order-detail'=>'/line-order/order-list',
                ];
    }
    public function controllerWhiteList(){
        return [];
    }
    public function forbiddenAccess(){
        if(Yii::$app->request->isPost) {
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
            Yii::$app->response->data = ['status' => 403, 'message' => '你没有此操作权限', 'url' => '', 'data' => ''];
        }else{
            Yii::$app->response->redirect('/error/index');
            //throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
    public function getAllowedUrl(){
        $company_id = $this->getCompanyId();
        $user_id = $this->getUserId();
        $role_id = $this->getUserRoles($company_id,$user_id);
        $permissions = $this->getRolePermissions($role_id);
        $permission_id = ArrayHelper::getColumn($permissions,'child');
        $self = AuthItem::find()->where(['in','id',$permission_id])->asArray()->all();
        $parent = (new \yii\db\Query())->select('A.*')
            ->from('auth_item A')
            ->innerJoin('auth_item B','A.id=B.parent_id')
            ->where(['in','B.id',$permission_id])
            ->all();
        $child = AuthItem::find()->where(['in','parent_id',$permission_id])
            ->asArray()->all();
        $data = array_merge($parent,$self,$child);
        $url = array_unique(ArrayHelper::getColumn($data,'url'));
        return $url;
    }
    public function checkAccess($module="company",$controller,$action){

        if($this->isSuperUser()){
            return true;
        }
        if($module=='ucenter'){
            return true;
        }
        $url = '/'.$module.'/'.$controller.'/'.$action;
        $url_no_slash = $module.'/'.$controller.'/'.$action;
        if($module=='company'){
            $url = '/'.$controller.'/'.$action;
            if(in_array($url,$this->baseWhiteList())){
                return true;
            }
        }else{
            if(in_array($url,$this->urlWhiteList()) ||in_array($url_no_slash,$this->urlWhiteList())){
                return true;
            }
        }
        //查询我的所有权限
        $allowed_url = $this->getAllowedUrl();
        if(in_array($url,$allowed_url)||in_array($url_no_slash,$allowed_url)){
            return true;
        }
        $equal_list = $this->equalPermissionList();
        foreach($equal_list as $k=>$v){
            if($k==$url){
                $url = $v;
                break;
            }
        }
        if(in_array($url,$allowed_url)){
            return true;
        }
        $params = Yii::$app->request->getQueryString();
        $old_url = $url;
        if($params) {
            $url = $url . '?' . $params;
            if (in_array($url, $allowed_url)) {
                return true;
            }
        }
        //判断地接公用的接口是否分配了权限
        foreach($allowed_url as $k=>$v){
            $test = preg_replace('/\?\w+=\d+/','',$v);
            if($test == $old_url){
                return true;
            }
        }
        //判断资源的接口是否分配了权限
        // /dijie/dj-resource/create
        if(Yii::$app->request->isPost) {
            if ($url == '/dijie/dj-resource/create'){
                return true;
            }
        }
        //判断资源的接口是否分配了权限
        // /dijie/dj-resource/create
        $this->forbiddenAccess();
        return false;
    }
    //根据角色权限动态加载菜单
    public function getRoleMenu(){
        $company_id = $this->getCompanyId();
        $user_id    = $this->getUserId();
        $is_super_user = $this->isSuperUser();
        $menuCallback = __NAMESPACE__.'\RoleManageForm::filterMenuCallback';

        //查询公司审核通过的应用
        $app_id = $this->getCompanyAppId($company_id);
        //如果不是超级管理员，获取授权的菜单 第一步，查询用户拥有的所有角色
        $role_id = $this->getUserRoles($company_id,$user_id);
        $cache = Yii::$app->menu_cache;
        //如果是超级管理员，获取基础模块的所有菜单，如果有应用，还要获取应用的菜单
        //没有赋予角色的员工只拥有首页的权限
        if($is_super_user) {
            $base_items = $this->getAppMenu(8);
            $base_menu = false; //$cache->get(self::CACHE_KEY_APP_BASE);
            if($base_menu == false) {
                $base_menu = self::buildTree($base_items, $menuCallback);
                $cache->set(self::CACHE_KEY_APP_BASE,$base_menu);
            }
            $app_menu = $this->buildAppMenu($app_id,$menuCallback);
            foreach($base_menu as $k=>$v){
                if($v["text"]=='企业应用'){
                    $base_menu[$k]["children"] = $app_menu;
                    break;
                }
            }
            $base_menu[]= $this->getMenuMyAccount();
            return $base_menu;
        }
        //根据角色，获取员工赋予的角色和权限
        $permissions = $this->getRolePermissions($role_id);
        $app_ids = array_merge([self::APP_ID_BASE],$app_id);
        //获取该公司超级管理员所拥有的菜单和权限
        $all_items = $this->getAppMenuAndPermission($app_ids);
        //根据权限进行过滤
        $all_menu = $this->getTreePath($all_items,$permissions);
        $all_menu_hash = ArrayHelper::index($all_menu,'id');
        $all_menu_id = ArrayHelper::getColumn($all_menu,'id');
        $menu_base = $this->getPermissionMenu($all_menu_hash,$all_menu_id,self::APP_ID_BASE);
        $menu_dijie = $this->getPermissionMenu($all_menu_hash,$all_menu_id,self::APP_ID_DIJIE);
        $menu_driver = $this->getPermissionMenu($all_menu_hash,$all_menu_id,self::APP_ID_DRIVER);

        $tree_base = self::buildTree($menu_base,$menuCallback);
        $tree_dijie = self::buildTree($menu_dijie,$menuCallback);
        $tree_driver = self::buildTree($menu_driver,$menuCallback);
        $app ='';
        if(count($menu_dijie)||count($menu_driver)) {
            $app = $this->emptyApp('企业应用');
        }
        if(count($menu_dijie)){
            $app1 = $this->emptyApp('地接应用');
            $app1['children'] = $tree_dijie;
            $app['children'][] = $app1;
        }
        if(count($menu_driver)){
            $app2 = $this->emptyApp('车调应用');
            $app2['children'] = $tree_driver;
            $app['children'][] = $app2;
        }
        if($app!='') {
            $tree_base[] = $app;
        }
        $tree_base[]= $this->getMenuMyAccount();
        return $tree_base;
    }

    /*
     * @API 如果用户跳转到ucenter的时候，顶部菜单和左侧菜单需要调整
     * @ 前端菜单组件每个页面共用，不能写死菜单链接，交由后端处理
     */
    public function getMenuMyAccount(){
        return [
            'id'=>'0','text'=>'我的账户','url'=>'','visible'=>"1","children"=>[
              0=>['id'=>'0','text'=>'资金管理','url'=>'/ucenter/person-account/index','visible'=>"1"],
              1=>['id'=>'0','text'=>'安全设置','url'=>'/ucenter/default/safe-set','visible'=>"1"],
              2=>['id'=>'0','text'=>'账户资料','url'=>'/ucenter/default/account','visible'=>"1"],
            ]
        ];
    }

    /*
     * 获取缓存的每个应用的全部菜单，不过因为测试的时候改动频繁，
     * 缓存目前没有启用，直接查询的数据库
     */
    public function getPermissionMenu(&$all_menu_hash,&$all_menu_id,$app_id)
    {
        $cache_app_id = $this->getCacheAppMenuId($app_id);
        $ret_app_id = array_intersect($cache_app_id,$all_menu_id);
        $menu = [];
        foreach($ret_app_id as $v){
            $menu[] = $all_menu_hash[$v];
        }
        return $menu;
    }
    /*
     * @param $keyword 关键词 根据角色名称搜索角色
     * @param $pageOffset 偏移量
     * @param $pageSize 每页显示的大小
     * @notice 请注意每个应用可能包含内置角色，需要一并返回给前端
     */
    public function roleList($keyword,$pageOffset,$pageSize)
    {
        $company_id = $this->getCompanyId();
        //如果有应用，就需要加载对应的内置角色
        $default_role = false;
        $app_id = $this->getCompanyAppId($company_id);
        foreach($app_id as $k=>$v){
            if($v==1){ //去除地接应用的导游角色
                unset($app_id[$k]);
            }
        }
        if(count($app_id)>0){
            $default_role = true;
        }
        $expr1 = new Expression('id,name,description,0 AS is_system');
        $sqlCompany = (new yii\db\Query())->select($expr1)
            ->from('auth_item')
            ->where(['type'=>1]) //type =1 表示角色
            ->andWhere(['company_id'=>$company_id]);
        $expr2 = new Expression('id,name,description,1 AS is_system');
        $sqlSystem = (new yii\db\Query())->select($expr2)
            ->from('auth_item')
            ->where(['type'=>1]) //type =1 表示角色
            ->andWhere(['company_id'=>0])
            ->andWhere(['in','type',$app_id]);
        if($keyword!=''){
            $sqlCompany->andFilterWhere(['like','name',$keyword]);
            $sqlSystem->andFilterWhere(['like','name',$keyword]);
        }
        if($default_role){
            $query = $sqlSystem->union($sqlCompany);
        }else{
            $query = $sqlCompany;
        }

        $total_page = $query->count();
        $list = $query->offset($pageOffset)->limit($pageSize)->all();
        return ['total_page'=>$total_page,'list'=>$list];
    }
    /*
     * @param $roleId array 角色ID数组
     * @notice 请注意deleteAll返回的是删除的角色条目数，如果条目数为0，deleteAll返回0
     */
    public function roleDelete($roleId)
    {
        $company_id = $this->getCompanyId();
        $data = AuthItem::findOne(['id'=>$roleId,'company_id'=>$company_id,'type'=>1]);
        if($data==null){
            throw new \Exception('角色名称不存在!');
        }
        $r = $data->delete(); //删除auth_item表中的角色
        $r = $r && AuthItemChild::deleteAll(['parent' => $roleId]);//删除auth_item_child表中角色关联的权限
        return $r;
    }
    /*
     * @param $src 用户拥有的所有权限(查询auth_item_child所得)，$menu_items 所有的菜单数据
     * @通过比对可以知道用户有用的所有菜单
     * @返回的是菜单的ID数组
     */
    public function getTreePath(&$src,&$menu_items)
    {
        $hash = ArrayHelper::index($src,'id');
        $menu_id = [];
        foreach($menu_items as $k=>$v){
            $path_id = $this->getTreeNodePath($hash,$v['child']);
            if(is_array($path_id)) {
                $path_id[] = $v['child'];
            }
            $menu_id = array_merge($menu_id,$path_id);
        }
        $unique = array_unique($menu_id);
        $menu = [];
        foreach($unique as $k2=>$v2){
            $menu[] = $hash[$v2];
        }
        return $menu;
    }

    /*
     * 只要权限和菜单的parent_id不为0,
     * 一直回溯到顶级菜单，并获取回溯路径的所有ID
     */
    public function getTreeNodePath(&$src,$id)
    {
        $path = [];
        if($src[$id]['parent_id']!=0){
            $parent_id = $src[$id]['parent_id'];
            $path[] = $parent_id;
            $path = array_merge($this->getTreeNodePath($src,$parent_id),$path);
        }
        return $path;
    }
    //获取基础项目应用菜单
    /*
     * 因为用户的权限不一样，顶部菜单的"企业管理"和"企业账户"需要
     * 根据权限动态改变URL，而且只取第一个权限的菜单URL
     */
    public function getBaseAppLink()
    {
        $company_id = $user_id =0;
        $is_super_user = false;
        try {
            $company_id = $this->getCompanyId();
            $user_id = $this->getUserId();
            $is_super_user = $this->isSuperUser();
        }catch(\Exception $e){
            return null;
        }
        if($is_super_user) {
            return ['staff_manage' => '/staff-manage/list', 'company' => '/company/index'];
        }
        //如果不是超级管理员，获取授权的菜单 第一步，查询用户拥有的所有角色
        $role_id = $this->getUserRoles($company_id,$user_id);
        $items = $this->getRolePermissions($role_id);
        $permission_id = ArrayHelper::getColumn($items,'child');
        $menu_company = $this->getMenuPermissionsMap(self::CACHE_KEY_PERMISSION_MENU_COMPANY,'企业管理',8);
        $menu_account = $this->getMenuPermissionsMap(self::CACHE_KEY_PERMISSION_MENU_ACCOUNT,'企业账户',8);
        $ret1 = array_intersect($menu_company,$permission_id);
        $ret2 = array_intersect($menu_account,$permission_id);
        //根据权限分配数据
        $menu1 = $menu2=null;
        if(count($ret1)) {
            $menu1 = (new \yii\db\Query())->select('A.*')
                ->from('auth_item A')
                ->innerJoin('auth_item B', 'A.id=B.parent_id')
                ->where(['in', 'B.id', $ret1])
                ->one();
        }
        if(count($ret2)){
            $menu2 = (new \yii\db\Query())->select('A.*')
            ->from('auth_item A')
            ->innerJoin('auth_item B','A.id=B.parent_id')
            ->where(['in','B.id',$ret2])
            ->one();
        }
        return ['staff_manage'=>is_null($menu1)?null:$menu1['url'],
                'company'=>is_null($menu2)?null:$menu2['url']];
    }
    /*
     * 如果用户申请通过了
     * 1 -- 地接应用
     * 3 -- 车调应用
     * 11 -- 批发商应用
     * 在顶部菜单需要需要显示 "企业应用" 菜单
     * 并且根据用户拥有的权限查询用户拥有的第一个权限的上级菜单的url给前端
     */
    public function getCompanyAppLink(){
        $company_id = $this->getCompanyId();
        $app_id = $this->getCompanyAppId($company_id);
        $menuCallback = __NAMESPACE__.'\RoleManageForm::filterMenuCallback';
        if(is_null($app_id)){
            return null;
        }
        $support = false;
        foreach($app_id as $k=>$v){
            if($v==1||$v==3||$v==11){
                $support = true;
            }
        }
        if(!$support){
            return null;
        }
        $company_id = $this->getCompanyId();
        $user_id = $this->getUserId();
        $is_super_user = $this->isSuperUser();
        if($is_super_user) {
            $query = AuthItem::find()->select(['id','parent_id','visible','sort','name','url'])
                ->where(['type'=>4])
                ->andWhere(['in','industry_appid',$app_id])
                ->orderBy('parent_id,sort');
            $data = $query->asArray()->all();
            $tree = $this->buildTree($data,$menuCallback);
            $link_menu_app = null;
            foreach($tree as $k=>$v){
                if($v['text']=='企业管理'||$v['text']=='企业账户'){
                    continue;
                }
                $link_menu_app = $this->getNodeFirstMenu($v['children']);
                if($link_menu_app!=''){break;}
            }
            return $link_menu_app;
        }
        //不是管理员,先查看拥有的角色和权限
        $role_id = $this->getUserRoles($company_id,$user_id);
        $permissions = $this->getRolePermissions($role_id);
        //获取所有应用的权限和菜单
        $all_items = $this->getAppMenuAndPermission(array_merge($app_id,[self::APP_ID_BASE]));

        //根据用户拥有的权限ID进行过滤，目的是根据权限ID 回溯查看拥有的菜单
        $menu = $this->getTreePath($all_items,$permissions);
        $tree = self::buildTree($menu,$menuCallback);
        $link_menu_app = null;
        foreach($tree as $k=>$v){
            if($v['text']=='企业管理'||$v['text']=='企业账户'){
                continue;
            }
            $link_menu_app = $this->getNodeFirstMenu($v['children']);
            if($link_menu_app!=''){break;}
        }
        return $link_menu_app;
    }

    public function getNodeFirstMenu($node){
        $url = '';
        foreach($node as $k=>$v){
            if($v['url']!=''){
                $url =  $v['url'];
                break;
            }else{
                if($v['children']) {
                    $url = $this->getNodeFirstMenu($v['children']);
                }
            }
        }
        return $url;
    }

    public function getMenuPermissionsMap($key,$menu,$app_id)
    {
        $cache = Yii::$app->menu_cache;
        $data = false;//$cache->get($key);
        if($data == false){
            //获取系统所有状态的菜单和权限
            $items = AuthItem::find()->where(['status' => 1])
                ->andWhere(['in', 'company_id', [0]])
                ->andWhere(['in', 'type', [2, 4]])
                ->andWhere(['in', 'industry_appid', [$app_id]])//得到应用的权限节点
                ->orderBy('parent_id,sort')->asArray()->all();
            $id_company_manage = $this->getMenuId($menu);
            $data = $this->getSubId($items, $id_company_manage);
            $cache->set($key,$data,0);
        }
        return $data;
    }

    public function getMenuId($menu_name)
    {
        $menu = AuthItem::find()->select('id,parent_id')
            ->where(['status'=>1,'type'=>4,'parent_id'=>0])
            ->andWhere(['=','industry_appid',8])
            ->andWhere(['like','name',$menu_name])->one();
        return $menu->id;
    }
    //获取某个菜单下面的子菜单和权限的ID
    public function getSubId(&$items,$id=0){
        $subs=[];
        foreach($items as $item){
            if($item['parent_id']==$id){
                $subs[]=$item['id'];
                $subs=array_merge($subs,$this->getSubId($items,$item['id']));
            }
        }
        return $subs;
    }

    public function getAppMenu($app_id){
       return AuthItem::find()->where(['type'=>4,'visible'=>1,'status'=>1]) //只查询有效的可见菜单
        ->andWhere(['status' => 1])
            ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
            ->andWhere(['in','industry_appid', $app_id])//得到应用的权限节点
            ->orderBy('parent_id,sort')->asArray()->all();
    }

    public function getAppMenuAndPermission($app_id)
    {
        return AuthItem::find()->where(['status'=>1]) //只查询有效的可见菜单
            ->andWhere(['in','type',[2,4]])
            ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
            ->andWhere(['in','industry_appid', $app_id])//得到应用的权限节点
            ->orderBy('parent_id,sort')->asArray()->all();
    }
    /*
     * @param $company_id 公司ID
     * @param $user_id 用户ID
     * 获取用户的角色，如果用户是超级管理员，不需要调用这个函数
     */
    public function getUserRoles($company_id,$user_id){
        $query = AuthAssign::find()
            ->select('auth_item_id')
            ->where(['user_id'=>$user_id,'company_id'=>$company_id]);
        $roles = $query->asArray()->all();

        return $roles?ArrayHelper::getColumn($roles,'auth_item_id'):[];
    }
    /*
     * @param $role_id 角色ID数组
     * 获取用户的权限
     */
    public function getRolePermissions($role_id)
    {
        return AuthItemChild::find()
            ->select('auth_item_child.parent, auth_item_child.child')
            ->rightJoin('auth_item','auth_item.id = auth_item_child.child')
            ->where(['in','auth_item_child.parent',$role_id])
            ->andWhere(['auth_item.status'=>1])
            ->groupBy('child')->asArray()->all();
    }
    /*
     * 返回一个树状结构的节点
     */
    public function emptyApp($name)
    {
        return ['id'=>'0','text'=>$name,'url'=>"",'visible'=>"1","children"=>[]];
    }
    /*
     * 创建企业应用菜单
     * 缓存目前没启用，此处代码可简化处理，目前时间不够，暂不简化
     */
    public function buildAppMenu($app_id,$menuCallback)
    {
        //支持地接和车调应用
        $apps = [];
        sort($app_id,SORT_NUMERIC);
        $cache = Yii::$app->menu_cache;
        foreach($app_id as $k=>$v){
            if($v ==1){ //地接应用
                $app = false; //$cache->get(self::CACHE_KEY_APP_DIJIE);
                if($app ==false ) {
                    $app_items = $this->getAppMenu($v);
                    $app_menu = self::buildTree($app_items, $menuCallback);
                    $app = $this->emptyApp('地接应用');
                    $app['children'] = $app_menu;
                    $cache->set(self::CACHE_KEY_APP_DIJIE,$app);
                }
                $apps[] = $app;
            }
            if($v==3){ //车调应用
                $app = false;//$cache->get(self::CACHE_KEY_APP_DRIVER);
                if($app == false) {
                    $app_items = $this->getAppMenu($v);
                    $app_menu = self::buildTree($app_items, $menuCallback);
                    $app = $this->emptyApp('车调应用');
                    $app['children'] = $app_menu;
                    $cache->set(self::CACHE_KEY_APP_DRIVER,$app);
                }
                $apps[] = $app;
            }
            if($v==11){ //批发商应用
                $app = false;//$cache->get(self::CACHE_KEY_APP_PURCHASE);
                if($app == false) {
                    $app_items = $this->getAppMenu($v);
                    $app_menu = self::buildTree($app_items, $menuCallback);
                    $app = $this->emptyApp('批发商应用');
                    $app['children'] = $app_menu;
                    $cache->set(self::CACHE_KEY_APP_PURCHASE,$app);
                }
                $apps[] = $app;
            }
        }
        return $apps;
    }

    /*
     * 标记角色拥有的权限。
     */
    public function markArray(&$dst,&$src,$dstId='id',$srcId='child')
    {
        foreach($dst as $k=>$v){
            $dst[$k]['mark'] = 0;
            foreach($src as $k2=>$v2){
                if($dst[$k][$dstId] == $src[$k2][$srcId]){
                    $dst[$k]['mark']=1;
                    unset($src[$k2]);
                    break;
                }
            }
        }
    }

    /*
     * @过滤菜单
     */
    public static function filterMenuCallback($src){
        $o['text'] =  $src['name'];
        $o['id'] = $src['id'];
        $o['url'] = $src['url'];
        $o['visible'] = $src['visible'];
        return $o;
    }

    /*
     * 过滤权限
     */
    public static function filterPermissionCallback($src){
        $o['text'] =  $src['name'];
        $o['id'] = $src['id'];
        $o['type'] = $src['type'];
        if(isset($src['mark'])){
            $o['checked'] = $src['mark'];
        }
        return $o;
    }

    /*
     * @Author: code lighter
     * @Date: 2017-05-27
     */
    public static function buildTree($array,$callback=null,$parent_id=0,$child_node="children"){
        $tree = [];
        foreach($array as $k=>$v){
            if($v['parent_id'] == $parent_id){
                unset($array[$k]);
                $tmp =is_callable($callback)?call_user_func($callback,$v):$v;
                $children = self::buildTree($array,$callback,$v['id'],$child_node);
                if($children){
                    $tmp[$child_node] = $children;
                }
                $tree[] = $tmp;
            }
        }
        return $tree;
    }

    public function arrayFilter($all_items, $del_items) {
        foreach ($all_items as $k => $v) {
            foreach ($del_items as $uk => $uv) {
                if ($v['id'] == $uv['child']) {
                    $all_items[$k]['mark'] = 1;
                    $tmp_arr = $this->arrayFilter($all_items,array(0=>array('child'=>$v['parent_id'])));
                    $all_items = $tmp_arr;
                }
            }
        }
        return $all_items;
    }

    /*
     * @API 子账号管理 -- 角色管理 -- 获取权限列表
     * @Author code lighter
     * @Date 2017-05-27
     */
    public function queryAuthList(){
        $company_id = $this->getCompanyId();
        $app_id = $this->getCompanyAppId($company_id);
        //获取所有权限
        $data = AuthItem::find()->select(['id','industry_appid','parent_id','name','url','sort',
            'parent_id','type'])->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
            ->andWhere(['or', ['company_id' => $company_id], ['company_id' => 0]])
            ->andWhere(['in','industry_appid',array_merge([8],$app_id)])//得到指定应用的权限节点
            ->andFilterWhere(['is_platform'=>0])
            ->orderBy('parent_id,sort');
        $all_node = $data->asArray()->all();
        foreach($all_node as $k=>$v){
            if($v['industry_appid']==1&&$v['parent_id']==0){
                $all_node[$k]['name'] = '地接应用--'.$v['name'];
            }
            if($v['industry_appid']==11&&$v['parent_id']==0){
                $all_node[$k]['name'] = '批发商应用--'.$v['name'];
            }
        }
        //重新组装数组结构
        $tree = TreeHelper::getSubs($all_node);
        $this->filterAuthData($tree);
        $tree = $this->changeSubsEdit($tree, '');
        return $tree;
    }

    /*
     * 获取角色对应的权限.
     * @Author: code lighter
     * @Date: 2017/05/27
     */
    public function queryRolePermissions($role_id,$company_id,$clean=true){
        $permissionCallback = __NAMESPACE__.'\RoleManageForm::filterPermissionCallback';
        $app_id = $this->getCompanyAppId($company_id);
        if($company_id == 0){//考虑内置角色
            $app_id = [self::APP_ID_DIJIE,self::APP_ID_DRIVER];
        }
        $app_id = array_merge([8],$app_id);
        //角色分配的权限
        $permissions = AuthItemChild::find()->where(['parent'=>$role_id])->asArray()->all();
        //公司拥有的权限
        $all_permission = AuthItem::find()->select(['id','name','type','parent_id'])->where(['IN', 'type', [2,4]])
            ->andWhere(['status'=>1])
            ->andWhere(['or', ['company_id' => $company_id], ['company_id' => 0]])
            ->andWhere(['in','industry_appid',$app_id])//得到指定应用的权限节点
            ->andFilterWhere(['is_platform'=>0])
            ->orderBy('parent_id,sort')->asArray()->all();
        $this->markPermission($all_permission,$permissions);
        if($clean){
            foreach($all_permission as $k=>$v){
                if((int)$v['type']==2 && $v['checked'] == false){
                    unset($all_permission[$k]);
                }
            }
        }
        $tree = self::buildTree($all_permission);
        $this->filterAuthData($tree);
        $this->removeMenuWithoutPermissions($tree);
        $tree = $this->array_values_recursive($tree);
        return array_values($tree);
    }

    public function array_values_recursive($arr)
    {
        foreach($arr as $key => $value){
            if (is_array($value)){
                $arr[$key] = $this->array_values_recursive($value);
            }
        }
        if(isset($arr['children'])){
            $arr['children'] = array_values($arr['children']);
        }
        return $arr;
    }

    public function removeMenuWithoutPermissions(&$array)
    {
        foreach($array as $k=>$v){
            if((int)$v['type']==4 && empty($v['children'])){
                unset($array[$k]);
            }
            if(isset($v['children'])){
                if($this->isMenuOnly($v['children'])){
                    unset($array[$k]);
                }
            }
            if((int)$v['type']==4 &&isset($array[$k])&&isset($v['children'])){
                $this->removeMenuWithoutPermissions($array[$k]['children']);
            }
        }
    }

    public function isMenuOnly($array){
        $ret = true;
        foreach($array as $k=>$v){
            if((int)$v['type']==4 &&isset($v['children'])){
                $ret = $this->isMenuOnly($array[$k]['children']);
            }
            if((int)$v['type']!=4){
                $ret = false;
            }
            if(!$ret){break;}
        }
        return $ret;
    }

    public function markPermission(&$all,&$sub){
        foreach($all as $k=>$v){
            if($v['type'] == '2'||$v['type'] == 2) {
                $all[$k]['checked'] = false;
            }
            foreach($sub as $k2=>$v2){
                if($v2['child'] == $v['id'] && ($v['type'] =='2'||$v['type']==2)){
                    $all[$k]['checked'] = true;
                    break;
                }
            }
        }
    }

    public function filterAuthDataEx(&$tree)
    {
        //去除首页和企业应用两个模块的权限
        //企业账户里面只保留资金管理权限
        foreach($tree as $k=>$v){
            if($v['name'] == '首页'){
                unset($tree[$k]);
            }
//            if($v['name'] == '企业账户'){
//                if(count($v['children'])){
//                    foreach($v['children'] as $k1=>$v1){
//                        if($v1['name']=='安全设置'||
//                            $v1['name'] =='账户资料'||
//                            $v1['name'] == '应用管理'){
//                            unset($tree[$k]['children'][$k1]);
//                        }
//                    }
//                }
//            }
        }
        //提取子账号管理里面的数据
        foreach($tree as $k =>$v){
            if($v['name']=='企业管理'&& count($v['children'])){
                $copy = null;
                foreach($v['children'] as $k1=>$v1){
                    if($v1['name']=='子账号管理' && count($v1['children'])){
                        $copy = array_reverse($v1['children']);
                        unset($tree[$k]['children'][$k1]);
                        foreach($copy as $k2=>$v2){
                            array_unshift($tree[$k]['children'],$v2);
                        }
                        break;
                    }
                }
            }
        }
    }
    /*
     * 前端权限是固定的，需要进行过滤
     */
    public function filterAuthData(&$tree){
        //去除首页和企业应用两个模块的权限
        //企业账户里面只保留资金管理权限
        foreach($tree as $k=>$v){
            if($v['name'] == '首页'||$v['name'] == '企业应用'){
                unset($tree[$k]);
            }
        }
        //提取子账号管理里面的数据
        foreach($tree as $k =>$v){
            if($v['name']=='企业管理'&& count($v['children'])){
                $copy = null;
                foreach($v['children'] as $k1=>$v1){
                    if($v1['name']=='子账号管理' && count($v1['children'])){
                        $copy = array_reverse($v1['children']);
                        unset($tree[$k]['children'][$k1]);
                        foreach($copy as $k2=>$v2){
                            array_unshift($tree[$k]['children'],$v2);
                        }
                        break;
                    }
                }
            }
        }
    }

    //组装树状结构
    public static function changeSubsEdit($array, $power) {
        $tree = [];
        foreach ($array as $k => $v) {
            $tree[$k]['id'] = $v['id'];
            $tree[$k]['text'] = $v['name'];
            $tree[$k]['url'] =$v['url'];
            $tree[$k]['type'] = $v['type'];
            $tree[$k]['checked'] = 0;

            if (!empty($power)) {
                foreach ($power as $pk => $pv) {
                    if ($v['id'] == $pv['child']) {
                        $tree[$k]['checked'] = 1;
                        break;
                    }
                }
            }
            if (isset($v['children'])) {
                $tree[$k]['children'] = self::changeSubsEdit($v['children'], $power);
            }
        }
        return $tree;
    }

    public function queryRolePermission($user_id,$company_id){
        //ajax请求
        $id = Yii::$app->request->post('id');
        $company_id = Yii::$app->user->CompanyId;
        if ($company_id == 1) {
            $is_platform = '';
        } else {
            $is_platform = 0;
        }

        //得到审核通过的应用
        $company_app_data = CompanyApp::find()->where(['company_id'=>$company_id,'audit_status'=>2])->all();
        $industry_appid = [8];//企业基础应用
        if ($company_app_data && count($company_app_data)>0) {
            foreach ($company_app_data as $k=>$v) {
                $industry_appid[] = $v['industry_app_id'];
            }
            array_unique($industry_appid);//去重
        }

        if (Yii::$app->request->isPost && !empty($id)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //获取角色目前所拥有的权限节点
            $power = AuthItemChild::find()->where(['parent'=>$id])->asArray()->all();
            //获取所有权限节点
            $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
                ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
                ->andFilterWhere(['is_platform'=>$is_platform])
                ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
            //重新组装数组结构
            $reArr = TreeHelper::getSubs($all_node);
            $reArr = $this->changeSubsEdit($reArr, $power);
            return $reArr;
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            //获取所有权限节点
            $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
                ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
                ->andFilterWhere(['is_platform'=>$is_platform])
                ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
            //重新组装数组结构
            $reArr = TreeHelper::getSubs($all_node);
            $reArr = $this->changeSubsEdit($reArr, '');
            return $reArr;
        }
    }

    /**
     * 根据uid,company_id查询用户的角色
     * @param $uid
     * @param $company_id
     */
    public function getUserRole($user_id,$company_id){
        return AuthAssign::findAll(['user_id' => $user_id, 'company_id' =>$company_id]);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'company_id'=>'company_id',
            'role_name'=>'角色名称',
            'page_index'=>'Page Index',
            'page_size'=>'Page Size'
        ];
    }
}
