<?php
use \company\assets\CompanyAsset;
CompanyAsset::addCss($this,Yii::$app->request->baseUrl . '/css/build/staff-manage.min.css');
$this->title = "角色管理|子账号管理|企业管理";
?>

<!-- 右侧主内容区 -->
<div class="right_side">
	<div class="content-title">角色管理</div>
	<div class="content-main">
		<div class="clearfix">
			<div class="searchBox left">
				<input placeholder="角色名称"
                       style="width: 228px"
                       class="searchInput"
                       v-model="searchParam.role_name">
	            <div class="searchInput inline-block IconBox" @click="getListData">
	            	<Icon type="ios-search" class="searchIcon"></Icon>
	            </div>
			</div>
			<i-button class="right" type="primary" @click="goToAdd">添加角色</i-button>
		</div>
		<div class="list_table">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th width="15.4%">角色名称</th>
                        <th>描述</th>
                        <th width="10.9%">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="listData.length != 0">
                        <tr v-for="(item, index) in listData" :data-id="item.id">
                            <td>
                                <p>{{ item.name }}</p>
                            </td>
                            <td>{{ item.description }}</td>
                            <td>
                                <a v-if="item.is_system == 0" class="c-green" href="" v-on:click.prevent="goToDetail(item.id)">详情</a>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <td colspan="3">
                                <p class="t-center">暂无数据</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
		</div>
		<div class="pageBox">
            <Page :total="count" show-sizer show-elevator placement="bottom" show-total
                  v-on:vdpagechange="pageChange"
                  v-on:vdsizechange="sizeChange">
            </Page>
        </div>

	</div>
</div>

<script>
var vm = new Vue({
    el: ".vueBox",
    data: {

        //搜索参数
        searchParam: {
            role_name: "",
            page_index: 1,
            page_size: 10
        },

        //列表条数
        count: 0,

        //列表数据
        listData: []
    },
    mounted: function(){
        this.getListData();
    },
    methods: {
        //获取列表数据
        getListData: function () {
            var that = this;
            axios.post('/role-manage/list', that.searchParam)
                .then(function (response) {
                    var res = response.data;
                    if(res.status == 1){
                        that.$set(that.$data,'listData',res.data.list);
                        that.count = res.data.total_page;
                    }
                });
        },

        //跳转到角色详情
        goToDetail: function (id) {
            window.location.href = '/role-manage/view?id=' + id;
        },

        //跳往添加角色页面
        goToAdd: function () {
            window.location.href = '/role-manage/create';
        },

        //去往第几页
        pageChange: function(page){
            vm.searchParam.page_index = page;
            vm.getListData();
        },

        //每页显示几条
        sizeChange: function(size){
            vm.searchParam.page_size = size;
            vm.getListData();
        }
    }
})
</script>
