<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model company\models\AuthItem */
?>
<div class="auth-item-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'name',
                'label' => '权限名',
            ],
            'url:url',
            [
                'attribute' => 'status',
                'value' => \company\models\AuthItem::getStatuses()[$model->status]
            ],
        ],
    ]) ?>

</div>
