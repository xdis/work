<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model company\models\UserCompany */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '员工管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-company-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
