<?php

namespace company\controllers;

use common\controllers\BaseController;
use company\models\AuthAssign;
use company\models\AuthItemChild;
use company\models\Route;
use Yii;
use company\models\AuthItem;
use company\models\search\AuthItem as AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\helpers\TreeHelper;
use yii\helpers\Url;
use common\models\CompanyApp;
use company\components\CompanyUser;
use company\models\RoleManageForm;

class RoleManageController extends BaseController
{
    public $enableCsrfValidation = false;

    public $layout = "public";

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    /*
     * @API 判断是否已登录，没有登录就拒绝服务
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function rejectService($msg){
        Yii::$app->Response->format = Response::FORMAT_JSON;
        Yii::$app->Response->data = ["status"=>0, "message"=>$msg, "data"=>"", "url"=>""];
    }
    public function beforeAction($action)
    {
        if(Yii::$app->user->isGuest){
            $this->rejectService('请登录后查看');
            return false;
        }
        return parent::beforeAction($action);
    }
    public function actionAppLink()
    {
        $link =  (new RoleManageForm)->getCompanyAppLink();
        return $this->ajaxSuccess('','',$link);
    }
    /*
     * @API 子账号管理 -- 角色管理 -- 子账号角色列表
     * @param int $company_id
     * @param string $role_name
     * @param int $page_index
     * @param int $page_size
     */
    public function actionList()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('index',[]);
        }
        $keyword = Yii::$app->request->post('role_name');
        $pageIndex = Yii::$app->request->post('page_index',1);
        $pageSize = Yii::$app->request->post('page_size',5);
        $pageOffset = $pageSize*($pageIndex-1);
        $list = (new RoleManageForm)->roleList($keyword,$pageOffset,$pageSize);
        return $this->ajaxSuccess('','',$list);
    }
    /*
     * @API 子账号管理 -- 角色管理 -- 子账号菜单
     */
    public function actionRoleMenu()
    {
        $menu = (new RoleManageForm)->getRoleMenu();
        return $this->ajaxSuccess('','',$menu);
    }
    /*
     * @API 子账号管理 -- 角色管理 -- 子账号权限列表
     */
    public function actionAuthList()
    {
        $authList = (new RoleManageForm)->queryAuthList();
        return $this->ajaxSuccess('', '', $authList);
    }

    /*
     * @API 子账号管理 -- 角色管理 -- 创建角色
     */
    public function actionCreate()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('create',[]);
        }
        $role_name = Yii::$app->request->post('role_name');
        $company_id = Yii::$app->user->getCompanyId();
        if($this->isRoleNameExist($role_name,$company_id)){
            return $this->ajaxFail('角色名称已经存在','','');
        }
        $model = new AuthItem();
        $model->scenario = AuthItem::SCENE_ROLE_AUTH;
        $model->type = AuthItem::TYPE_ROLE;
        if ($model->load(Yii::$app->request->post(),'')) {
            $model->company_id = Yii::$app->user->getCompanyId();
            $role_auth_id = Yii::$app->request->post('role_auth_id');
            $model->name = Yii::$app->request->post('role_name');
            $model->description = Yii::$app->request->post('role_desc');
            //创建角色并分配权限
            if ($model->editAuth(1, $role_auth_id)){
                return $this->ajaxSuccess('添加角色成功','','');
            }
            return $this->ajaxFail(current($model->getFirstErrors()),'','');
        }
        $error = $model->getFirstErrors();
        return $this->ajaxFail($error,'','');
    }
    //判断角色名称是否已经存在
    public function isRoleNameExist($role_name,$company_id,$id=0)
    {
        $data = AuthItem::findAll(['name'=>$role_name,'company_id'=>$company_id]);
        if($id==0) {
            return $data ? true : false;
        }else{
            if($data){
                foreach($data as $k=>$v){
                    if($v->id != $id && $v->name == $role_name){
                        return true;
                    }
                }
                return false;
            }
            return false;
        }
    }

    /*
     * @API 子账号管理 -- 角色管理 -- 查看角色详情,包含角色和权限
     */
    public function actionView()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('view',[]);
        }
        $role_id = Yii::$app->request->post('role_id');
        $company_id = Yii::$app->user->getCompanyId();
        //查询是否是内置角色？
        $model = AuthItem::find()->where(['id'=>$role_id,'company_id'=>0,'type'=>1])->one();
        $is_system_role = true;
        if(is_null($model)) {
            $model = AuthItem::find()->where(['id' => $role_id, 'company_id' => $company_id, 'type' => 1])->one();
            if (is_null($model)) {
                return $this->ajaxFail('角色不存在', '', 'invalid role id');
            }
            $is_system_role = false;
        }
        if($is_system_role){ //如果是内置角色，需要重置company_id 为 0
            $company_id = 0;
        }
        $role_name = $model->name;
        $role_desc = $model->description;
        $auth_manage = new RoleManageForm;
        $role_permissions = $auth_manage->queryRolePermissions($role_id,$company_id);
        $data = [
            'role_name'=>$role_name,
            'role_desc'=>$role_desc,
            'role_permissions'=>$role_permissions,
            'is_system'=>$is_system_role
        ];
        return $this->ajaxSuccess('','',$data);
    }
    /*
     * @API 子账号管理 -- 角色管理 -- 删除角色
     */
    public function actionDelete()
    {
        $roleId = Yii::$app->request->post('role_id');
        $trans = Yii::$app->db->beginTransaction();
        try {
               $form = new RoleManageForm;
               $r = $form->roleDelete($roleId);
               $r?$trans->commit():$trans->rollBack();
               if($r){
                  return $this->ajaxSuccess('','','');
               }else{
                  return $this->ajaxFail('删除角色失败','','');
               }
        }catch(\Exception $e){
            return $this->ajaxFail($e->getMessage(),'',$e->getMessage());
        }
    }

    /**
     * 刷新权限集
     * Remove routes
     * @param $id
     * @return array
     */
    public function actionRefreshPermissions($id = '')
    {
        $model = new Route();
        $model->invalidate();
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        Yii::$app->getCache()->delete('company\models\Route::getAppRoutes');
        return $model->getRoutes($id);
    }

    /**
     * 移除角色权限
     * Remove routes
     * @param $id
     * @return array
     */
    public function actionRemovePermissions($id)
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $authItem = new AuthItem();
        $authItemChild = new AuthItemChild();
        foreach ($routes as $route) {
            $_authItem = clone $authItem;
            $_authItemChild = clone $authItemChild;
            try {
                $permissionId = $_authItem::find()->select('id')->where(['url' => $route, 'type' => AuthItem::TYPE_PERMISSION])->scalar();
                $_authItemChild::find()->where(['parent' => $id, 'child' => $permissionId])->one()->delete();
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return $authItem::findOne(['id' => $id])->getRolePermissions();
    }

    /**
     * 分配角色权限
     * Remove routes
     * @param $id
     * @return array
     */
    public function actionAssignPermissions($id)
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $authItem = new AuthItem();
        $authItemChild = new AuthItemChild();
        foreach ($routes as $route) {
            $_authItem = clone $authItem;
            $_authItemChild = clone $authItemChild;
            try {
                $permissionId = $_authItem::find()->select('id')->where(['url' => $route, 'type' => AuthItem::TYPE_PERMISSION])->scalar();
                $_authItemChild->setAttributes(['parent' => $id, 'child' => $permissionId]);
                $_authItemChild->save();
            } catch (\Exception $exc) {
                Yii::error($exc->getMessage(), __METHOD__);
            }
        }
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return $authItem::findOne(['id' => $id])->getRolePermissions();
    }

    /**
     * 更新角色
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('create',[]);
        }
        $role_id = Yii::$app->request->post('role_id');
        $role_name = Yii::$app->request->post('role_name');
        $company_id = Yii::$app->user->getCompanyId();
        if($this->isRoleNameExist($role_name,$company_id,$role_id)){
            return $this->ajaxFail('角色名称已经存在','','');
        }
        $role_auth_id = Yii::$app->request->post('role_auth_id');
        $model = $this->findModel($role_id);
        if ($model->load(Yii::$app->request->post(),'')) {
            //编辑角色与权限
            $model->name = $role_name;
            $model->description = Yii::$app->request->post('role_desc');
            $model->save(false);
            $model->company_id = Yii::$app->user->getCompanyId();
            if ($model->editAuth(2, $role_auth_id,$role_id)) {
                return $this->ajaxSuccess('',Url::to(['role-manage/list']),'');
            } else {
                return $this->ajaxSuccess('',current($model->getFirstErrors()),'');
            }
        }
        return $this->render('create',[]);
    }

//    //异步加载权限数据
//    public function actionLoadAuthDate() {
//        //ajax请求
//        $id = Yii::$app->request->post('id');
//        $company_id = Yii::$app->user->CompanyId;
//        if ($company_id == 1) {
//            $is_platform = '';
//        } else {
//            $is_platform = 0;
//        }
//
//        //得到审核通过的应用
//        $company_app_data = CompanyApp::find()->where(['company_id'=>$company_id,'audit_status'=>2])->all();
//        $industry_appid = [8];//企业基础应用
//        if ($company_app_data && count($company_app_data)>0) {
//            foreach ($company_app_data as $k=>$v) {
//                $industry_appid[] = $v['industry_app_id'];
//            }
//            array_unique($industry_appid);//去重
//        }
//
//        if (Yii::$app->request->isPost && !empty($id)) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            //获取角色目前所拥有的权限节点
//            $power = AuthItemChild::find()->where(['parent'=>$id])->asArray()->all();
//            //获取所有权限节点
//            $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
//                ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
//                ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
//                ->andFilterWhere(['is_platform'=>$is_platform])
//                ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
//            //重新组装数组结构
//            $reArr = TreeHelper::getSubs($all_node);
//            $reArr = $this->changeSubsEdit($reArr, $power);
//            return $reArr;
//        } else {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            //获取所有权限节点
//            $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
//                ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
//                ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
//                ->andFilterWhere(['is_platform'=>$is_platform])
//                ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
//            //重新组装数组结构
//            $reArr = TreeHelper::getSubs($all_node);
//            $reArr = $this->changeSubsEdit($reArr, '');
//            return $reArr;
//        }
//    }

    /**
     *  增加用户组时获取所有的节点
     * @param  array  	$arr   所有权限节点
     * @param  array  	$arr   该角色所拥有的权限节点
     */
    public static function changeSubsEdit($arr, $power) {

        $jsonArr = array();
        foreach ($arr as $key => $val) {
            $jsonArr[$key]['id'] = $val['id'];
            $jsonArr[$key]['text'] = $val['name'];
            $jsonArr[$key]['type'] = $val['type'];

            $jsonArr[$key]['checked'] = 0;
            if (!empty($power)) {
                foreach ($power as $pk => $pv) {
                    if ($val['id'] == $pv['child']) {
                        $jsonArr[$key]['checked'] = 1;
                        break;
                    }
                }
            }

            if (isset($val['children'])) {
                $jsonArr[$key]['children'] = self::changeSubsEdit($val['children'], $power);
            }
        }
        return $jsonArr;
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var $model AuthItem */
        if (($model = AuthItem::find()->where(['id' => $id, 'company_id' => Yii::$app->user->getCompanyId()])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
