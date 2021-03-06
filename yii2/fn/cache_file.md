# cache
## 常用操作 

[代码](cache_file/DemoCacheController.php)  

### 组件配置 yii\caching\FileCache
```php
'components' => [
	...
    'cache'=>[
        'class' => 'yii\caching\FileCache',
    ],
...
]

```

### 基本的保存与写入
```php
class DemoCacheController extends Controller {
	 public $cache;

    public function init() {
        parent::init(); // TODO: Change the autogenerated stub
        $this->cache = \Yii::$app->cache;
    }


    public function actionIndex() {
        $id = 456;
      $cache_key =  md5('cache'.$id);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello world2332!';
            $res = $this->cache->set($cache_key, $str);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }

    /**
     * http://ysk.dev/admin/demo-cache/cache1
     * 缓存简单的读取与保存,没有设置时间
     * @author cmk
     */
    public function actionCache1() {
        $id = 456;
      $cache_key =  md5('cache'.$id);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello world2332!';
            $res = $this->cache->set($cache_key, $str);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }

    /**
     * http://ysk.dev/admin/demo-cache/cache2
     * 缓存简单的读取与保存,增加缓存时间
     * @author cmk
     */
    public function actionCache2() {
        $id = 456;
        $cache_key =  md5('cache'.$id);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello world23322222!';
            $res = $this->cache->set($cache_key, $str,10);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }

}
```


### 清空缓存
```php
    /**
     * http://ysk.dev/admin/demo-cache/flush
     * 清除缓存
     * @author cmk
     */
    public function actionFlush() {
        $res = $this->cache->flush();
        if ($res) {
            echo '成功清除';
        } else {
            echo '清除失败';
        }
    }

```

## 依赖操作

### 文件
```php
    /**http://ysk.dev/admin/demo-cache/file-dep
     * 文件依赖,缓存有期时间内,如果/web/h2.txt 文件的时候有变化,则缓存马上失效
     *  条件:在当前应用下 /web/目录/建立一个文件 如 hw.txt文件,只要这个文件的时间一变,则该缓存就失败
     * @author cmk
     */
    public function actionFileDep(){
        $id = 456;
        $cache_key =  md5('cache'.$id);
        $dependency = new FileDependency(['fileName'=>'hw.txt']);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello aaaa!';
            $res = $this->cache->set($cache_key, $str,3000,$dependency);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }
```

### 表达式
```php
    /**http://ysk.dev/admin/demo-cache/reg-dep?name=123
     * 表达式依赖: 当参数name改变的时候,缓存马上清空
     * @author cmk
     */
    public function actionRegDep(){
        $id = 456;
        $cache_key =  md5('cache'.$id);

        $dependency = new ExpressionDependency(['expression'=>' \Yii::$app->request->get("name")']);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello aaaabbbb!';
            $res = $this->cache->set($cache_key, $str,3000,$dependency);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }
```

### DB
```php
    /**http://ysk.dev/admin/demo-cache/db-dep
     * DB依赖: 绑定的数据库表里有变化,缓存才会清除
     * @author cmk
     */
    public function actionDbDep(){
        $id = 456;
        $cache_key =  md5('cache'.$id);

        $dependency = new DbDependency(['sql'=>'select count(*) from system_log']);
        if (!$data =  $this->cache->get($cache_key)) {
            $str = 'hello aaaabbbbccc!';
            $res = $this->cache->set($cache_key, $str,3000,$dependency);
            if($res){
                $data =$str;
            }
        }
        echo $data;
    }
```

## tag
**vendor/mdmsoft/yii2-admin/models/Route.php**

