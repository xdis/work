<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">
    
    <style>.tip{text-align:center;font-weight: bold;color: red;}</style>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="tip"> <?php if(Yii::$app->session->hasFlash('info')){ echo Yii::$app->session->getFlash('info');} ?></div>

    <style>
        ul.list{display: inline-block;width: 100%;}
        ul.list li{float: left;list-style: none;margin: 0 50px 0 0;width:100px;}
        .button_submit{float: right;border: 1px #ccc solid;padding: 5px 20px;}
    </style>
 
  <?php ActiveForm::begin(
         ['action'=>'confirm']
         ) ?>      
    <ul class="list">
        <li>产品</li>
        <li>数量<li>
        <li>价格</li>
        <li>金额</li>
    </ul>
<?php 
    $count = 0;
   foreach ($lists as $list) { 
     $count += ($list['product_price']*$list['product_amount']);
     $is_fahuo = $list['is_fahuo'];
    ?>
        <ul class="list">
            <li><?php echo $list['name'] ?></li>
            <li><?php echo $list['product_price'] ?></li>
            <li><?php echo $list['product_amount'] ?></li>
            <li><?php echo $list['product_price']*$list['product_amount']."元" ?></li>
        </ul>
<?php } ?>
      <ul class="list">
        <li>&nbsp</li>
        <li>&nbsp<li>
        <li>&nbsp</li>
        <li>共<?php echo $count ?>元</li>
    </ul>
    


   
</div>
