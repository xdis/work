<?php
use \company\assets\CompanyAsset;
CompanyAsset::addCss($this,Yii::$app->request->baseUrl . '/css/build/staff-manage.min.css');
$this->title = "角色管理|子账号管理|企业管理";
?>
<!-- 右侧主内容区 -->
<div class="right_side">
	<div class="content-title">
		<Icon type="ios-arrow-left" class="arrow"></Icon>
        <span v-if="current_id">编辑角色</span>
        <span v-else>添加角色</span>
	</div>
	<div class="form-content full-form-content">
		<i-form ref="formValidate" :model="formItem" label-position="left" :rules="ruleValidate" :label-width="79">
	        <Form-item label="角色名称" prop="role_name" style="width: 619px">
	            <i-input placeholder="请输入角色名称" v-model="formItem.role_name"></i-input>
	        </Form-item>
	        <Form-item label="角色描述" prop="role_desc" style="width: 619px">
	            <i-input v-model="formItem.role_desc" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入描述"></i-input>
	        </Form-item>
	        <Form-item label="分配权限" prop="role_auth_id" class="ivu-form-item-required">
                <Checkbox-group v-model="formItem.role_auth_id">
                    <div v-for="(item, index) in roles_list" class="mb16">
                        <div class="detailTitle">
                            <span>{{item.text}}</span>
                            <div style="float: right;padding-right: 24px">
                                <button type="button" class="check-btn checkAll-btn" @click="checkAll(index)">全选</button>
                                <button type="button" class="check-btn cleanAll-btn" @click="cleanAll(index)">清空</button>
                            </div>
                        </div>
                        <div class="companyInfo dispatch-role-box">
                            <ul v-if="item.children">
                                <li v-for="(value, key) in item.children">
                                    <span>{{ value.text }}</span>
                                    <div v-if="value.children">
                                        <Checkbox v-for="(v,k) in value.children" :label="v.id"><span>{{v.text}}</span></Checkbox>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </Checkbox-group>
	        </Form-item>
	        <Form-item style="float: right;margin-top: 21px;">
	            <i-button type="primary" @click="handleSubmit('formValidate')" class="btn btn-confirm">保存</i-button>
<!--				<i-button type="text" @click="handleReset('formValidate')" class="btn btn-cancer">取消</i-button>-->
	        </Form-item>
	    </i-form>
    </div>
</div>
<script>

var vm = new Vue({
    el: ".vueBox",
    data: {

        //当前角色的ID,仅在编辑时有效
        current_id: '',

        //权限列表
        roles_list: [],

        single: false,
        formItem: {
            role_name: '',
            role_desc: '',
            role_auth_id: []
        },
        ruleValidate: {
            role_name: [
                { required: true, message: '角色名称不能为空', trigger: 'blur' }
            ],
            role_auth_id: [
                { required: true, type: 'array', min: 1, message: '分配的权限不能为空', trigger: 'change' }
            ]
        }
    },
    mounted: function(){
        //从地址栏取出当前员工的id
        var searchArr = window.location.search.split("=");
        var curid = searchArr[searchArr.length - 1];
        this.current_id = curid;

        if(curid != ''){
            var that = this;
            axios.post('/role-manage/view',{role_id: curid})
                .then(function (response) {
                    var res = response.data;
                    if(res.status == 1){
                        var arr_old = res.data.role_permissions;
                        var new_arr = [];
                        for(var i=0;i<arr_old.length;i++){
                            var tem_arr = arr_old[i].children;
                            if(tem_arr.length > 0){
                                for(var j=0;j<tem_arr.length;j++){
                                    var ls_arr = tem_arr[j].children;
                                    if(ls_arr){
                                        for(var k=0;k<ls_arr.length;k++){
                                            new_arr.push(ls_arr[k].id);
                                        }
                                    }
                                }
                            }
                        }
                        that.formItem.role_name = res.data.role_name;
                        that.formItem.role_desc = res.data.role_desc;
                        that.formItem.role_auth_id = new_arr;
                    }
                });
        }
        this.getRolesList();
    },
    methods: {

        //重数组里删除另一个数组元素值相同的元素
        array_diff: function (a, b) {
            for(var i=0;i<b.length;i++) {
                for(var j=0;j<a.length;j++) {
                    if(a[j]==b[i]){
                        a.splice(j,1);
                        j=j-1;
                    }
                }
            }
            return a;
        },

        //获取权限列表
        getRolesList: function () {
            var that = this;
            axios.post('/role-manage/auth-list')
                .then(function (response) {
                    var res = response.data;
                    if(res.status == 1){
                        that.$set(that.$data,'roles_list',res.data);
                    }
                });
        },

		handleSubmit (name) {
            this.$refs[name].validate((valid) => {
                if (valid) {
                    var that = this;
                    var url = '';
                    if(that.current_id != ''){
                        url = '/role-manage/update';
                        that.formItem.role_id = that.current_id;
                    }else{
                        url = '/role-manage/create';
                    }
                    axios.post(url,that.formItem)
                        .then(function (response) {
                            var res = response.data;
                            if(res.status == 1){
                                that.$Message.success('操作成功!');
                                window.location.href = "/role-manage/list";
                            }else{
                                that.$Message.error(res.message);
                            }
                        });
                }
            })
        },
        handleReset (name) {
            this.$refs[name].resetFields();
        },

        checkAll: function (inx) {
            var roleList = this.roles_list;
            var opera_arr = [];
            var temp_arr = roleList[inx].children;
            for(var i in temp_arr){
                var child_temp_arr = temp_arr[i].children;
                for(var j in child_temp_arr){
                    if(!child_temp_arr[j].id) continue;
                    opera_arr.push(child_temp_arr[j].id);
                }
            }
            this.formItem.role_auth_id = $.unique($.merge(this.formItem.role_auth_id,opera_arr));
        },

        cleanAll: function (inx) {
            var roleList = this.roles_list;
            var opera_arr = [];
            var temp_arr = roleList[inx].children;
            for(var i in temp_arr){
                var child_temp_arr = temp_arr[i].children;
                for(var j in child_temp_arr){
                    if(!child_temp_arr[j].id) continue;
                    opera_arr.push(child_temp_arr[j].id);
                }
            }
            this.formItem.role_auth_id = this.array_diff(this.formItem.role_auth_id,opera_arr);
        }
    }
})


</script>
