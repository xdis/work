<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/27 0027
 * Time: 10:59
 */
use company\assets\SupplierAsset;
$this->title = '添加员工';
$this->params['breadcrumbs'][] = $this->title;
SupplierAsset::register($this);
?>
<style>
    .user-company-form{
        min-height: 800px;
    }
    .user-company-form.supplier .do-something{
        margin-top: 50px;
    }
    .user-company-form .lb-lk input,.user-company-form .lb-lk select{
        width: 100%;
        height: 32px;
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
    <form action="">
        <div class="info-block">
            <div class="inne">
                <div class="lb-lk">
                    <span class="need">员工手机号</span>
                    <input type="text" placeholder="请输入11位手机号码">
                </<div class="lb-lk">
                <div class="lb-lk">
                    <span class="need">员工姓名</span>
                    <input type="text" placeholder="输入姓名">
                </<div class="lb-lk">
                <div class="lb-lk">
                    <span class="need">岗位</span>
                    <input type="text" placeholder="输入岗位">
                </<div class="lb-lk">
                <div class="lb-lk">
                    <span class="need">
                        <span>选择部门</span>
                        <a class="add">添加</a>
                    </span>
                    <select class="department1">
                        <option>选择部门</option>
                    </select>
                </<div class="lb-lk">
                <div class="lb-lk">
                    <span>分配角色</span>
                    <input type="text" placeholder="测试; 导游; 总经理">
                </<div class="lb-lk">
                <div class="lb-lk">
                    <span class="need">个人店铺</span>
                    <select>
                        <option>开通</option>
                        <option>关闭</option>
                    </select>
                </<div class="lb-lk">
                <div class="do-something">
                    <a class="btn btn-success">保存</a>
                    <a class="btn btn-warning">取消</a>
                </div>
    </form>
    <!--弹框-->
    <div class="add-dept">
        <div class="lb-lk">
            <span class="need">部门名称</span>
            <input class="department2" type="text" placeholder="输入部门名称">
        </div>
        <div class="lb-lk">
            <span class="need">上级部门</span>
            <select class="pre-dept">
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
        var offset = $(this).offset();
        $(".add-dept").css({left: offset.left-548,top: offset.top-102}).show();
    });
    $(".command .btn").click(function() {
        $(this).parents(".add-dept").hide();
        if($(this).hasClass("btn-Y")){
            layer.msg("保存成功的方法")
        }
    });
    // 选择部门触发事件
    $(".department1").click(function() {
        console.log("需请求后台数据");
      $.ajax({
          url: '',
          method: 'get',
          dataType: 'json',
          success: function() {
                
          },
          error: function() {
            
          }
      })
    });
    // 选择上级部门触发事件
    $(".pre-dept").click(function() {
        console.log("需请求后台数据");
      $.ajax({
          url: '',
          method: 'get',
          dataType: 'json',
          success: function() {
                
          },
          error: function() {
            
          }
      })
    });
JS;
$this->registerJs($js);
?>

