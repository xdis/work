<?php

use company\assets\CompanyAuthAsset;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model company\models\AuthItem */

$this->title = '添加';
$this->params['breadcrumbs'][] = ['label' => '角色管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
CompanyAuthAsset::register($this);

?>
<div class="company-update">

    <?php echo Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->csrfToken); ?>

    <form action="" class="auth-form">
        <div class="form-group field-company-business_license required">
            <label class="control-label" for="role_name">角色名称</label>
            <input type="text"
                   id="role_name"
                   class="form-control"
                   name="AuthItem['role_name']"
                   datatype="s1-20"
                   maxlength="20"
                   errormsg="不能超过10个汉字"
                   nullmsg="请输入角色名称"
                   placeholder="请输入角色名称">
            <p class="help-block help-block-error"></p>
        </div>

        <div class="form-group field-company-name">
            <label class="control-label" for="role_desc">描述</label>
            <input type="text"
                   id="role_desc"
                   class="form-control"
                   datatype="s1-300"
                   errormsg="不能超过150个汉字"
                   nullmsg="请输入权限描述"
                   name="Auth[role_desc]"
                   maxlength="300"
                   placeholder="请输入角色描述">
        </div>

        <div class="form-group field-company-delegate_name required">
            <label class="control-label" for="company-delegate_name">选择权限</label>
            <div class="auth-treeview-container" id="auth-tree"></div>
            <div id="show-tips"></div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary" id="auth-now">保存</button>
            <button type="submit" class="btn btn-default">取消</button>
        </div>
    </form>

</div>
