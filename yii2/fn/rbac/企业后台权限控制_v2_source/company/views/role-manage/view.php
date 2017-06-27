<?php
use \company\assets\CompanyAsset;
CompanyAsset::addCss($this,Yii::$app->request->baseUrl . '/css/build/staff-manage.min.css');
$this->title = "角色管理|子账号管理|企业管理";
?>
<!-- 右侧主内容区 -->
<div class="right_side role-detail-content">
	<div class="content-title">
		<Icon type="ios-arrow-left" class="arrow"></Icon>角色详情
	</div>
    <div class="content-main">
        <div class="clearfix">
            <i-button class="right" type="text" @click="removeItem">删除</i-button>
            <i-button class="right" type="primary" @click="goToEdite">编辑</i-button>
        </div>
        <!-- 基本信息 -->
        <div class="mt24 mb16">
            <div class="detailTitle">基本信息</div>
            <div class="companyInfo">
                <ul>
                    <li style="margin-bottom:15px">
                        <span>角色名称</span>
                        <span v-cloak>{{ pageData.role_name }}</span>
                    </li>
                    <li>
                        <span>角色描述</span>
                        <span v-cloak>{{ pageData.role_desc }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 权限分配 -->
        <div class="mb16">
            <div class="detailTitle">权限分配</div>
            <table border=0 cellspacing=1>
                <tr v-for="(item,index) in pageData.role_permissions">
                    <td class="module-name-td" v-cloak>{{ item.name }}</td>
                    <td class="own-role-td">
                        <div class="dispatch-role-box">
                            <ul v-if="item.children">
                                <li v-for="(val,key) in item.children">
                                    <span v-cloak>{{ val.name }}</span>
                                    <template v-for="(v,k) in val.children">
                                        <Checkbox :value="v.checked == 1" :label="v.id" disabled v-cloak>{{v.name}}</Checkbox>
                                    </template>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
var vm = new Vue({
    el: ".vueBox",
    data: {
        //当前id
        current_id: '',

        single: false,
        //基本信息
        pageData: {}
    },
    mounted: function(){
        //从地址栏取出当前员工的id
        var searchArr = window.location.search.split("=");
        var curid = searchArr[searchArr.length - 1];
        this.current_id = curid;
        this.getPageData();
    },
    methods: {
        //获取页面数据
        getPageData: function () {
            var that = this;
            axios.post('/role-manage/view', {role_id: that.current_id})
                .then(function (response) {
                    var res = response.data;
                    if(res.status == 1){
                        that.$set(that.$data,'pageData',res.data);
                    }
                });
        },

        //删除
        removeItem: function () {
            var that = this;

            that.$Modal.confirm({
                title: '提示',
                content: '<div style="color:#333">确认删除该角色吗？</div>',
                loading: true,
                onOk: function(){
                    //执行删除
                    axios.post('/role-manage/delete',{role_id:that.current_id})
                        .then(function (response) {
                            var res = response.data;
                            if(res.status == 1){
                                vm.$Message.success("删除成功!");
                                vm.$Modal.remove();
                                window.location.href = "/role-manage/list";
                            }else{
                                vm.$Modal.remove();
                                vm.$Message.error(res.message);
                                setTimeout(function () {
                                    window.location.href = "/role-manage/list";
                                },1500);

                            }
                        });
                }
            });
        },

        //编辑
        goToEdite: function () {
            window.location.href = "/role-manage/update?id=" + this.current_id;
        }
    }
})
</script>
