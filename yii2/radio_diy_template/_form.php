<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Activity */
/* @var $form yii\bootstrap\ActiveForm */
?>

    <div class="activity-form">

        <?php $form = ActiveForm::begin();
        Yii::$app->params['activity_form'] = $form; //定义全局的$form, radio里自定义的模板里，在嵌入输入框，要调用，默认的调用不了
        Yii::$app->params['activity_model'] = $model;//定义全局的$model,radio里自定义的模板里，在嵌入输入框，要调用，默认的调用不了
        ?>

        <?php echo $form->errorSummary($model); ?>

        <?php echo $form->field($model, 'name', ['options' => ['class' => 'col-lg-12  padding0'],])->textInput(['maxlength' => true]) ?>

        <!--<div class="col-lg-12  padding0 field-activity-name">-->
        <!--    <label class="control-label" for="activity-name">活动日期</label>-->
        <!--</div>-->

        <?php echo $form->field($model, 'from_at', ['options' => ['class' => 'col-lg-2  padding0'],])->widget(
            DateTimeWidget::className(), [
                'phpMomentMapping' => ["yyyy-MM-dd HH:mm" => 'YYYY-MM-DD HH:mm',],
                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
                'locale' => 'zh-CN',
            ]
        ); ?>
        <?php echo $form->field($model, 'to_at', ['options' => ['class' => 'col-lg-2'],])->widget(
            DateTimeWidget::className(), [
                'phpMomentMapping' => ["yyyy-MM-dd HH:mm" => 'YYYY-MM-DD HH:mm',],
                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
                'locale' => 'zh-CN',

            ]
        ) ?>

        <?php echo $form->field($model, 'amount', ['options' => ['class' => 'col-lg-12 padding0']])->textInput(['maxlength' => true])->textInput(['placeholder' => '￥输入金额']) ?>

        <?php echo $form->field($model, 'sent_condition', ['options' => ['class' => 'col-lg-12 padding0']])->textInput(['placeholder' => '群发全部用户']) ?>

        <div class="col-lg-12  padding0 field-activity-name">
            <label class="control-label" for="activity-name">奖券有效期</label>
        </div>

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


        <div class="form-group col-lg-12 padding0">
            <?php echo Html::submitButton($model->isNewRecord ? '保存' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>


<?php
$css = <<<CSS
.padding0{
    padding: 0;
}
CSS;
$this->registerCss($css);