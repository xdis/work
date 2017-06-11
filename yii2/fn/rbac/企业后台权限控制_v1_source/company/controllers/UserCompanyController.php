<?php

namespace company\controllers;

use company\models\AuthAssign;
use company\models\search\AuthItem;
use company\models\search\CompanyApp;
use eleiva\noty\Noty;
use Yii;
use company\models\UserCompany;
use company\models\search\UserCompanySearch;
use common\controllers\BaseCompanyController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use moonland\phpexcel\Excel;
use yii\web\UploadedFile;
use company\models\UserCompanyImport;
use company\models\Department;
use company\models\User;
use company\models\SignupForm;
use common\models\UserValidate;
use company\models\BankCard;
use common\models\UserProfile;
use common\models\Company;
use common\models\DpStore;
use yii\web\Response;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * UserCompanyController implements the CRUD actions for UserCompany model.
 */
class UserCompanyController extends BaseCompanyController
{
    /**
     * Lists all UserCompany models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userCompanyImport = new UserCompanyImport();
        $searchModel = new UserCompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $company_id = Yii::$app->user->getCompanyId();

        //得到审核通过的应用
        $company_app_data = CompanyApp::find()->where(['company_id'=>$company_id,'audit_status'=>2])->all();
        $industry_appid = [8];//企业基础应用
        if ($company_app_data && count($company_app_data)>0) {
            foreach ($company_app_data as $k=>$v) {
                $industry_appid[] = $v['industry_app_id'];
            }
            array_unique($industry_appid);//去重
        }

        //角色列表
        $authitem_model = AuthItem::find()
                            ->where(['type'=>1,'status'=>1])
                            ->andWhere(['or', ['in','industry_appid',$industry_appid], ['company_id'=>$company_id]])
                            ->all();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userCompanyImport'  => $userCompanyImport,
            'authitem_model'=>$authitem_model
        ]);
    }

    /**
     * Displays a single UserCompany model.
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
     * Creates a new UserCompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        if (Yii::$app->request->isPost) {
            $staff_mobile = Yii::$app->request->post('UserCompany')['staff_mobile'];
            $company_id = Yii::$app->user->getCompanyId();
            $model = UserCompany::find()->where(['company_id'=>$company_id,'staff_mobile'=>$staff_mobile,'is_deleted'=>1])->one();
            
            //如果员工记录存在，则更新状态，否则为创建记录
            if ($model) {
                if ($userRoles = ArrayHelper::getColumn(AuthAssign::getUserRoles($model->user_id), 'auth_item_id')) {
                    $model->roleIds = $userRoles;
                }
                
                $model->staff_status = 1;
                $model->is_deleted = 0;
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    //分配角色
                    AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
                    if ($model->roleIds) {
                        AuthAssign::create($model->user_id, $model->roleIds);
                    }
                    
                    return $this->redirect('index');
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                
            } else {
                $model = new UserCompany();
                $model->scenario = 'add_user_company';
                $model->company_id = Yii::$app->user->getCompanyId();
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    //分配角色
                    AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
                    if ($model->roleIds) {
                        AuthAssign::create($model->user_id, $model->roleIds);
                    }
                    
                    return $this->redirect('index');
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            
        } else {
            $model = new UserCompany();
            $model->company_id = Yii::$app->user->getCompanyId();
            
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        
    }

    /**
     * Updates an existing UserCompany model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $company_id = Yii::$app->user->getCompanyId();
        if ($userRoles = ArrayHelper::getColumn(AuthAssign::getUserRoles($model->user_id), 'auth_item_id')) {
            $model->roleIds = $userRoles;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            //分配角色
            AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
            if ($model->roleIds) {
                AuthAssign::create($model->user_id, $model->roleIds);
            }
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserCompany model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->staff_status = 0;
        $model->is_deleted = 1;
        $model->is_opened_store = 0;
        $model->save(false);
        
        $shop_model = DpStore::find()->where(['company_id'=>$model->company_id,'ower_id'=>$model->user_id])->one();
        if ($shop_model) {
            $shop_model->status = 0;
            $shop_model->save();
        }

        return $this->redirect(['index']);
    }


    public function actionResetPassword($id)
    {
        if (($model = $this->findModel($id)) && $password = rand(00000001, 99999999)) {
            try {
                $model->password_admin = $password;
                $model->save();
                // todo 发短信
//            Yii::$app->smser->send($model->staff_mobile, '您在【中晟达集团】的重置管理密码申请已受理，新管理密码为' . $password . '，请勿泄露，登录后尽快修改。【微叮】');
                Yii::$app->session->setFlash('noty', ['text' => '发送成功', 'type' => Noty::SUCCESS]);
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('noty', ['text' => '发送失败', 'type' => Noty::ERROR]);
            }
        }
        return $this->redirect('index');
    }

    /**
     * Finds the UserCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserCompany::findOne(['id' => $id, 'company_id' => Yii::$app->user->getCompanyId()])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * 批量导入员工
     * @throws \Exception
     */
    public function actionImport  () {
        
        $back = ['status'=>0,'message'=>'','url'=>''];
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        //企业配置
        $company_id = Yii::$app->user->getCompanyId();
        $user_id    = Yii::$app->user->getId();
    
        //excel导出配置
        $title      = '员工导入失败汇总';
        $faildata   = array();//错误记录集合
        $headNames  = array('部门','岗位','姓名','手机号码','错误原因');
    
        //第一步：上传文件
        $model = new UserCompany();
        $model->scenario = 'import_user_company';
        $model->user_company_excelfile = UploadedFile::getInstanceByName('user_company_excelfile');
        
        $base_path = \Yii::getAlias('@storage/web/source/user_company_import/');
        $upload_file_name = 'upload_import'.date('YmdHis',time());//修改上传的文件名字，这样可避免中文乱码错误之类的问题
        if ($model->upload($base_path, $upload_file_name , 'user_company_excelfile')) {
    
            //第二步：获取上传文件内容
            $uploadfile     = $base_path. $upload_file_name . '.' . $model->user_company_excelfile->extension;
            //step1：根据后缀实例化配置
            $objPHPExcel    = new \PHPExcel();
            if ($model->user_company_excelfile->extension=="xlsx") {
                $objReader  = \PHPExcel_IOFactory::createReader('Excel2007');        //use excel2007 for 2007 format
            } else {
                $objReader  = \PHPExcel_IOFactory::createReader('Excel5');           //use excel2007 for 2007 format
            }
    
            $objPHPExcel = $objReader->load($uploadfile);                           //读取Excel文件内容
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();                                  //取得总行数
            $highestColumn = $sheet->getHighestColumn();                            //取得总列数
             
            $objWorksheet = $objPHPExcel->getActiveSheet();
    
            $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
    
            $headtitle=array();
            //step2：从第二行开始读数据
            for ($row = 2;$row <= $highestRow;$row++) {
                try {
                    $strs=array();
                    //注意highestColumnIndex的列数索引从0开始
                    for ($col = 0;$col < $highestColumnIndex;$col++) {
                        $strs[$col] =$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                    }
    
                    //step3：部门，岗位，姓名，手机号码都不允许为空
                    if (empty($strs[0]) || empty($strs[1]) || empty($strs[2]) || empty($strs[3])) {
                        //记录没有导入成功的数据
                        throw new \Exception('部门，岗位，姓名，手机号码都不允许为空');
                    }
    
                    //step4：检查手机号码格式
                    $is_mobile = is_mobile($strs[3]);
                    if (!$is_mobile) {
                        throw new \Exception('手机号码格式错误');
                    }
    
                    //step5：如果找不到该部门，则创建
                    $department_model = Department::find()->where(['company_id'=>$company_id,'name'=>trim($strs[0]),'is_deleted'=>0])->one();
                    if (empty($department_model)) {
                        $department_model = new Department();
                        $department_model->company_id   = $company_id;
                        $department_model->name         = trim($strs[0]);
                        $department_model->is_deleted   = 0;
                        if (!$department_model->save()) {
                            throw new \Exception('创建部门失败'.current($department_model->errors)[0]);
                        }
                    }
                    
                    //判断是否为企业注册手机号，如果是，则导入失败
                    $company_model = Company::findOne($company_id);
                    $user_model = User::findOne($company_model->user_id);
                    if ($user_model->mobile == $strs[3]) {
                        throw new \Exception('导入失败...企业注册手机号已经是超级管理员');
                    }
    
                    //step6：根据手机号码查找是否已经注册，如果没有则自动注册（个人）
                    $user_data = User::find()->where(['mobile'=>trim($strs[3]),'user_type'=>2])->one();
    
                    if (empty($user_data)) {
                        throw new \Exception('导入失败...必须为平台注册用户');
                        
                        //产品要求：必须是已经注册的用户才能添加为员工，所以以下注释代码用不上
                         //开启事务
                        $trans = Yii::$app->db->beginTransaction();
                        //注册
                        $username = randStr('1',false,'abcdefg').$this->checkUsername(); //得到唯一微叮号
                        $SignupForm_model = new SignupForm();
                        $SignupForm_model->scenario = 'import';
                        $SignupForm_model->username = $username;
                        $SignupForm_model->mobile = trim($strs[3]);
                        $SignupForm_model->user_type = 2;
                        $SignupForm_model->password = substr(trim($strs[3]),-6);
                        $user_info = $SignupForm_model->signup();//调用注册方法
    
                        if (!$user_info) {
                            $trans->rollback();
                            throw new \Exception('注册失败:'.current($SignupForm_model->errors)[0]);
                        } else {
                            $trans->commit();
                        }
    
                        //插入成为员工信息
                        $department_model = Department::find()->where(['company_id'=>$company_id,'name'=>trim($strs[0]),'is_deleted'=>0])->one();
    
                        $userCompany_model = new UserCompany();
                        $userCompany_model->user_id             = $user_info->id; //用户id
                        $userCompany_model->company_id          = $company_id;
                        $userCompany_model->department_id       = $department_model->id;
                        $userCompany_model->staff_name          = trim($strs[2]);
                        $userCompany_model->staff_mobile        = trim($strs[3]);
                        $userCompany_model->position_name       = trim($strs[1]);
                        $userCompany_model->staff_status        = 1;    //是否在职//0 离职，1 在职
                        $userCompany_model->is_deleted          = 0;    //是否删除 //0 否,1是
                        $userCompany_model->is_opened_store     = 0;    //是否开通个人店铺//0 否,1是
                        $userCompany_model->created_at          = time();
                        if ($userCompany_model->save()) {
                            //部门人数+1
                            $department_model->staff_amount = empty($department_model->staff_amount) ? 1 : $department_model->staff_amount+1;
                            if (!$department_model->save()) {
                                throw new \Exception('建立员工关系失败:'.current($department_model->errors)[0]);
                            }
                        } else {
                            throw new \Exception('建立员工关系失败:'.current($userCompany_model->errors)[0]);
                        }
                    } else {//如果已经注册
                        //查找该员工是否已存在该公司
                        $userCompany_model = UserCompany::find()->where(['user_id'=>$user_data->id, 'company_id'=>$company_id])->one();
                        if (empty($userCompany_model)) {
                            //插入成为员工信息
                            $department_model = Department::find()->where(['company_id'=>$company_id,'name'=>trim($strs[0]),'is_deleted'=>0])->one();
    
                            $userCompany_model = new UserCompany();
                            $userCompany_model->user_id             = $user_data->id; //用户id
                            $userCompany_model->company_id          = $company_id;
                            $userCompany_model->department_id       = $department_model->id;
                            $userCompany_model->staff_name          = trim($strs[2]);
                            $userCompany_model->staff_mobile        = trim($strs[3]);
                            $userCompany_model->position_name       = trim($strs[1]);
                            $userCompany_model->staff_status        = 1;    //是否在职//0 离职，1 在职
                            $userCompany_model->is_deleted          = 0;    //是否删除 //0 否,1是
                            $userCompany_model->is_opened_store     = 0;    //是否开通个人店铺//0 否,1是
                            $userCompany_model->created_at          = time();
                            if ($userCompany_model->save()) {
                                //部门人数+1
                                $department_model->staff_amount = empty($department_model->staff_amount) ? 1 : $department_model->staff_amount+1;
                                if (!$department_model->save()) {
                                    throw new \Exception('建立员工关系失败：'.current($department_model->errors)[0]);
                                }
                            } else {
                                throw new \Exception('建立员工关系失败:'.current($userCompany_model->errors)[0]);
                            }
                        } else {
                            //如果是已删除的员工重新加入，则更该之前的记录状态即可
                            if ($userCompany_model->is_deleted ==1) {
                                //更新成为员工信息
                                $department_model = Department::find()->where(['company_id'=>$company_id,'name'=>trim($strs[0]),'is_deleted'=>0])->one();
    
                                $userCompany_model->department_id       = $department_model->id;
                                $userCompany_model->staff_name          = trim($strs[2]);
                                $userCompany_model->staff_mobile        = trim($strs[3]);
                                $userCompany_model->position_name       = trim($strs[1]);
                                $userCompany_model->staff_status        = 1;    //是否在职//0 离职，1 在职
                                $userCompany_model->is_deleted          = 0;    //是否删除 //0 否,1是
                                $userCompany_model->created_at          = time();
                                if ($userCompany_model->save()) {
                                    //部门人数+1
                                    $department_model->staff_amount = empty($department_model->staff_amount) ? 1 : $department_model->staff_amount+1;
                                    if (!$department_model->save()) {
                                        throw new \Exception('建立员工关系失败:'.current($department_model->errors)[0]);
                                    }
                                } else {
                                    throw new \Exception('建立员工关系失败:'.current($userCompany_model->errors)[0]);
                                }
                            }
                        }
                    }
                    
                    $successdata[] = array(
                        '部门'        =>  $strs[0],
                        '岗位'        =>  $strs[1],
                        '姓名'        =>  $strs[2],
                        '手机号码'     =>	 $strs[3],
                    );

    
                } catch (\Exception $e) {
                    //stepEnd：保存导入失败的记录
                    $faildata[] = array(
                        '部门'        =>  $strs[0],
                        '岗位'        =>  $strs[1],
                        '姓名'        =>  $strs[2],
                        '手机号码'     =>	 $strs[3],
                        '错误原因'     =>  $e->getMessage(),
                    );
                    continue;
                }
            }

            //如果有导入失败的则导出成excel
            if (count($faildata)>0) {
                $file_name = 'faile_import'.time().'.xls';
                $path = $base_path.$file_name;
                exportExcel($title,$headNames,$faildata,2,$path);
                $back['data']['file_path']   = Yii::$app->urlManagerStorage->createAbsoluteUrl('source/user_company_import/'.$file_name);
            } else {
                $back['data']['file_path'] = '';
            }
            $back['status']  = 1;
            $back['url']     = Url::to(['user-company/import']);
            $back['message'] = '上传成功';
            $back['data']['success_num'] = $highestRow-1-count($faildata);
            $back['data']['faile_num']   = count($faildata);
            return $back;
        } else {
            $back['message'] = '上传失败';
            return $back;
        }
    }
    
