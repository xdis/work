<?php

namespace company\controllers;

use common\models\OperationLog;
use company\models\AuthAssign;
use eleiva\noty\Noty;
use Yii;
use common\models\Account;
use company\models\UserCompany;
use common\controllers\BaseController;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use moonland\phpexcel\Excel;
use yii\web\UploadedFile;
use company\models\UserCompanyImport;
use company\models\Department;
use company\models\User;
use company\models\SignupForm;
use common\models\Company;
use common\models\CompanyApp;
use common\models\UserProfile;
use common\models\DpStore;
use yii\web\Response;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\db\Expression;
use yii\helpers\arrayHelper;

/*
 * 基础模块 -- 子账号管理 -- 员工管理
 */
class StaffManageController extends BaseController
{
    public $enableCsrfValidation = false;

    public $layout = "public";

    public $new_staff_user_id = 0;


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

    /*
     * @API 子账号管理 -- 员工管理 -- 查看列表
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionList()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('index');
        }
        $company_id = Yii::$app->user->getCompanyId();
        $page_index = Yii::$app->request->post('page_index',1);
        $page_size = Yii::$app->request->post('page_size',5);
        $keyword = Yii::$app->request->post('keyword','');
        $status = Yii::$app->request->post('status','');
        $page_offset = ($page_index-1)*$page_size;
        $expr = new Expression('S.id,S.staff_name,S.staff_no,
                S.staff_mobile,D.name AS staff_department,group_concat(A.auth_item_name) AS staff_role,S.is_activated AS staff_status');
        $query = (new Yii\db\Query())->select($expr)
                ->from('user_company S')
                ->leftJoin('department D','S.department_id=D.id')
                ->leftJoin('auth_assign A','A.company_id=S.company_id AND A.user_id=S.user_id')
                ->where(['S.company_id'=>$company_id]);
        if($keyword!=''){
            $query->andFilterWhere(['or',['like','S.staff_mobile',$keyword],
                ['like','S.staff_name',$keyword],
                ['like','D.name',$keyword]]);
        }
        if($status==1||$status==0){
            $query->andFilterWhere(['S.is_activated'=>$status]);
        }
        $total_num = $query->groupBy('S.staff_no')->count();
        $staff = $query->groupBy('S.staff_no')->orderBy('S.staff_no')->offset($page_offset)->limit($page_size)->all();
        /*foreach($staff as $k=>$v){

        }*/
        $data = ['total_num'=>$total_num,'staff'=>$staff];
        return $this->ajaxSuccess('','',$data);
    }

    /*
     * @API 子账号管理 -- 员工管理 -- 员工详情
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionView()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('view',[]);
        }
        $company_id = Yii::$app->user->getCompanyId();
        $id = Yii::$app->request->post('id');
        $model = UserCompany::findOne(['id'=>$id,'company_id'=>$company_id]);
        //$model = UserCompany::findOne(['id'=>$id]);
        if(is_null($model)){
            return $this->ajaxFail('此员工不存在','','');
        }
        $staff = (new \yii\db\Query())->select('S.staff_name,S.staff_no,S.is_activated staff_status,
                 S.staff_mobile,D.name department,U.staff_name handover_name')
                ->from('user_company S')
                ->innerJoin('department D','S.department_id = D.id')
                ->leftJoin('user_company U','S.handover_id = U.id')
                ->where(['S.id'=>$id])->one();
        $roles = AuthAssign::find()->select('auth_item_name role_name,auth_item_id as role_id')
                ->where(['company_id'=>$company_id,'user_id'=>$model->user_id])->asArray()->all();
        $unix_time = new Expression('FROM_UNIXTIME(O.created_at,"%Y-%m-%d %H:%i:%S") AS created_at,
                            U.username operator,O.name action,O.memo comment');
        $query  = (new \yii\db\Query())->select($unix_time)
                ->from('operation_log O')
                ->leftJoin('User U','U.id=O.user_id')
                ->where(['O.company_id'=>$company_id,'O.relation_type'=>12,'relation_id'=>$id]);
        $logs = $query->offset(0)->limit(5)->all();
        $total_logs = $query->count('O.id');
        $o=(object)($staff);
        if(is_null($roles)){
            $o->roles = '';
            $o->role_ids = [];
        }else {
            $role_col = arrayHelper::getColumn($roles, 'role_name');
            $o->roles = implode($role_col,',');
            $o->role_ids = arrayHelper::getColumn($roles,'role_id');
        }
        $o->operation_log = $logs;
        $o->total_log = $total_logs;
        return $this->ajaxSuccess('','',$o);
    }

    /*
     * @API 子账号管理 -- 员工管理 -- 操作记录
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionOperationLog()
    {
        $company_id = Yii::$app->user->getCompanyId();
        $id = Yii::$app->request->post('id');
        $page_index = Yii::$app->request->post('page_index',1);
        $page_size = Yii::$app->request->post('page_size',5);
        $page_offset = ($page_index-1)*$page_size;
        $query = (new yii\db\Query())->select('O.created_at,U.username operator,O.name action,O.memo comment')
            ->from('operation_log O')
            ->innerJoin('user U','O.user_id = U.id')
            ->where(['company_id'=>$company_id,'relation_id'=>$id,'relation_type'=>12]);
        $total = $query->count();
        $data = $query->limit($page_size)->offset($page_offset)->all();
        foreach($data as $k=>$v){
            if(is_null($v['comment'])){
                $data[$k]['comment'] = '';
            }
        }
        $o = [
            'total_log'=>$total,
            'operation_log'=>$data
        ];
        return $this->ajaxSuccess('','',$o);
    }
    /*
     * @API 子账号管理 -- 员工管理 -- 创建员工
     * @Author code lighter
     * @Date 2017-06-01
     */
    //系统自动产生个人微叮号
    public function gen_personal_vding_no(){
        $data = Yii::$app->getDb()->createCommand('select max(id) as maxid from user')->queryOne();
        if($data['maxid']<10000){
            $vding_no ='vd'.($data['maxid']+10000);
        }else{
            $vding_no ='vd'.($data['maxid']+1);
        }
        return $vding_no;
    }
    //判断用户是否被其他公司添加为员工？
    public function isStaffInOtherCompany($mobile,$company_id)
    {
        $model = UserCompany::find()->where(['staff_mobile'=>$mobile])
            ->andWhere(['<>','company_id',$company_id])->all();
        return $model?true:false;
    }
    //创建新的个人用户
    public function addNewUser($mobile){
        $o = new User;
        $o->isNewRecord = true;
        $o->mobile = $mobile;
        $o->username = $this->gen_personal_vding_no();
        $o->status = User::STATUS_ACTIVE; // 1 有效，2 禁用,3 删除
        if(!$o->save(false)){
            throw new Exception('创建个人用户失败!');
        };
        return true;
    }
    //注册个人用户
    public function registerVding($mobile,$company_id)
    {
        $user = User::findOne(['mobile'=>$mobile]); //手机号对应的个人vding用户存在
        if(!$user){ //
            $ret = $this->addNewUser($mobile);
            $user_id = Yii::$app->db->getLastInsertID();
            $this->new_staff_user_id = $user_id;
            $ret = $ret&& $this->createAccount($user_id);
            $ret = $ret&& $this->createUserProfile($user_id);
            return $ret;
        }
        $this->new_staff_user_id = $user->id;
        // user_type　1 企业账户 2 个人账户
        $account = User::findOne(['id'=>$user->id,'user_type'=>2]); //个人账户是否存在？
        if(!$account){
            return $this->createAccount($user->id,'',2,$company_id);
        }
        return true;
    }
    //account_type = 1 企业, 2 个人
    public function createAccount($uid,$vding_id ='', $account_type = 2,$company_id=0){
        $a = new Account();
        $time = time();
        $a->isNewRecord = true;
        $a->user_id  = $uid;
        $a->company_id = $company_id;
        $a->account_type  = $account_type;
        if($account_type == 1){
            $a->vd_no = $vding_id;
        }
        $a->created_at = $time;
        $a->updated_at = $time;
        $ret = $a->save(false);
        $ret?:$a->addError('error_msg','创建账户失败!');
        return $ret;
    }
    //创建用户个人资料
    public function createUserProfile($user_id)
    {
        $u = new UserProfile;
        $u->user_id = $user_id;
        $u->locale = 'zh-CN';
        $u->nickname='';
        $u->real_name='';
        $u->id_card='';
        $u->is_validated=0;
        $u->birthday=null;
        $u->city_id=null;
        $u->address='';
        $u->avatar_path = '';
        $u->avatar_base_url='';
        $u->gender=1;
        $u->note='';
        $u->created_at=time();
        $u->updated_at=time();
        $ret = $u->save(false);
        $ret?:$u->addError('user_id','添加员工资料失败!');
        return $ret;
    }

    public function actionCreate()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('create');
        }
        $company_id = Yii::$app->user->getCompanyId();
        $user_id = Yii::$app->user->id;
        $role_ids = Yii::$app->request->post('roleIds',[]);
