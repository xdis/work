<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model company\models\AuthItem */

?>
<div class="auth-item-create">
    <?= $this->render('_form', [
        'model' => $model,
        'industry_appid'=>$industry_appid
    ]) ?>
</div>
