
# 权限

## 企业后台_globalAccesss配置

```php
'as globalAccess'=>[
         'class'=>'\common\behaviors\GlobalAccessBehavior',
         'rules'=>[
             [
                 'controllers'=>['sign-in'],
                 'allow' => true,
                 'roles' => ['?'],
                 'actions'=>['login']
             ],
             [
                 'controllers'=>['sign-in'],
                 'allow' => true,
                 'roles' => ['@'],
                 'actions'=>['logout']
             ],
             [
                 'controllers'=>['site'],
                 'allow' => true,
                 'roles' => ['?', '@'],
                 'actions'=>['error']
             ],
             [
                 'controllers'=>['debug/default'],
                 'allow' => true,
                 'roles' => ['?'],
             ],
             [
                 'controllers'=>['user'],
                 'allow' => true,
                 'roles' => ['administrator'],
             ],
             [
                 'controllers'=>['user'],
                 'allow' => false,
             ],
             [
                 'allow' => true,
                 'roles' => ['manager'],
             ]
         ]
     ]
];
```

## 企业后台权限控制_v1

[源代码](rbac/企业后台权限控制_v1_source)  

### 自定义globalAccesss配置

**company/config/web.php**

```php
 'as globalAccess'=>[
        'class'=>'\common\behaviors\GlobalAccessBehavior',
        'accessControlFilter' => 'common\filters\UrlAccessFilter',
        'rules'=>[
            
        ]
    ],
```


# 3个表

