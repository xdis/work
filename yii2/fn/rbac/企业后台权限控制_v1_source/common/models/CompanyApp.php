<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\validators\IDCardValidator;
use common\validators\PhoneValidator;
use company\modules\lookup\models\Lookup;
use common\models\Company;
use common\models\User;

/**
 * This is the model class for table "company_app".
 *
 * @property integer $id
 * @property integer $company_id
 * @property integer $industry_app_id
 * @property string $app_path
 * @property integer $level
 * @property integer $audit_status
 * @property integer $passed_time
 * @property integer $is_enabled
 * @property integer $auditor_id
 * @property integer $audit_at
 * @property string $audit_remark
 * @property integer $request_by
 * @property integer $request_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $thumbnail_base_url
 * @property string $thumbnail_path
 * @property string $thumbnail_new_base_url
 * @property string $thumbnail_new_path
 *
 * @property User $auditor
 * @property User $requestBy
 * @property Company $company
 */
class CompanyApp extends \common\models\BaseCompanyModel
{
	const STATUS_ADD		= 1;//申请应用，
	const STATUS_EDIT		= 2;//更改资料，
	const STATUS_PASS 		= 3;//通过审核  ，
	
	public $thumbnail = '';
	public $attachments = '';
	
	
	//应用操作类型
	static $record_sel = array(
		self::STATUS_ADD => '申请应用',
		self::STATUS_EDIT => '更改资料'	,
		self::STATUS_PASS => '通过审核'	,
	);
	
	
	//景点级别静态数组
	static $ticket_sel = array(
		1 => array('id'=>1 , 'title'=> '无'),
		2 => array('id'=>2 , 'title'=> '5A'),
		3 => array('id'=>3 , 'title'=> '4A'),
		4 => array('id'=>4 , 'title'=> '3A'),
		5 => array('id'=>5 , 'title'=> '2A'),
		6 => array('id'=>6 , 'title'=> '1A'),
	);
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company_app';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
            TimestampBehavior::className(),
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'thumbnail',
                'pathAttribute' => 'thumbnail_path',
                'baseUrlAttribute' => 'thumbnail_base_url'
            ],
    		[
    			'class' => UploadBehavior::className(),
    			'attribute' => 'attachments',
    			'multiple' => true,
    			'uploadRelation' => 'fileStorages',
    			'pathAttribute' => 'path',
    			'baseUrlAttribute' => 'base_url',
    			'sizeAttribute' => 'size',
    			'nameAttribute' => 'name',
    			//                'file_mode'=>1,
    			//'file_type'=>10
    		],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'industry_app_id' ,'thumbnail'], 'required'],
            [['company_id', 'industry_app_id', 'level', 'audit_status', 'passed_time', 'is_enabled', 'auditor_id', 'audit_at', 'request_by', 'request_at', 'created_at', 'updated_at'], 'integer'],
            [['description', 'advance_notice', 'traffic_guidance'], 'string'],
           	[['name', 'area'], 'string', 'max' => 128],
           	[['app_path', 'opening_time'], 'string', 'max' => 512],
        	[['attachments'], 'safe'],
           	[['introduction'], 'string', 'max' => 2048],
           	[['address', 'audit_remark'], 'string', 'max' => 256],
           	[['thumbnail_base_url', 'thumbnail_path', 'thumbnail_new_base_url', 'thumbnail_new_path'], 'string', 'max' => 1024],[['auditor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['auditor_id' => 'id']],
            [['request_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['request_by' => 'id']],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        	'thumbnail' => '资质图片',
        	'new_thumbnail' => '资质图片',
            'company_id' => '企业id//company(company_id)',
            'industry_app_id' => '企业开通的应用//读lookup表 type = industry_appid 的值',
        	'area' => '商圈',
            'app_path' => '行业资质图片//弃用',
            'level' => '级别',
        	'introduction' => '摘要',//简介 ,景区摘要,
        	'address' => '详细地址 ',//景区地址,
        	'opening_time' => '营业时间',
        	'description' => ' ',//介绍 景区介绍,
        	'advance_notice' => ' ',
        	'traffic_guidance' => ' ',
            'audit_status' => '认证状态//1待审核，2 审核通过，3 审核未通过',
            'passed_time' => '认证通过次数//每次通过后才累加',
            'is_enabled' => '是否启用//0 未启用，1 启用',
            'auditor_id' => '审核人//user(user_id)',
            'audit_at' => '审核时间',
            'audit_remark' => '审核备注',
            'request_by' => '申请人//user(user_id)',
            'request_at' => '申请时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'thumbnail_base_url' => '封面',
            'thumbnail_path' => '封面',
            'thumbnail_new_base_url' => '封面//新的资质图片',
            'thumbnail_new_path' => '封面//新的资质图片',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(User::className(), ['id' => 'auditor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestBy()
    {
        return $this->hasOne(User::className(), ['id' => 'request_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }
    
    /**
     * [getUserInfo 获取用户信息]
     * @param  [type] $uId [description]
     * @return [type]      [description]
     */
    public function getUserInfo($uId){
    	return User::find()->where(['id'=>$uId])->one();
    }
    /**
     * [getCompanyInfo 获取公司信息]
     * @param  [type] $cId [description]
     * @return [type]      [description]
     */
    public function getCompanyInfo($cId){
    	$rs = Company::find()->where(['id'=>$cId])->one();
    	return $rs;
    }
    /**
     * [获取lookup单条数据信息]
     * @return [type] [description]
     */
    public function getLookup($code,$type)
    {
    	$rs = Lookup::find()->where(['code'=>$code,'type'=>$type])->one();
    	return $rs;
    }
    /**
     * [getLevel获取等级名称]
     * @return [type] [description]
     */
    public function getLevel($level,$aId){
    	if($aId == 4){
    		$type = 'scenic_spot_level';
    	}else if($aId == 2){
    		$type = 'hotel_level';
    	}
    	$rs = Lookup::find()->where(['code'=>$level,'type'=>$type])->one();
    	return $rs;
    }
    /**
     * [getOPenApp 获取开通的应用列表]
     * @return [type] [description]
     */
    public function getOPenApp($cId){
    	$openApp  = CompanyApp::find()->where(['company_id'=>$cId,'audit_status'=>2])
    	->select(['id','industry_app_id'])
    	->asarray()
    	->all();
    	if(!is_null($openApp)){
    		foreach ($openApp as $key => $value) {
    			$openApp[$key]['lookup'] = Lookup::find()->where(['code'=>$value['industry_app_id'],'type'=>'industry_appid'])->select(['name'])->asarray()->one();
    		}
    	}
    	return $openApp;
    }
    /**
     * [getOPenApp 获取申请中的应用]
     * 只有单条
     * @return [type] [description]
     */
    public function getApplyApp($cId){
    	$applyApp  = CompanyApp::find()
    	->where(['company_id'=>$cId])
    	->andwhere(['or', 'audit_status = 1', 'audit_status = 3'])
    	->select(['id','industry_app_id','audit_status'])
    	->asarray()
    	->one();
    	if(!is_null($applyApp)){
    		$applyApp['lookup'] = Lookup::find()->where(['code'=>$applyApp['industry_app_id'],'type'=>'industry_appid'])->select(['name'])->asarray()->one();
    	}
    	return $applyApp;
    }
    
    public static function hasApp($companyId, $appId)
    {
    	$cnt = CompanyApp::find()->where(['company_id' => $companyId, 'industry_app_id' => $appId, 'audit_status' => 2])
    	->count();
    	return $cnt > 0;
    }
    
    /**
     * 新增企业应用操作记录
     * @param unknown $company_id  
     * @param unknown $user_id
     * @param unknown $relation_id
     * @param unknown $name
     * @return intval $id  
     */
    public function addOptlog($company_id , $user_id , $relation_id , $name){
    	$model = new OperationLog();
    	$model->company_id = $company_id;
    	$model->user_id = $user_id;
    	$model->relation_type = 7;
    	$model->relation_id = $relation_id;
    	$model->name = $name;
    	$model->created_at = time();
    	$model->save();
    	$id = $this->attributes['id'];
    	return $id;
    }
    
    /**
     * 获取应用操作日志类型
     */
    public function getCompanyAppReType($key){
    	$arr = self::$record_sel;
    	return $arr[$key];
    }
    
    
    /**
     * 获取酒店下拉数据
     */
    static function getHotelsel(){
    	return Lookup::find()->where(['type'=>'hotel_level'])->select('name,code')->orderBy('code DESC')->asArray()->all();
    }
    
    /**
     * 获取景区下拉数据
     */
    static function getScenicsel(){
    	return self::$ticket_sel;
    }
    
    /**
     * 获取应用等级名称
     * @param unknown $type  应用类型
     * @param unknown $lv    等级
     */
    public function getLvname($code , $lv){
    	switch ($code) {
    		case 2:
    			$arr = Lookup::find()->where(['type'=>'hotel_level' , 'code'=>$lv])->select('name')->one();
    			return empty($arr)?'':$arr->name;
    			break;
    		case 4:
    			$arr = self::$ticket_sel;
    			return $arr[$lv]['title'];
    		break;
    		
    		default:
    			return '无';
    		break;
    	}
    }
    
    /**
     * 获取应用等级名称
     */
    public function getLvlabername($code){
    	//酒店
    	switch ($code) {
    		case 2:
    			return '酒店星级';
    			break;
    		case 4:
    			return '景区级别';
    			break;
    		default:
    			return '';
    			break;
    		
    	}
    }
    
    /** 附件上传图片 多张
     * @return \yii\db\ActiveQuery
     * @author cmk
     */
    public function getFileStorages()
    {
    	return $this->hasMany(FileStorage::className(), ['relation_id' => 'id'])->where(['file_type'=>13]);
    }
}