```php
const CACHE_TAG = 'mdm.admin.route';

  public function getAppRoutes($module = null) {
        if ($module === null) {
            $module = Yii::$app;
        } elseif (is_string($module)) {
            $module = Yii::$app->getModule($module);
        }
        //dp(__METHOD__);  //mdm\admin\models\Route::getAppRoutes
        // dp($module->getUniqueId()); // ''
        $key = [__METHOD__, $module->getUniqueId()];
        /**
         * dp($key);
         * array (size=2)
         * 0 => string 'mdm\admin\models\Route::getAppRoutes' (length=36)
         * 1 => string '' (length=0)
         */
        //
        dp($module->getModules());
        $cache = Configs::instance()->cache;
        if ($cache === null || ($result = $cache->get($key)) === false) {
            $result = [];
            $this->getRouteRecrusive($module, $result);
            if ($cache !== null) {
                $cache->set($key, $result, Configs::instance()->cacheDuration, new TagDependency([
                    'tags' => self::CACHE_TAG,
                ]));
            }
        }

        return $result;
    }
```

#页面缓存 [view层]

## beginCache
```php

//缓存时间
$duration = 15;

?>

<?php if ($this->beginCache('cache-div', ['duration' => $duration])) { ?>
    <div id="cache-div">
        <div>这里待会缓存</div>
    </div>

    <?php
 $this->endCache();
}
?>

```
### 缓存时间
```php

//缓存依赖
$Dependency = [
    'class' => 'yii\caching\FileDependency',
    'fileName' => 'hw.txt',
];

?>

<?php if ($this->beginCache('cache-div', ['dependency' => $Dependency])) { ?>
    <div id="cache-div">
        <div>这里待会缓存</div>
    </div>

    <?php
 $this->endCache();
}
?>

```

### 缓存开关
```php
//缓存开关
$enabled = false;

?>

<?php if ($this->beginCache('cache-div', ['enabled' => $enabled])) { ?>
    <div id="cache-div">
        <div>这里待会缓存</div>
    </div>

    <?php
 $this->endCache();
}
?>

```

### 嵌套缓存
```php

<?php if ($this->beginCache('cache-out-div', ['duration' => 20])) { ?>
    <div id="cache-out-div">
        <div>这里是外层x</div>

        <?php if ($this->beginCache('cache-in-div', ['duration' => 1])) { ?>
        <div id="cache-in-div">
            <div>这是内层yy</div>
        </div>

        <?php
            $this->endCache();
        } ?>
    </div>

    <?php
    $this->endCache();
}
?>

```
## 页面缓存 [controller层]
### 使用behavior进行全局缓存
```php
class DemoCacheController extends Controller {

    public $cache;

    public function behaviors() {
      return [
            [
                'class'=>'yii\filters\PageCache',
                'only'=>['index'], //只限index方法有效,如果该注释掉,则全部的方法有效
                'duration'=>1000,
                'dependency'=>[
                    'class'=>'yii\caching\FileDependency',
                    'fileName'=>'hw.txt'
                ]
            ]
      ];
    }

    /**
     * http://ysk.dev/admin/demo-cache/page-cache
     * 使用behavior进行全局缓存
     * @author cmk
     */
    public function actionPageCache(){
        echo 'abc';
    }
```

### http缓存

**httpcahe要缓存更新两个要素**
>lastModified 控制文件时间  
>etagSeed 控制文件内容  
>上面两个同时改变的话，缓存才清空  

```php
class DemoCacheController extends Controller {

  public function behaviors() {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'lastModified' => function () {
                    return filemtime('hw.txt');
                },
                'etagSeed' => function () {
                    $fp = fopen('hw.txt', 'r');
                    $title = fgets($fp); //采集第一行作为内容的变化的依据,考虑到文档太大了原因
                    fclose($fp);
                    return $title;
                },
            ],
        ];
    }

    /**
     * http://ysk.dev/admin/demo-cache/http-cache
     * 使用behavior定义,使用httpCache缓存，lastModified与etagSeed 同时变化，缓存才清空
     * @author cmk
     */
    public function actionHttpCache() {
        $content = file_get_contents('hw.txt');
        return $this->renderPartial('http-cache', ['content' => $content]);
    }
```