- lookup
	- 配置
		- 使用
			- 1.数据入库
			- 2.列表页使用
			- 3.添加页使用


##2.列表页使用
```php
["attribute" => "is_delete", //字段里 
 "value" => function ($model) 
{ 
    return Yii::$app->lookup->item('procduct_category_is_delete', $model->is_delete); // lookup定义的字段 
}, 
 "filter" => Yii::$app->lookup->items('procduct_category_is_delete'), // lookup定义的字段 
 
], 
```

##3.添加页使用
```php
//自定义class
 <?php echo $form->field($model, 'sent_condition',['options' => ['class' => 'col-lg-12 padding0']])->dropDownList(Yii::$app->lookup->items('activity_condition')) ?>

//或
 <?php echo $form->field($model, '字段名')->dropDownList(Yii::$app->lookup->items('activity_condition')) ?>


```