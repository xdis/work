<?php

use yii\helpers\Html;
use yii\grid\GridView;
use company\models\UserCompany;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use company\modules\lookup\models\Lookup;

/* @var $this yii\web\View */
/* @var $searchModel company\models\search\UserCompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '员工管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-company-index">
 
    <p>
    	<?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],

            'staff_name',
            'staff_mobile',
            [
                'attribute' => 'name',
                'label' => '部门',
                'value' => 'department.name'
            ],
            'position_name',
            [
                'attribute' => 'roles',
                'label' => '角色',
                'value' => function ($model) {
                    if (empty($model->rolesName)) {
                        return '暂无角色';
                    } else {
                        return implode(',', \yii\helpers\ArrayHelper::getColumn($model->rolesName, 'auth_item_name'));
                    }
                }
            ],
            [
                'attribute' => 'is_opened_store',
                'filter' => Html::activeDropDownList($searchModel, 'is_opened_store', UserCompany::getIsOpenedStore(), ['class' => 'form-control', 'prompt' => '全部']),
                'value' => function ($model) {
                    return UserCompany::getIsOpenedStore()[$model->is_opened_store];
                },
            ],
            [
                'attribute' => 'staff_status',
                'label'     => '员工状态',
                "filter" => Html::activeDropDownList($searchModel, 'staff_status', Yii::$app->lookup->items('staff_status'), ['class' => 'form-control', 'prompt' => '全部']), // lookup定义的字段 
                'value' => function ($model) {
                    return Yii::$app->lookup->item('staff_status', $model->staff_status);
                }
            ],
            [
                'label' => '管理密码',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->apply_reset_pwd) {
                        return Html::a('发送', ['reset-password', 'id' => $model->id], ['data' => ['confirm' => '您确定要重置此员工的密码吗？', 'method' => 'post']]);
                    }
                    return '';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {view} {delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        if ($model->staff_status==1) {
                            return Html::a('编辑', $url);
                        } else {
                            return '';
                        }
                    },
                    'view' => function ($url, $model, $key) {
                        return Html::a('详情', $url);
                    },
                    'delete' => function ($url, $model, $key) {
                        if ($model->staff_status==1) {
                            return Html::a('离职', $url,['data-confirm' => '是否确认将该员工设置为离职状态,离职后该员工不能登录后台？']);
                        } else {
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".importModal">批量导入</button>
    <button type="button" id="dispatchRole" class="btn btn-success">批量分配角色</button>
</div>
<!--批量导入---上传-->
<div class="modal fade bs-example-modal-sm moreImport importModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <p class="tt">提示：</p>
            <p>1.上传的员工信息会自动过滤手机号重复的员工。</p>
            <p>2.<a href="<?php echo Yii::$app->urlManagerCompany->createAbsoluteUrl('img/demo.xlsx');?>">点击下载EXCEL模板</a></p>
            <form id="fileUpload"  enctype="multipart/form-data" method="post">
                <input type="file" name="user_company_excelfile" id="user_company_excelfile">
                <button type="button" class="btn btn-primary" id="stratUpload">开始上传</button>
            </form>
        </div>
    </div>
</div>
<!--批量导入---成功失败提示-->
<div class="modal fade bs-example-modal-sm moreImport tipModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <p>上传完成，成功上传<span id="se">0</span>条，失败<span id="fa">0</span>条</p>
            <a href='' class="btn btn-default" id='file_path'>下载导入失败记录</a>
        </div>
    </div>
</div>
<!--批量分配角色-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel" style="text-align: left;">选择角色
                	<span style="font-size: 14px;margin-left:15px;">(提示：批量设置角色后将替换员工原有的角色)</span>
                </h4>
            </div>
            <div class="modal-body" style="overflow-y: auto;max-height: 350px;">
                <form action="">
                <?php foreach ($authitem_model as $k=>$v) {?>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="inlineCheckbox" value="<?php echo $v['id']?>"> <?php echo $v['name']?>
                    </label>
                <?php }?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yesBtn">是</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">否</button>
            </div>
        </div>
    </div>
</div>
<?php
$js = <<<JS
    var keys;
    $(function(){
        $('.importModal').on('show.bs.modal', function () {
            console.log('dafad');
            var objFile = document.getElementById('user_company_excelfile');
            objFile.outerHTML=objFile.outerHTML.replace(/(value=\").+\"/i,"$1\"");
        });
        $('.tipModal').on('hidden.bs.modal', function (e) {
            window.location.reload();
        });
        $('#stratUpload').click(function(e) {
            e.preventDefault();
            if($("input[name='file']").val() == ''){
                layer.msg('请选择文件后再上传');
                return
            }
    
//      var formData = new FormData($('#user_company_excelfile')[0].files[0]);

    
//     console.log($('#user_company_excelfile')[0].files[0]);
//     console.log(formData);
//             ajax('/user-company/import','post',formData,function(response) {
//                 if(response.status == 1){
//                     $('.importModal').modal('hide');
//                     $('.tipModal').modal('show');
//                     $('#se').html(response.data.success_num);
//                     $('#fa').html(response.data.faile_num);
//                 }else{
//                   layer.msg(response.message);
//                 }
//             })
            var formData = new FormData();
            formData.append('user_company_excelfile', $('#user_company_excelfile')[0].files[0]);
            $.ajax({
                url: '/user-company/import',
                type: 'POST',
                cache: false,
                data: formData,
                processData: false,
                contentType: false
            }).done(function(response) {
                 if(response.status == 1){
                     $('.importModal').modal('hide');
                     $('.tipModal').modal('show');
                     $('#se').html(response.data.success_num);
                     $('#fa').html(response.data.faile_num);
                     $('#file_path').attr('href',response.data.file_path);
                 }else{
                   layer.msg(response.message);
                 }

            }).fail(function(res) {}); 
        });
        $('#dispatchRole').click(function(e) {
            e.preventDefault();
            keys = $('#w0').yiiGridView('getSelectedRows');
            if(keys.length < 1){
              layer.msg('请选择员工');
              return
            }
            $('#myModal').modal('show');
        });
        
        $('#yesBtn').click(function(e) {
            e.preventDefault();
            var arr = [];
            $("input[name='inlineCheckbox']:checked").each(function(){
                arr.push($(this).val());
            });
            if(arr.length < 1){
                layer.msg('请选择角色');
                return
            }
            ajax('/user-company/batch-role','post',{userIds:keys,roleIds:arr},function(response) {
              if(response.status == 1){
                  window.location.reload();
              }else{
                  layer.msg(response.message);
              }
            })
        })
    });
JS;
$this->registerJs($js);
?>