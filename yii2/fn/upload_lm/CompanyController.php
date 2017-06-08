<?php
namespace rest\versions\v1\service\controllers;
use Yii;
use company\models\User;//会员用户表
use rest\versions\v1\service\models\UserF;
use company\models\Account;//会员资产表
use rest\versions\v1\service\models\UserProfilev;//会员基本信息表
use rest\versions\v1\service\models\CompanyJy;//会员公司表
use rest\versions\v1\service\models\CusSupService;//向关联表插入数据

use yii\web\UploadedFile;
use yii\web\BadRequestHttpException;
use ihacklog\sms\models\Sms;

class CompanyController extends BaseController
{
    
    /**
     * 添加供应商
     * @param $post [营业执照图片 thumbnail_path； 营业执照号 business_license；   payment_term 账期、rebate 返利百分比；contract_img[] 合同图片；name 企业名称；法人代表 delegate_name；法人代表身份证号  delegate_idcard；所在城市 city_name；详细地址 address；企业联系人 contact_name；企业联系人手机号 contact_phone]
     * @return bool
     */
//    public $enableCsrfValidation = false;
    public function actionCreate(){
        $post=Yii::$app->request->post();
        $post["thumbnail_path"]= json_decode($post["thumbnail_path"],true);
        $post["contract_img"]= json_decode($post["contract_img"],true);
        $trance=Yii::$app->db->beginTransaction();
        $uid=$this->_insetUser($post);//定义注册向user表中插入数据
        $account=$this->_insertAccount($uid);//定义向account表中插入数据
        $profile=$this->_insertProfile($post, $uid);//向profile表插入数据
        $company=$this->_insertCompany($post, $uid);//像company表插入数据
        $cusSupService = new  CusSupService();
        $cussupResult=$cusSupService->addCusSup($company,$uid,$post);//向cussup表插入数据
        if($uid&&$account&&$profile&&$company&&$cussupResult){
            $trance->commit();//此后需要发送短信
            $sms = new Sms();
            $sms->sendNotice(Yii::$app->request->post('contact_phone'), [''], 1111);
            return true;
        }
        else{
            $trance->rollBack();
            throw new BadRequestHttpException("添加失败");
        }
        
    }
    //定义需要注册方面的数据
    private function _dataSigup($post){
        $arr['UserF']=[
            "username"=>"user".$post['contact_phone'],
            "mobile"=>$post['contact_phone'],
            "password"=>substr($post['contact_phone'],5),
            "user_type"=>1
        ];
        return $arr;
    }
    //定义注册向user表中插入数据
    private function _insetUser($post){
        $user = new UserF();
        $time=time();//获取当前时间
        $query=$this->_dataSigup($post);
        if($user->load($query)&&$user->validate()){
            $user->username = "user".$post['contact_phone'];
            $user->mobile = $post['contact_phone'];
            $user->status = 2;
            $user->user_type =1;
            $user->created_at=$time;
            $user->updated_at=$time;
            $user->setPassword(substr($post['contact_phone'],5));
            if($user->save(false)){return $user->attributes["id"];}
            else{throw new BadRequestHttpException("添加用户失败");}
        }else{throw new BadRequestHttpException("添加用户失败");}
    }
    //定义向账户表插入一条数据
    private function _insertAccount($id){
        $account=new Account();
        $time=time();
        $account->id=$id;
        $account->balance=0.00;
        $account->frozen=0.00;
        $account->reward=0.00;
        $account->created_at=$time;
        $account->updated_at=$time;
        return $account->save();
    }
    //定义想user-profile表插入数据
    private function _insertProfile($post,$id){
        $data=$this->_dataProfile($post, $id);//处理为可以被user_profile表使用的数据
        $profile=new UserProfilev();
        if($profile->load($data)&&$profile->validate())
        {
            $profile->locale=Yii::$app->language;
            return $profile->save(false);
        }else{throw new BadRequestHttpException("添加用户属性失败");}
        
        
    }
    //定义组合profile中的数据
    private function _dataProfile($post,$id){
        $time=time();
        $arr['UserProfilev']=[
            "user_id"=>$id,
            "real_name"=>$post['name'],
            "city_id"=>$post['city_name'],
            "address"=>$post['address'],
            "avatar_base_url"=>"",
            "avatar_path"=>"",
            "created_at"=>$time,
            "updated_at"=>$time
        ];
        return $arr;
    }
    //定义写入表company
    public function _insertCompany($post, $id){
        $data=$this->_dataCompany($post, $id);
        $company=new CompanyJy();
        if($company->load($data)&&$company->validate())
        {
            if($company->save(false)){return $company->attributes["id"];}
            else{throw new BadRequestHttpException("添加公司失败");}
        }else{throw new BadRequestHttpException("添加公司失败");}
    }
    //定义组合company表的数据
    private function _dataCompany($post,$id){
        $time=time();
        $arr["CompanyJy"]=[
            "name"=>$post['name'],
            "user_id"=>$id,
            "city_name"=>$post['city_name'],
            "address"=>$post['address'],
            "contact_name"=>$post["contact_name"],
            "contact_phone"=>$post["contact_phone"],
            "delegate_name"=>$post['delegate_name'],
            "delegate_idcard"=>$post["delegate_idcard"],
            "business_license"=>$post["business_license"],
            "auditor_id"=>1,
            "audit_status"=>2,//默认审核通过
            "audit_at"=>$time,
            "audit_remark"=>"交运添加供应商自动审核通过",
            "request_by"=>$id,
            "request_at"=>$time,
            "created_at"=>$time,
            "updated_at"=>$time,
            "thumbnail_base_url"=>$post['thumbnail_path']['base_url'],//营业执照网址基本
            "thumbnail_path"=>$post['thumbnail_path']['path'],//营业执照路径
        ];
        return $arr;
    }
    //图片上传接口,单个图片上传
    public function actionUpload(){
        $model=new \rest\versions\v1\service\models\Upload();
        $model->upFile = UploadedFile::getInstance($model,'upFile');
        $upload=$model->upload();
        if($upload['status']){return $upload['data'];}
        else{
            throw new BadRequestHttpException($upload['msg']);
        }
    }
    
    
}
