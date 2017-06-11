<?php

use yii\helpers\Html;
use company\assets\SupplierAsset;
use yii\bootstrap\ActiveForm;
use vova07\select2\Widget;

SupplierAsset::register($this);

/* @var $this yii\web\View */
/* @var $model company\models\UserCompany */
/* @var $form yii\bootstrap\ActiveForm */
$departmentTreeMap = \common\helpers\ArrHelper::getTreeMap((new \company\models\Department())->getAllTree());
?>
<style>
    .user-company-form{
        min-height: 800px;
    }
    .user-company-form .form-group{
        width: 100%;
    }
    .user-company-form.supplier .do-something{
        margin-top: 50px;
    }
    .user-company-form .lb-lk input,.user-company-form .lb-lk select{
        width: 100%;
        padding: 5px;
        margin: 1px 0;
        border: 1px solid #c5c5c5;
        text-indent: 10px;
        outline: none;
    }
    .need:after{
        content: '*';
        display: block;
        position: absolute;
        left: -9px;
        top: 0;
        color: #f00;
    }
    .add{
        float: right;
        margin-right: 10px;
        cursor: pointer;
    }
    .add-dept{
        width: 350px;
        position: absolute;
        box-shadow: 0 0 10px #c5c5c5;
        padding: 10px;
        display: none;
        background: #fff;
        right: 0;
        top: 0;
    }
    .add-dept .lb-lk>input,.add-dept .lb-lk>select{
        width: 100% !important;
    }
    .command{
        display: flex;
        padding: 5px 0;
        justify-content: space-around;
    }
    .command>.btn{
        width: 45%;
    }
</style>
<div class="user-company-form supplier">
    <?php $form = ActiveForm::begin(); ?>
        <div class="info-block">
            <div class="inne">
                <div class="lb-lk">
                    <span class="need">员工手机号</span>
                    <?php
                    if ($model->isNewRecord) {
                        echo $form->field($model, 'staff_mobile')->textInput(
                            ['maxlength' => true,'placeholder'=>'请输入11位手机号码'])->label(false);
                    } else {
                        echo $form->field($model, 'staff_mobile')->textInput
                        (['maxlength' => true,'disabled' => true])->label(false);
                    }
                    ?>
                </<div class="lb-lk">
                    <div class="lb-lk">
                        <span class="need">员工姓名</span>
                        <?= $form->field($model, 'staff_name')->textInput(
                            ['maxlength' => true,'placeholder'=>'输入姓名'])->label(false); ?>
                    </<div class="lb-lk">
                        <div class="lb-lk">
                            <span class="need">岗位</span>
                            <?= $form->field($model, 'position_name')
                                ->textInput(['maxlength' => true,'placeholder'=>'输入岗位'])->label(false); ?>
                        </<div class="lb-lk">
                            <div class="lb-lk">
                    <span class="need">
                        <span>选择部门</span>
                        <a class="add">添加</a>
                    </span>
                            <?= $form->field($model, 'department_id')->dropDownList($departmentTreeMap,
                                ['prompt' => '选择部门','class'=>'department1'])->label(false) ?>
                            </<div class="lb-lk">
                                <div class="lb-lk">
                                    <span>分配角色</span>
                                    <?= $form->field($model, 'roleIds')->widget(Widget::className(), [
                                        'options' => [
                                            'multiple' => true,
                                            'placeholder' => '请选择角色',
                                        ],
                                        'settings' => [
                                            'width' => '100%',
                                        ],
                                        'items' => \yii\helpers\ArrayHelper::map(\company\models\AuthItem::getRoles(), 'id', 'name'),
                                        'events' => [
                                            'select2-open' => 'function (e) { log("select2:open", e); }',
                                            'select2-close' => new \yii\web\JsExpression('function (e) { log("select2:close", e); }')
                                        ]
                                    ])->label(false); ?>
                                </<div class="lb-lk">
                                    <div class="lb-lk">
                                        <span class="need">个人店铺</span>
                                        <?= $form->field($model, 'is_opened_store')
                                            ->dropDownList($model::getIsOpenedStore())->label(false); ?>
                                    </<div class="lb-lk">
                                        <div class="do-something">
                                            <?= Html::submitButton('保存',
                                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<!--                                            <a class="btn btn-warning">取消</a>-->
                                        </div>

    <?php ActiveForm::end(); ?>
        <!--弹框-->
        <div class="add-dept">
            <div class="lb-lk">
                <span class="need">部门名称</span>
                <input class="department2" type="text" placeholder="输入部门名称">
            </div>
            <div class="lb-lk">
                <span class="need">上级部门</span>
                <select>
                    <option>选择上级部门</option>
                    <option>技术部</option>
                    <option>产品部</option>
                    <option>|---产品一组</option>
                </select>
            </div>
            <div class="command">
                <a class="btn btn-success btn-Y">是</a>
                <a class="btn btn-warning btn-N">否</a>
            </div>
        </div>
    </div>


<?php
    $js = <<<JS
    
    $(".add").click(function() {
        getDepartment();
        var offset = $(this).offset();
        $(".add-dept").css({left: offset.left-548,top: offset.top-102}).show();
    });
    $(".command .btn").click(function() {
        $(this).parents(".add-dept").hide();
        if($(this).hasClass("btn-Y")){
            var name = $(".department2").val();
            var parent_id = $(this).parents(".add-dept").find("select").val();
            $.ajax({
                  url: '/department/return-add',
                  method: 'post',
                  dataType: 'json',
                  data: {
                      name: name,
                      parent_id: parent_id
                  },
                  success: function(data) {
                        if(data.status==1){
                            layer.msg(data.msg);
                            var html;
                             html += "<option value='0'>顶级部门</option>";
                             for(var i=0; i<data['data'].length; i++){
                               html += "<option value='"+data['data'][i].id+"'>"
                               +data['data'][i].befor+data['data'][i].name+"</option>";
                             }
                            $(".department1").html(html);
                            $(".add-dept select").html(html);
                        }else{
                            layer.msg(data.msg);
                        }
                  },
                  error: function() {
        
                  }
              });
            
        }else if($(this).hasClass("btn-N")){
            $(this).parents(".add-dept").hide();
        }
    });
    
    function getDepartment() {
      $.ajax({
          url: '/department/get-departments',
          method: 'get',
          dataType: 'json',
          success: function(data) {
                if(data.status==1){
                    var html;
                    html += "<option value='0'>顶级部门</option>";
                    for(var i=0; i<data['data'].length; i++){
                        html += "<option value='"+data['data'][i].id+"'>"+data['data'][i].befor+data['data'][i].name+"</option>";
                    }
                    $(".department1").html(html);
                    $(".add-dept select").html(html);
                }
          },
          //error: function(){}

      });
    }
   
JS;
   $this->registerJs($js);
?>