    /**
     * 批量分配角色
     * @param array $userIds
     * @param array $roleIds
     */
    public function actionBatchRole () {
        $back = ['status'=>0,'message'=>'','url'=>'','data'=>''];
        $company_id = Yii::$app->user->getCompanyId();
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            //开启事务
            $trans = Yii::$app->db->beginTransaction();
            $postdata = Yii::$app->request->post();

            foreach ($postdata['userIds'] as $k=>$v) {
                $model = UserCompany::findOne($v);
                $model->roleIds = $postdata['roleIds'];
                $rs = AuthAssign::create($model->user_id, $postdata['roleIds']);
                if (!$rs) {
                    throw new ServerErrorHttpException('分配失败'.current($model->errors)[0]);
                }
            }
            $trans->commit();
            $back['status'] = '1';
            $back['url'] = Url::to(['user-company/index']);
        } catch (\Exception $e) {
            $trans->rollback();
            $back['message'] = $e->getMessage();
        }
        
        return $back;
    }

    public function actionRenew(){
        return $this->render('renew');
    }
    
    //生成唯一微叮号
    private function checkUsername () {
        $username = randStr ( 10, false );//随机生成微叮名称
        $is_exist_user = User::find()->where(['username'=>$username])->one();
        if ($is_exist_user) {
            $this->checkUsername();
        } else {
            return $username;
        }
    }
    
}
