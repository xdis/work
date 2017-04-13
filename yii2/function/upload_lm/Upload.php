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

