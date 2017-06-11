<?php

namespace company\controllers;

use company\models\AuthItemChild;
use company\models\Route;
use Yii;
use company\models\AuthItem;
use company\models\search\AuthItem as AuthItemSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\helpers\TreeHelper;
use yii\helpers\Url;
use common\models\CompanyApp;

class AuthController extends Controller
{
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

    /**
     * 角色列表
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch(['type' => AuthItem::TYPE_ROLE]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        //ajax
        if (Yii::$app->request->getIsAjax()) {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            return $dataProvider->getModels();
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 角色详情
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 创建角色
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $model->type = AuthItem::TYPE_ROLE;
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $purview = Yii::$app->request->post('AuthItem')['purview'];
            $model->company_id = Yii::$app->user->getCompanyId();
            //编辑角色与权限
            if ($model->editAuth(1, $purview)) {
                $back['status'] = 1;
                $back['message'] = '';
                $back['url'] = Url::to(['auth/index']);
                return $back;
            } else {
                $back['status'] = 0;
                $back['message'] = current($model->errors)[0];
                $back['url'] = '';
                return $back;
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
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
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $purview = Yii::$app->request->post('AuthItem')['purview'];
            //编辑角色与权限
            Yii::$app->getResponse()->format = Response::FORMAT_JSON;
            $model->company_id = Yii::$app->user->getCompanyId();
            if ($model->editAuth(2, $purview, $id)) {
                $back['status'] = 1;
                $back['message'] = '';
                $back['url'] = Url::to(['auth/index']);
                return $back;
            } else {
                $back['status'] = 0;
                $back['message'] = current($model->errors)[0];
                $back['url'] = '';
                return $back;
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    
    //异步加载权限数据
    public function actionLoadAuthDate() {
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
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->db->transaction(function () use ($id) {
            $this->findModel($id)->delete();
            AuthItemChild::deleteAll(['parent' => $id]);
        });

        return $this->redirect(['index']);
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
