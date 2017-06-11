<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model company\models\AuthItem */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $routes [] */
$opts = \yii\helpers\Json::htmlEncode(['routes' => $model->getRolePermissions()]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIcon = ' <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>';
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php if ($model->id): ?>
        <div class="row">
            <div class="col-sm-5">
                <div class="input-group">
                    <input class="form-control search" data-target="available"
                           placeholder="<?= Yii::t('rbac-admin', '搜索未分配权限') ?>">
                    <span class="input-group-btn">
                <?= Html::a('<span class="glyphicon glyphicon-refresh"></span>', ['refresh-permissions', 'id' => $model->id], [
                    'class' => 'btn btn-default',
                    'id' => 'btn-refresh'
                ]) ?>
            </span>
                </div>
                <select multiple size="20" class="form-control list" data-target="available"></select>
            </div>
            <div class="col-sm-1">
                <br><br>
                <?= Html::a('&gt;&gt;' . $animateIcon, ['assign-permissions', 'id' => $model->id], [
                    'class' => 'btn btn-success btn-assign',
                    'data-target' => 'available',
                    'title' => '分配'
                ]) ?><br><br>
                <?= Html::a('&lt;&lt;' . $animateIcon, ['remove-permissions', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-assign',
                    'data-target' => 'assigned',
                    'title' => '移除'
                ]) ?>
            </div>
            <div class="col-sm-5">
                <input class="form-control search" data-target="assigned"
                       placeholder="<?= Yii::t('rbac-admin', '搜索已分配权限') ?>">
                <select multiple size="20" class="form-control list" data-target="assigned"></select>
            </div>
        </div>
    <?php endif ?>


    <div class="form-group">
        <?php echo Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>