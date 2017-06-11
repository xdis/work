<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model company\models\UserCompany */

$this->title = '编辑: ' . ' ' . $model->staff_name;
$this->params['breadcrumbs'][] = ['label' => '员工管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="user-company-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
