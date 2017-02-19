
#最终效果如下
![aaa](radio_diy_template/1.png)


- 效果要求
	- 每个radio后面都跟踪一个input
- 开发思路
	- 对raio的模板进行个性自定义

###开发代码
http://i.vding.dev/admin/activity/create<br />
路径 company/modules/admin/views/activity/_form.php

```php
<?= $form->field($model, 'valid_type', [
            'options' => ['class' => 'form-group form-md-radios col-lg-12'],
            'template' => '{label}<div class="md-radio-inline">{input}</div>{hint}{error}',
        ])->radioList([0 => '获取之后延后', 1 => '开始日期：获取奖券日期'],
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    $checked = $checked ? "checked" : "";
                    $return = '<div class="md-radio " style="display: inline-block;width: 100%;">';
                    $return .= '<div class="" style="float:left;margin-top:10px;">';
                    $return .= '<input type="radio" id="' . $name . $value . '" name="' . $name . '" value="' . $value . '" class="md-radiobtn"  ' . $checked . '>';
                    $return .= '<label for="' . $name . $value . '">
                    <span></span>
                    <span class="check"></span>
                    <span class="box"></span>' . ucwords($label) . '</label></div>';
                    $model2 = new common\models\Activity();
                    if ($value == 0) {
                        $return .= ActiveForm::begin()->field($model2, 'valid_period', ['options' => ['class' => 'col-lg-3']])->textInput(['placeholder' => '输入天数'])->label('');
                    } else {
                        $return .= ActiveForm::begin()->field($model2, 'valid_end_at', ['options' => ['class' => 'col-lg-2'],])->widget(
                            DateTimeWidget::className(), [
                                'phpMomentMapping' => ["yyyy-MM-dd HH:mm" => 'YYYY-MM-DD HH:mm',],
                                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
                                'locale' => 'zh-CN',
                            ]
                        )->label('')->textInput(['placeholder' => '截止日期']);
                    }
                    $return .= '</div>';
                    return $return;
                },
            ]

        )->label('') ?>
```
##上面的代码，改进如下
[company/modules/admin/views/activity/_form.php](_form.php) 
```php
 <div class="activity-form">

        <?php $form = ActiveForm::begin();
        Yii::$app->params['activity_form'] = $form; //定义全局的$form, radio里自定义的模板里，在嵌入输入框，要调用，默认的调用不了
        Yii::$app->params['activity_model'] = $model;//定义全局的$model,radio里自定义的模板里，在嵌入输入框，要调用，默认的调用不了
        ?>

       ...
       

        <?= $form->field($model, 'valid_type', [
            'options' => ['class' => 'form-group form-md-radios col-lg-12'],
            'template' => '<div class="md-radio-inline">{input}</div>{hint}',
        ])->radioList([0 => '获取之后延后', 1 => '开始日期：获取奖券日期'],
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    $checked = $checked ? "checked" : "";
                    $return = '<div class="md-radio " style="display: inline-block;width: 100%;">';
                    $return .= '<div class="" style="float:left;margin-top:5px;">';
                    $return .= '<input type="radio" id="' . $name . $value . '" name="' . $name . '" value="' . $value . '" class="md-radiobtn"  ' . $checked . '>';
                    $return .= '<label for="' . $name . $value . '">
                        <span></span>
                        <span class="check"></span>
                        <span class="box"></span>' . ucwords($label) . '</label></div>';
                    $model2 = new common\models\Activity();
                    if ($value == 0) {
                        $return .= Yii::$app->params['activity_form']->field( Yii::$app->params['activity_model'], 'valid_period', ['options' => ['class' => 'col-lg-3']])->textInput(['placeholder' => '输入天数'])->label(false);

                    } else {
                        $return .= Yii::$app->params['activity_form']->field( Yii::$app->params['activity_model'], 'valid_end_at', ['options' => ['class' => 'col-lg-2'],])->widget(
                            DateTimeWidget::className(), [
                                'phpMomentMapping' => ["yyyy-MM-dd HH:mm" => 'YYYY-MM-DD HH:mm',],
                                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
                                'locale' => 'zh-CN',
                            ]
                        )->label(false);
                    };
                    $return .= '</div>';
                    return $return;
                },
            ]

        )->label(false) ?>

   ...


        <div class="form-group col-lg-12 padding0">
            <?php echo Html::submitButton($model->isNewRecord ? '保存' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

```





###网上借鉴的代码
[http://www.yiichina.com/tutorial/733?sort=desc](http://www.yiichina.com/tutorial/733?sort=desc "链接")

```php
<?php $model->test=($model->test)?:1?>
<?= $form->field($model, 'test',[
   'options'=>['class' => 'form-group form-md-radios'],
    'template' => '{label}<div class="col-md-9 md-radio-inline">{input}</div>{hint}{error}', 
])->radioList([1=>Yii::t('common','has'),0=>Yii::t('common','not')],
[
    'item' => function($index, $label, $name, $checked, $value) {
        $checked=$checked?"checked":"";
        $return = '<div class="md-radio">';
        $return .= '<input type="radio" id="' . $name . $value . '" name="' . $name . '" value="' . $value . '" class="md-radiobtn"  '.$checked.'>';
        $return .= '<label for="' . $name . $value . '">
                    <span></span>
                    <span class="check"></span>
                    <span class="box"></span>' . ucwords($label) . '</label>';
        $return .= '</div>';
        return $return;
    }
]) ?>
```



