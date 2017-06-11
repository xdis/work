<?php
use yii\helpers\Url;
use yii\helpers\Html;
use company\models\AuthItem;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'name',
        'label' => '权限名',
        'value' => function ($model) {
                    return $model['level'] > 1 ? str_repeat('|---', $model['level'] - 1) . $model['name'] : $model['name'];
                }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'url',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'type',
        "filter" => Html::activeDropDownList($searchModel, 'type', ['2'=>'权限','3'=>'特殊权限','4'=>'菜单'], ['class' => 'form-control', 'prompt' => '']),
        'label'=>'类型',
        'value' => function ($model) {
            if ($model['type']==2) {
                return '权限';
            } elseif ($model['type']==3) {
                return '特殊权限';
            } elseif ($model['type']==4) {
                return '菜单';
            } else {
                return '';
            }
         }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'industry_appid',
        "filter" => Html::activeDropDownList($searchModel, 'industry_appid', Yii::$app->lookup->items('industry_appid'), ['class' => 'form-control', 'prompt' => '']),
        'value' => function ($model) {
            return Yii::$app->lookup->item('industry_appid', $model['industry_appid']);
         }
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'visible',
        'trueLabel' => '显示',
        'falseLabel' => '不显示'
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function ($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'data-toggle' => 'tooltip','label'=>'详情'],
        'updateOptions' => ['role' => 'modal-remote', 'data-toggle' => 'tooltip','label'=>'编辑'],
        'deleteOptions' => ['role' => 'modal-remote',
            'data-confirm' => false, 'data-method' => false,// for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Are you sure?',
            'data-confirm-message' => 'Are you sure want to delete this item',
            'label'=>'删除'
        ],
        'template' => '{create} {view} {update} {delete}',
        'buttons' => [
            'create' => function ($url, $model, $key) {
                return Html::a('添加子级', $url,['title'=>'添加子级']);
            },
        ],
        'width'=>'180px'
        
    ],

];   