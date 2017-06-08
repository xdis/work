<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Order;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'price',
            ["attribute" => "is_fahuo",
                "value" => function ($model) {
                    return $model::dropDown("is_fahuo", $model->is_fahuo);
                },
                "filter" => Order::dropDown("is_fahuo"),
            ],
            'memo',
            // 'created_at',
            // 'updated_at',
            // 'is_fahuo',

            ['class' => 'yii\grid\ActionColumn',"template" => "{view}"],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