//        if(count($role_ids)==0){
//            return $this->ajaxFail('请添加角色名称 !','','');
//        }

        $mobile = Yii::$app->request->post('staff_mobile','');
        //查看该员工是否已经离职
        $old_staff =UserCompany::findOne(['company_id'=>$company_id,'staff_mobile'=>$mobile]);
        if($old_staff){
            if($old_staff->is_activated==0){
                return $this->ajaxFail('离职员工不能再次添加 !','','');
            }else{
                return $this->ajaxFail('该员工已存在 !','','');
            }
        }
        $model = new UserCompany();
        $model->scenario = 'add_user_company';
        if ($model->load(Yii::$app->request->post(),'') && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $ret = $this->registerVding($mobile,$company_id);
                //保存员工信息
                $model->isNewRecord = true;
                $model->user_id = $this->new_staff_user_id;
                $model->company_id = $company_id;
                $model->staff_no = $this->getStaffNo($company_id);
                $model->is_activated = 1; //处于激活状态
                $model->staff_status = 1;
                $model->is_deleted = 0;
                $ret = $ret && $model->save();
                $staff_id = $model->insert_staff_id;
                //保存操作记录
                $log = new OperationLog;
                $log->isNewRecord = true;
                $log->company_id = $company_id;
                $log->user_id = $user_id;
                $log->relation_type = 12; //操作员工记录
                $log->relation_id = $staff_id;//员工ID
                $log->name = "创建账户";
                $ret = $ret && $log->save(false);
                //分配角色
                if($role_ids!='') {
                   AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
                    if ($model->roleIds) {
                        $ret = $ret &&AuthAssign::create($model->user_id, $model->roleIds);
                    }
                }
                if($ret) {
                    $transaction->commit();
                    return $this->ajaxSuccess('', '', ['id' => $staff_id]);
                }
                    $transaction->rollBack();
                    return $this->ajaxFail('添加员工失败!','','');
            }catch(\Exception $e) {
                $transaction->rollBack();
                return $this->ajaxFail($e->getMessage(),'','');
            }
        }
        $error = current($model->getFirstErrors());
        return $this->ajaxFail($error,'','');
    }
    /*
     * 获取员工号码
     * 账户ID生成规则 主账户id:xxxx 账户创建成功后，xxxx从1001开始，顺序生成
     */
    public function getStaffNo($company_id)
    {
        $count = UserCompany::find()->select('max(staff_no) max_no')
                ->where(['company_id'=>$company_id])->scalar();
        if($count){
            return $count+1;
        }
        return 1001;
    }
    /*
     * @API 子账号管理 -- 员工管理 -- 编辑员工
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionUpdate()
    {
        if(!Yii::$app->request->isPost){
            return $this->render('create',[]);
        }
        $company_id = Yii::$app->user->getCompanyId();
        $operator_id = Yii::$app->user->id;
        $r = Yii::$app->request;
        $new = new \stdClass();
        $new->id = $r->post('id');
        $new->staff_name = $r->post('staff_name');
        $new->staff_mobile = $r->post('staff_mobile');
        $new->department_id = $r->post('department_id');
        $new->roleIds = $r->post('roleIds',[]);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //编辑的员工是否存在？
            $model = UserCompany::findOne(['id' => $new->id, 'company_id' => $company_id]);
            $model->scenario = "update_user_company";
            if (is_null($model)) {
                throw new \Exception('员工不存在');
            }
            $old = clone $model;
            $model->department_id = $new->department_id;
            $model->roleIds = $new->roleIds;
            //判断员工是否是超级管理员
            if ($model->staff_no !=1000) {
                $model->staff_name = $new->staff_name;
            }
            //手机号是否可以修改
            if ($this->canStaffModifyMobile($model->staff_mobile)) {
                $model->staff_mobile = $new->staff_mobile;
            }
            $ret = $model->save();
            if (!$ret) {
                $error_msg = current($model->getFirstErrors());
                //throw new \Exception('修改员工失败');
                throw new \Exception($error_msg);
            }
            //添加修改记录
            $ret = $ret && $this->addModifyLog($old,$new,$operator_id);
            //修改员工角色
            $ret = $ret && $this->modifyStaffRoles($company_id,$model->user_id,$new->roleIds);
            if(!$ret){
                $transaction->rollBack();
                return $this->ajaxFail('修改员工信息失败','','');
            }
            $transaction->commit();
            return $this->ajaxSuccess('修改员工信息成功','','');
        }catch(\Exception $e){
            $transaction->rollBack();
            return $this->ajaxFail($e->getMessage(),'','');
        }
    }
    /*
     * @API 子账号管理 -- 员工管理 -- 获取角色
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionGetRoles()
    {
        $company_id = Yii::$app->user->getCompanyId();
        //是否有开通相应的应用
        $app_id = $this->getCompanyAppId($company_id);
        foreach($app_id as $k=>$v){
            if($v ==1)//地接应用去除地接应用的内置角色
            {
                unset($app_id[$k]);
            }
        }
        $data = (new yii\db\Query())->select('name as role_name, id as role_id')
            ->from('auth_item')
            ->where(['company_id'=>$company_id,'type'=>1])->all();
        $app_data = (new yii\db\Query())->select('name as role_name, id as role_id')
            ->from('auth_item')
            ->where(['company_id'=>0])
            ->andWhere(['type'=>1]) //只选择角色
            ->andWhere(['in','industry_appid',$app_id])->all();
        $data = array_merge($data,$app_data);
        return $this->ajaxSuccess('','',$data);
    }

    //车调应用是3, 地接应用是1, 基础模块是8
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

    /*
     * @helper function 修改员工角色
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function modifyStaffRoles($company_id,$staff_user_id,$newRoleId)
    {
        AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $staff_user_id]);
        $ret = true;
        if ($newRoleId) {
            $ret = $ret &&AuthAssign::create($staff_user_id, $newRoleId);
        }
        return $ret;
    }
    /*
     * @helper function 增加修改记录
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function addModifyLog($old,$new,$user_id)
    {
        $diff = $this->getModifyDiff($old,$new);
        if(is_null($diff)){ //没有做任何修改，直接返回
            return true;
        }
        $log = new OperationLog;
        $log->isNewRecord = true;
        $log->user_id =$user_id;
        $log->relation_type = 12; //操作员工记录
        $log->company_id = $old->company_id;
        $log->relation_id = $old->id;
        $log->name="修改员工信息";
        $log->memo=$diff;
        return $log->save(false);
    }
    public function addDimissionLog($id,$company_id,$operator_id){
        $log = new OperationLog;
        $log->isNewRecord = true;
        $log->user_id =$operator_id;
        $log->relation_type = 12; //操作员工记录
        $log->company_id = $company_id;
        $log->relation_id = $id;
        $log->name="离职";
        $log->memo='';
        return $log->save(false);
    }
    /*
     * @helper function 获取修改记录变更，没有修改就返回null
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function getModifyDiff($old,$new)
    {
        $o = [];
        if($old->staff_name !=$new->staff_name){
            $o[] = '员工姓名';
        }
        if(is_null($old->roleIds) && is_array($new->roleIds)){
            $o[] = '角色';
        }
        if(is_array($old->roleIds) && is_array($new->roleIds)) {
            foreach ($old->roleIds as $k => $v) {
                if (!array_key_exists($k, $new->roleIds)) {
                    $o[] = '角色';
                    break;
                }
            }
        }
        if($old->department_id != $new->department_id){
            $o[] = '部门';
        }
        //手机号码
        if($this->canStaffModifyMobile($old->staff_mobile) && $old->staff_mobile != $new->staff_mobile){
            $o[] = '手机号码';
        }
        if(count($o)) {
            return '修改' . implode($o, ',');
        }
        return null;
    }

    /*
     * @helper function 检测员工是否可以修改手机号码
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function canStaffModifyMobile($mobile){
        $user = User::findOne(['mobile'=>$mobile]);
        if($user && $user->status ==1){ // 1 禁用，2 有效,3 删除
            return true;
        }
        return false;
    }

    /*
     * @API 子账号管理 -- 部门管理 -- 获取部门
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionGetDepartment(){
        $company_id = Yii::$app->user->getCompanyId();
        $data = Department::find()->where(['is_deleted'=>0,'company_id'=>$company_id])->all();
        $filterCallback = __NAMESPACE__.'\StaffManageController::filterCallback';
        $tree = self::plainTree($data,$filterCallback);
        return $this->ajaxSuccess('','',$tree);
    }
    public static function filterCallback($src){
        $o['id'] = $src['id'];
        $o['parent_id'] = $src['parent_id'];
        $o['name'] = $src['name'];
        return $o;
    }

    public static function plainTree($array,$callback=null){
        $tree = [];
        foreach($array as $k=>$v){
            $tree[] = is_callable($callback)?call_user_func($callback,$v):$v;
        }
        return $tree;
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

    /*
     * 查询当前账号是子账号还是主账号
     */
    public function actionIsSuperUser()
    {
        $status = Yii::$app->user->isCompanySuperUser();
        return $this->ajaxSuccess('','',['is_super_user'=>$status]);
    }

    /*
     * @API 子账号管理 -- 部门管理 -- 员工离职
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionDimission()
    {
        $company_id = Yii::$app->user->getCompanyId();
        $id = Yii::$app->request->post('id');
        $handover_id = Yii::$app->request->post('handover_id',0);
        $staff = UserCompany::findOne(['id'=>$id,'company_id'=>$company_id]);
        if(is_null($staff)){
            return $this->ajaxFail('员工不存在','','');
        }
        if($staff->is_activated==0){
            return $this->ajaxFail('员工已离职','','');
        }
        //允许没有交接人
        if($handover_id!=0){
            $handover = UserCompany::findOne(['id'=>$handover_id,'company_id'=>$company_id,'is_deleted'=>0]);
            if(is_null($handover)){
                return $this->ajaxFail('交接人不存在','','');
            }
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($staff->load(Yii::$app->request->post(), '') && $staff->validate()) {
                $staff->staff_status = 0; //0 离职，1 在职
                $staff->is_activated = 0; //账户停用
                $staff->is_deleted = 1; //0 否，1 是
                $staff->handover_id = $handover_id==0?null:$handover_id;
                $ret = $staff->save(false);
                $ret = $ret&& $this->addDimissionLog($staff->id,$staff->company_id,Yii::$app->user->id);
                if (!$ret) {
                    $transaction->rollBack();
                    return $this->ajaxFail('操作失败', '', '');
                }
                $transaction->commit();
                return $this->ajaxSuccess('离职成功', '', '');
            }
        }catch(\Exception $e) {
            $transaction->rollBack();
            return $this->ajaxFail('离职失败', '', '');
        }
    }

    /*
     * @API 子账号管理 -- 离职 -- 搜索员工
     * @Author code lighter
     * @Date 2017-06-01
     */
    public function actionSearchStaff(){
        $company_id = Yii::$app->user->getCompanyId();
        $keyword = Yii::$app->request->post('keyword');
        $query = (new \yii\db\Query())->select('S.id,S.staff_name,D.name department')
                ->from('user_company S')
                ->innerJoin('Department D','S.department_id=D.id')
                ->where(['S.company_id'=>$company_id,'S.is_deleted'=>0]);
        if($keyword!=''){
            $query->andFilterWhere(['or',['like','S.staff_name',$keyword],
                ['like','D.name',$keyword]]);
        }
        $data =$query->all();
        return $this->ajaxSuccess('','',$data);
    }

    //检测交接人是否存在
    public function isHandOverExist($id,$company_id){
        $data = UserCompany::findOne(['id'=>$id,'company_id'=>$company_id]);
        if(is_null($data)){
            return false;
        }
        return true;
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
        $model->company_id = $company_id;
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
