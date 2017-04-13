# 上传

## API通用上传接口_每次仅上传一个_lm


- 获取文件图片接口

**请求URL：** 
- `/product-line/file-storage?id=13&file_mode=0&file_type=9`
  
**请求方式：**
- GET 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|id |否  |int |主键  |
|file_mode |是  |int |文件格式//0 图片，1 附件，2 音频，3 视频  默认为0  |
|file_type |否  |int |文件类型//1=司机认证身份证正面照 关联表(sj_driver),2=司机认证身份证反面照 关联表(sj_driver) ,3=驾驶证正面照片 关联表(sj_driver),4=驾驶证副面照片 关联表(sj_driver),5=行驶证正面照片 关联表(sj_car) ,6=行驶证副面照片 关联表(sj_car),7=车辆相关图片 关联表(sj_car),8=车辆营运证 关联表(sj_car), 9 = 产品图片 关联(product) , 10 = 供应商资质图片 关联（supplier_app）,11 = 行程相关图片 关联表(sj_order)|

**返回示例**

``` 
{
  "status": 1,
  "message": "操作成功",
  "url": "",
  "data": [
    {
      "id": 279,
      "file_mode": 0,
      "file_type": "9",
      "relation_id": "13",
      "base_url": "http://storage.v2.vding.wang/source",
      "path": "1/9gHuVYeF78BFxw9FSdFkTuo20LwM9V8J.jpg",
      "size": 8931,
      "name": "t01e966f72d3f358db8.jpg",
      "upload_ip": null,
      "display_order": 1,
      "is_cover": 0,
      "created_at": 1484056307,
      "updated_at": 1484056307
    }
  ]
}
```

 **返回参数说明** 

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|id |int   |id，主键  |
|file_mode |int   |文件格式//0 图片，1 附件，2 音频，3 视频 |
|file_type |string   |文件类型//1=司机认证身份证正面照 关联表(sj_driver),2=司机认证身份证反面照 关联表(sj_driver) ,3=驾驶证正面照片 关联表(sj_driver),4=驾驶证副面照片 关联表(sj_driver),5=行驶证正面照片 关联表(sj_car) ,6=行驶证副面照片 关联表(sj_car),7=车辆相关图片 关联表(sj_car),8=车辆营运证 关联表(sj_car), 9 = 产品图片 关联(product) , 10 = 供应商资质图片 关联（supplier_app）,11 = 行程相关图片 关联表(sj_order)|
|relation_id |string   |关联id|
|base_url |int   |服务器地址  |
|path |int   |文件相对路径  |
|size |int   |文件大小  |
|name |string   |文件名称  |
|upload_ip |int   |上传ip |
|display_order |int   |排序 |
|is_cover |string   | 是否设为封面// 0 否，1 是|
|created_at |string   | 上传时间|
|updated_at |string   | 更新时间|


## 控制器
rest/versions/v1/service/controllers/CompanyController.php  
[源码](upload_lm/CompanyController.php#L149-L158) 
```php
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

```

## model_upload

```php
<?php
namespace rest\versions\v1\service\models;
use yii\base\Model;
use Yii;
class Upload extends Model
{
    public $upFile;//需要上传的图片
    
    public function rules(){
        return [
            ["upFile","file"]
        ];
    }
    
    public function upload()      
    {  
        if ($this->validate())  
        {  
            $rootPath = "../../storage/web/source/1/"; //定义上传的根目录  
            $ext = $this->upFile->extension;   //获取文件的后缀(*格式*)  
            $randName = time() . rand(1000, 9999) . "." . $ext; //重新编译文件名称 
            $path=date("Ym");
            $rootPath = $rootPath . $path . "/";    //拼接  
            if (!file_exists($rootPath)){   //判断该目录是否存在  
                mkdir($rootPath,0777,true);  
            }  
            $re = $this->upFile->saveAs($rootPath . $randName);        //调用内置封装类**执行上传  
            if($re){  
                return ["status"=>true,"data"=>["path"=>"1/".date("Ym")."/". $randName,
                    "base_url"=> Yii::$app->urlManagerStorage->createAbsoluteUrl('/source'),
                    "size"=>$this->upFile->size,
                    "name"=>$this->upFile->name
                    ]] ;   //上传成功**返回文件的路径名称  
            }else{  
                return ["status"=>false,"msg"=>"文件上传失败"];    
            }  
        }  
        else  
        {return ["status"=>false,"msg"=>"文件检测失败"];}  
    }  
}


```