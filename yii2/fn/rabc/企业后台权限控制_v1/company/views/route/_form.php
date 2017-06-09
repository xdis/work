<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model company\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('权限名') ?>
    
    <?= $form->field($model, 'industry_appid')->dropDownList($industry_appid) ?>
    
    <?= $form->field($model, 'type')->dropDownList(['2'=>'权限','3'=>'特殊权限','4'=>'菜单']) ?>
    
    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->textInput(['maxlength' => true])->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'visible')->dropDownList($model::getStatuses()) ?>
    
    <?= $form->field($model, 'sort')->textInput() ?>
    
    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