## 表的数据结构
```

#auth_assign  角色指派 

CREATE TABLE `auth_assign` (
  `auth_item_id` int(11) NOT NULL,
  `auth_item_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '角色名、权限名',
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL COMMENT '企业ID',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`auth_item_id`,`user_id`,`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户关联角色表'


# auth_item  创建权限

CREATE TABLE `auth_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '角色名、权限名',
  `url` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `industry_appid` tinyint(11) DEFAULT NULL COMMENT '企业开通的应用//look_up(industry_appid)',
  `company_id` int(11) DEFAULT '0' COMMENT '企业ID 0表示共用',
  `parent_id` int(11) DEFAULT '0' COMMENT '父级',
  `type` tinyint(4) NOT NULL COMMENT '类型：1角色 2权限,3 特殊权限,4 菜单',
  `description` text CHARACTER SET utf8 COLLATE utf8_bin COMMENT '描述',
  `visible` tinyint(4) DEFAULT '1' COMMENT '是否显示//0 否，1 是',
  `level` tinyint(4) DEFAULT '0' COMMENT '级别//-1 type为角色时,0 应用（模块级）， 1 控制器， 2  操作 ',
  `depth` int(4) DEFAULT '0' COMMENT '节点深度// 顶级 0 ， 一级 1，  二级 2 ， 三级 3 ... (程序如果需要可以使用这个字段）',
  `status` tinyint(11) unsigned DEFAULT '1' COMMENT '状态 1有效 0无效',
  `sort` mediumint(8) DEFAULT '0' COMMENT '排序，值越大，排越前',
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `is_platform` tinyint(4) DEFAULT '0' COMMENT '是否为平台角色权限 1:是 0:不是',
  PRIMARY KEY (`id`),
  KEY `type` (`type`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE,
  KEY `url` (`url`) USING BTREE,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4617 DEFAULT CHARSET=utf8 COMMENT='角色、权限表'



#auth_item_child  创建角色

CREATE TABLE `auth_item_child` (
  `parent` int(11) NOT NULL COMMENT 'auth_item=>id type=1',
  `child` int(11) NOT NULL COMMENT 'auth_item=>id type=2',
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限关联表'

```

## auth_item
> 添加权限  

### auth_item_列表页
>http://i.vding.dev/route/

![](rbac/rbac_vding_v1/auth_item_list.png)


### auth_item_创建页

> 访问的地址:http://i.vding.dev/route/create

![](rbac/rbac_vding_v1/auth_item_create.png)


```php
/**
 * 新增
 * @return mixed
 */
public function actionCreate()
{
    $request = Yii::$app->request;
    $model = new AuthItem();  
    
    $model->type = 1;
    if ($model->load($request->post()) && $model->save()) {
        return $this->redirect(['view', 'id' => $model->id]);
    } else {
        $parent_id = $request->get('id',0);
        $model->parent_id = $parent_id;
        $look_data = Lookup::find()->where(['type'=>'industry_appid'])->asArray()->all();
        $industry_appid = ArrayHelper::map($look_data, 'code','name');
        return $this->render('create', [
            'model' => $model,
            'industry_appid'=>$industry_appid
        ]);
    }

   
}

```

## auth_item_child
> 创建角色使用  

[时序图](../uml/rbac_vding_v1/角色添加.oom)  

### auth_item_child_列表页

> http://i.vding.dev/auth/index  


![](rbac/rbac_vding_v1/auth_item_child_list.png)

**company/controllers/AuthController.php**

```php
/**
 * 角色列表
 * @return mixed
 */
public function actionIndex()
{
    $searchModel = new AuthItemSearch(['type' => AuthItem::TYPE_ROLE]);
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    //ajax
    if (Yii::$app->request->getIsAjax()) {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return $dataProvider->getModels();
    }
    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
```

### auth_item_child_创建页
> 创建角色  
> http://i.vding.dev/auth/create  


![](rbac/rbac_vding_v1/auth_item_child_create.png)

```php
/**
 * 创建角色
 * @return mixed
 */
public function actionCreate()
{
    $model = new AuthItem();
    $model->type = AuthItem::TYPE_ROLE;
    if ($model->load(Yii::$app->request->post())) {
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        $purview = Yii::$app->request->post('AuthItem')['purview'];
        $model->company_id = Yii::$app->user->getCompanyId();
        //编辑角色与权限
        if ($model->editAuth(1, $purview)) {
            $back['status'] = 1;
            $back['message'] = '';
            $back['url'] = Url::to(['auth/index']);
            return $back;
        } else {
            $back['status'] = 0;
            $back['message'] = current($model->errors)[0];
            $back['url'] = '';
            return $back;
        }
    } else {
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}

```

### 权限入库 [auth_item_child]

```php
/**
 * 编辑角色与权限
 * @param int       $type       1:添加    2：修改
 * @param string    $purview    权限，多个权限则逗号隔开
 * @param int       $id         角色id，只有type=2的时候才用上
 * @return bool
 */
public function editAuth ($type = 1, $purview = '', $id = '') {
    if ($type == 1) {
        try {
            $trans = Yii::$app->db->beginTransaction();
            //第一步：保存角色基本信息
            if (!$this->save()) {
                $this->addError(array_keys($this->errors)[0],current($this->errors)[0]);
                throw new Exception(current($this->errors)[0]);
            }
            
            //第二步：为该角色添加新的权限
            if (!empty($purview)) {
                $arr_purview = explode(',', $purview);
                $rows = array();
                foreach ($arr_purview as $k=>$v) {
                    $rows[$k]['parent'] = $this->primaryKey;
                    $rows[$k]['child']  = $v;
                }
                //批量添加
                if (!ModelHelper::saveAll('auth_item_child', $rows)) {
                    $this->addError('add','操作失败');
                    throw new Exception('操作失败');
                }
            }
            
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollback();
            return false;
        }
        
        
    } elseif ($type == 2) {
        try {
            $trans = Yii::$app->db->beginTransaction();
            //第一步：保存角色基本信息
            if (!$this->save()) {
                $this->addError(array_keys($this->errors)[0],current($this->errors)[0]);
                throw new Exception(current($this->errors)[0]);
            }
            
            //第二步：删除改角色原来所有的权限
            if (!AuthItemChild::deleteAll(['parent'=>$id])) {
                $this->addError('update','操作失败');
                throw new Exception('操作失败');
            }
            
            //第三步：为该角色添加新的权限
            if (!empty($purview)) {
                $arr_purview = explode(',', $purview);
                $rows = array();
                foreach ($arr_purview as $k=>$v) {
                    $rows[$k]['parent'] = $id;
                    $rows[$k]['child']  = $v;
                }
                //批量添加
                if (!ModelHelper::saveAll('auth_item_child', $rows)) {
                    $this->addError('update','操作失败');
                    throw new Exception('操作失败');
                }
            }
            
            $trans->commit();
            return true;
        } catch (\Exception $e) {
            $trans->rollback();
            return false;
        }

    }
}

```


### 选择权限_列表

> http://i.vding.dev/auth/load-auth-date  

```php
/异步加载权限数据
public function actionLoadAuthDate() {
    //ajax请求
	//编辑的时候才有ID,如 http://i.vding.dev/auth/update?id=4616
    $id = Yii::$app->request->post('id');
    $company_id = Yii::$app->user->CompanyId;
    if ($company_id == 1) {
        $is_platform = '';
    } else {
        $is_platform = 0;
    }
    
    //得到审核通过的应用
    $company_app_data = CompanyApp::find()->where(['company_id'=>$company_id,'audit_status'=>2])->all();
    $industry_appid = [8];//企业基础应用
    if ($company_app_data && count($company_app_data)>0) {
        foreach ($company_app_data as $k=>$v) {
            $industry_appid[] = $v['industry_app_id'];
        }
        array_unique($industry_appid);//去重
    }
    
    if (Yii::$app->request->isPost && !empty($id)) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //获取角色目前所拥有的权限节点
        $power = AuthItemChild::find()->where(['parent'=>$id])->asArray()->all();
        //获取所有权限节点
        $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
                    ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                    ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
                    ->andFilterWhere(['is_platform'=>$is_platform])
                    ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
        //重新组装数组结构
        $reArr = TreeHelper::getSubs($all_node);
        $reArr = $this->changeSubsEdit($reArr, $power);
        return $reArr;
    } else {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //获取所有权限节点
        $all_node = AuthItem::find()->where(['IN', 'type', [2,4]])->andWhere(['status'=>1])
                    ->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['company_id' => 0]])
                    ->andWhere(['in','industry_appid',$industry_appid])//得到指定应用的权限节点
                    ->andFilterWhere(['is_platform'=>$is_platform])
                    ->orderBy(['sort'=>SORT_DESC,'id'=>SORT_DESC])->asArray()->all();
        //重新组装数组结构
        $reArr = TreeHelper::getSubs($all_node);
        $reArr = $this->changeSubsEdit($reArr, '');
        return $reArr;
    }
}

```

## auth_assign
> 指派角色 多个
[时序图](../uml/rbac_vding_v1/创建用户_新增角色.oom) 

### auth_assign_列表页
> http://i.vding.dev/user-company/index  

![](rbac/rbac_vding_v1/auth_assign_list.png)

**company/controllers/UserCompanyController.php**

```php
public function actionIndex()
{
    $userCompanyImport = new UserCompanyImport();
    $searchModel = new UserCompanySearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    $company_id = Yii::$app->user->getCompanyId();

    //得到审核通过的应用
    $company_app_data = CompanyApp::find()->where(['company_id'=>$company_id,'audit_status'=>2])->all();
    $industry_appid = [8];//企业基础应用
    if ($company_app_data && count($company_app_data)>0) {
        foreach ($company_app_data as $k=>$v) {
            $industry_appid[] = $v['industry_app_id'];
        }
        array_unique($industry_appid);//去重
    }

    //角色列表
    $authitem_model = AuthItem::find()
                        ->where(['type'=>1,'status'=>1])
                        ->andWhere(['or', ['in','industry_appid',$industry_appid], ['company_id'=>$company_id]])
                        ->all();
    
    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'userCompanyImport'  => $userCompanyImport,
        'authitem_model'=>$authitem_model
    ]);
}
```



### auth_assign_创建页
 
> http://i.vding.dev/user-company/create

![](rbac/rbac_vding_v1/auth_assign_add.png)

![](rbac/rbac_vding_v1/auth_assign_edit.png)

**company/controllers/UserCompanyController.php**

```php
public function actionCreate()
{

    if (Yii::$app->request->isPost) {
        $staff_mobile = Yii::$app->request->post('UserCompany')['staff_mobile'];
        $company_id = Yii::$app->user->getCompanyId();
        $model = UserCompany::find()->where(['company_id'=>$company_id,'staff_mobile'=>$staff_mobile,'is_deleted'=>1])->one();
        
        //如果员工记录存在，则更新状态，否则为创建记录
        if ($model) {
            if ($userRoles = ArrayHelper::getColumn(AuthAssign::getUserRoles($model->user_id), 'auth_item_id')) {
                $model->roleIds = $userRoles;
            }
            
            $model->staff_status = 1;
            $model->is_deleted = 0;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //分配角色
                AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
                if ($model->roleIds) {
                    AuthAssign::create($model->user_id, $model->roleIds);
                }
                
                return $this->redirect('index');
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
        } else {
            $model = new UserCompany();
            $model->scenario = 'add_user_company';
            $model->company_id = Yii::$app->user->getCompanyId();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //分配角色
                AuthAssign::deleteAll(['company_id' => $company_id, 'user_id' => $model->user_id]);
                if ($model->roleIds) {
                    AuthAssign::create($model->user_id, $model->roleIds);
                }
                
                return $this->redirect('index');
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
        
    } else {
        $model = new UserCompany();
        $model->company_id = Yii::$app->user->getCompanyId();
        
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
}
```