<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model company\models\UserCompany */

$this->title = $model->staff_name;
$this->params['breadcrumbs'][] = ['label' => '员工管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-company-view">

    <p>
        <?php echo Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('离职', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '是否确认将该员工设置为离职状态,离职后该员工不能登录后台？',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'staff_mobile',
            'staff_name',
            'position_name',
            [
                'label' => '员工状态',
                'value' => $model->staff_status == 1 ? '在职':'离职',
            ],
            [
                'label' => '角色',
                'value' => implode(',', \yii\helpers\ArrayHelper::getColumn($model->rolesName, 'auth_item_name')),
            ],
            [
                'attribute' => 'is_opened_store',
                'value' => isset($model::getIsOpenedStore()[$model->is_opened_store]) ? $model::getIsOpenedStore()[$model->is_opened_store] : ''
            ],
            [
                'label' => '店铺网址',
                'value' => isset($model->dpStore->store_url) ? $model->dpStore->store_url : ''
            ],
            [
                'label' => '店铺访问量',
                'value' => isset($model->dpStore->pv) ? $model->dpStore->pv : ''
            ],
        ],
    ]) ?>

</div>
