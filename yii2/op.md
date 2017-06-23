# 常用
## 手动增加csrf

```php
<?php echo Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->csrfToken);?>
```

## layout

### 根据参数选择不同layout,如充值有个人和企业统一入口,不同身份不同layout
company/modules/admin/controllers/AlipayController.php  
```php
if (Yii::$app->user->getIsPerson()) {
    //个人充值
    Yii::$app->name = '个人中心';
    $this->layout = '@company/modules/ucenter/views/layouts/main.php';
} else {
    //企业充值
    Yii::$app->name = '企业管理后台';
    $this->layout = '@company/views/layouts/main.php';
}
```

### controller传值给layout
**在控制器中这样写**
```php
$this->view->params['customParam'] = 'customValue';
```

**在视图中这样调用**
```php
echo $this->params['customParam'];
```

## 发布线上
### 缓存清空
```php
//方法一:清空表结构缓存的方法
 
//flush all the schema cache
Yii::$app->db->schema->refresh();
 
//clear the particular table schema cache
Yii::$app->db->schema->refreshTableSchema($tableName);
 
 
//方法二:清空所有的缓存--不仅仅是mysql表结构
Yii::$app->cache->flush();
 
 
//方法三:使用 yii命令行的方式commond清除缓存
cache/flush                Flushes given cache components.
cache/flush-all            Flushes all caches registered in the system.
cache/flush-schema         Clears DB schema cache for a given connection component.
cache/index (default)      Lists the caches that can be flushed.
 
//执行 
./yii cache/flush-all
```

## 打印sql
```php
$query = Dporder::find()->select($select)->where(['cus_order_no' =>$cus_order_no]);
$commandQuery = clone $query;
echo $commandQuery->createCommand()->getRawSql();
```

---

# isAjax
## 权限添加的demo

**company/controllers/RouteController.php**
```php
 /**
 * 编辑
 * @param integer $id
 * @return mixed
 */
public function actionUpdate($id)
{
    $request = Yii::$app->request;
    $model = $this->findModel($id);       

    if($request->isAjax){
        /*
        *   Process for ajax request
        */
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($request->isGet){
            $look_data = Lookup::find()->where(['type'=>'industry_appid'])->asArray()->all();
            $industry_appid = ArrayHelper::map($look_data, 'code','name');
            return [
                'title'=> "编辑",
                'content'=>$this->renderAjax('update', [
                    'model' => $model,
                    'industry_appid' => $industry_appid
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
            ];         
        }else if($model->load($request->post()) && $model->save()){
            return [
                'forceReload'=>'#crud-datatable-pjax',
                'title'=> "详情",
                'content'=>$this->renderAjax('view', [
                    'model' => $model,
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
            ];    
        }else{
             return [
                'title'=> "Update AuthI22tem #".$id,
                'content'=>$this->renderAjax('update', [
                    'model' => $model,
                ]),
                'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
            ];        
        }
    }else{
        /*
        *   Process for non-ajax request
        */
        if ($model->load($request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
}

```

## 没有限定post和get

**company/controllers/AuthController.php**

```php

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
```

## glide控制生成图片指定大小

```php
$picUrl = Yii::$app->glide->createSignedUrl([
                    'glide/index',
                    'path' => $thumbnail_path,
                    'w' => 650,
                    'h' => 440,
                    'fit' => 'crop',
                ], true);

$picUrl = Yii::$app->glide->createSignedUrl([
                    'glide/index',
                    'path' => $thumbnail_path,
                    'w' => 650,
                    'h' => 440,
                ], true);

 'glide/index', 是固定的
 只有path是必须参数
```