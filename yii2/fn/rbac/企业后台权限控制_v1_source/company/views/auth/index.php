<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel company\models\search\AuthItem */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => '角色',
                'attribute' => 'name',
            ],
            'description:ntext',

            [
                'label' => '操作',
                'format' => 'raw',
                'value' => function ($data) {
                    if (!$data->company_id) {
                        return '';
                    }
                    return Html::a('编辑', ['/auth/update', 'id' => $data->id])
                    . '&nbsp;'
                    . Html::a('删除', ['/auth/delete', 'id' => $data->id], ['data-confirm' => '你确定要删除吗', 'data-method' => 'post']);
                },
            ],
        ],
    ]); ?>

</div>
