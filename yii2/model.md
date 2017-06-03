# model关联多表的使用
1. model的配置
2. 列表页设置

## managesearch方法_个性化+自定义配置
company/modules/coshop/models/search/ProductSearch.php  
```php
 public function managesearch($params)
    {
        $company_id = Yii::$app->user->getCompanyId();//对应的产品表公司ID
        $query = Product::find();
        $query->joinWith(['supplierApp', 'dpLine', 'sourceProduct']);
        $sql = "product.*,supplier_app.address,dp_line.start_address,source_product.number as p_number,source_product.name as p_product_name";
        $sql .= ",(select pricelist.id from pricelist where pricelist.product_id =product.id and  pricelist.is_default = 1 limit 1) as pricelist_id";
        $sql .= ",(SELECT IFNULL(SUM(dp_order_detail.quantity),0) AS _quantity FROM dp_order_detail LEFT JOIN dp_order ON dp_order.id = dp_order_detail.order_id WHERE dp_order_detail.product_id=product.id) as _sell_stock";//产品销量 (自己卖)

        //$sql .= ",(SELECT IFNULL(SUM((FLOOR((pricelist.end_at - pricelist.start_at)/(3600*24))+1) * pricelist.max_count),0) FROM pricelist WHERE pricelist.product_id = product.id ) as _total_stock";//产品总库存
        //$sql .=",(SELECT IFNULL(SUM(dp_order_detail.quantity),0) FROM dp_order_detail  WHERE dp_order_detail.product_id  = product.id) AS _all_sell_quantity "; //  （多级卖出的数据 ）

       //分销商取源头product.original_product_id，否则取product.id
        $sql .=",(select f_get_product_stock(if(product.original_product_id,product.original_product_id,product.id),-1)) as _total_stock";
        $sql .=",(select f_get_supplier_name(product.id )) as supplier_name";
        $query->select(new Expression($sql));
        //$query->where('product.is_deleted=0');
        // echo  $query->createCommand()->getRawSql();exit;
//        dp($params);
        //上下架状态//状态  -1 全部 -2 已售完 1 上架 0 下架
        switch (intval($params['is_on_sale'])) {
            case -1:
                $query->where('product.company_id=:_company_id', ['_company_id' => $company_id]);
                break;
            case -2:
                $query->where('(SELECT IFNULL(SUM((ROUND((pricelist.end_at - pricelist.start_at)/(3600*24))+1) * pricelist.max_count),0) FROM pricelist WHERE pricelist.product_id = product.id ) - (SELECT IFNULL(SUM(dp_order_detail.quantity),0) AS _quantity FROM dp_order_detail LEFT JOIN dp_order ON dp_order.id = dp_order_detail.order_id WHERE dp_order_detail.product_id=product.id) = 0  and product.company_id=:_company_id', ['_company_id' => $company_id]);
                break;
            case 0:
                $query->where('product.is_on_sale= 0 and product.company_id=:_company_id', ['_company_id' => $company_id]);
                break;
            case 1:
                $query->where('product.is_on_sale= 1 and product.company_id=:_company_id', ['_company_id' => $company_id]);
                break;
            default:
                break;
        }

        $query->andWhere('product.is_deleted=0 and product.is_published_store = 1 and product.is_free = 0 ');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        //  echo $query->createCommand()->getRawSql();exit;
        $dataProvider->setSort([
            'attributes' =>
                ArrayHelper::merge(
                    [
                        'address', 'start_address', 'supplier_name'
                    ], array_keys(parent::attributeLabels())
                )
        ]);

        //  dp($params);
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'supplier_id' => $this->supplier_id,
            'supplier_company_id' => $this->supplier_company_id,
            'category_id' => $this->category_id,
            'status' => $this->status,
            'product.sys_category_id' => $this->sys_category_id,
            'is_refund' => $this->is_refund,
            'max_count' => $this->max_count,
            'original_product_id' => $this->original_product_id,
            'parent_product_id' => $this->parent_product_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
//        dp($this->address);
        $query->andFilterWhere(['like', 'product.name', $this->name])
            ->andFilterWhere(['like', 'source_product.name', $this->p_product_name])
            ->andFilterWhere(['like', 'source_product.number', $this->p_number])
            ->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
            ->andFilterWhere(['like', 'product.number', $this->number])
            ->andFilterWhere(['like', 'supplier_app.address', $this->address])
            ->andFilterWhere(['like', 'dp_line.start_address', $this->start_address]);

        return $dataProvider;
    }


```

## 使用子查询使用
>如上例子中，使用子查询来获取指定的值 如 _total_stock
>index.php  使用 $model->_total_stock
>关于这个关联表的配置，可以看下面例子如配置模型里1.变量 2.命名显示 3.rule加入safe等
#在列表页index.php的使用如下
```php

    [
        'attribute' => '_total_stock',
        'value' => function ($model) {
            return  (!$model->max_count ||  $model->max_count ==0) && ($model->original_product_id == 0) ? '不限制' :  ($model->_total_stock == 0 && $model->_sell_stock ==0 ?  '未设价目' : $model->_total_stock );
        },
        'filter' => '',
    ],

```

## 列表页使用
index.php  
>使用关联表的字段  

```php
[// 线路 - 出发地
    'attribute' => 'start_address',
    'value' => function ($model) {
        return isset($model->dpLine->start_address) ? $model->dpLine->start_address : null;
    },
    'filter' => Html::activeTextInput($searchModel, 'start_address', ['class' => 'form-control']),
    'visible' => intval(Yii::$app->request->get('type'))  == 2
],
```


# search关联用户表取username

>举例场景 
>在用户奖券表(user_activity表)里显示的列表里，显示有user表的username

![](model/activity_list.png)

- 配置model
	- [1.配置UserActivity模型](model.md#1配置useractivity模型)
	- [2.配置UserActivitySearch模型](model.md#2配置useractivity_search模型)
	- [3.列表页显示](model.md#3列表页显示)

## 1配置useractivity模型
```php
class UserActivity extends \common\models\***Model{
  //1.定义变量
  public $username;     #关联user表

  //2.设置显示的命名
    public function attributeLabels()
    {
        return [
			...
            'username' => '姓名',
            ...

        ];
    }
 
  //3.关联的表
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

```

## 2配置useractivity_search模型
```php
class UserActivitySearch extends UserActivity{

   //1.在规则里将 username列入safe   
    public function rules()
    {
        return [
            [
 			...
            [['username'], 'safe'],
			...
        ];
    }

  public function search($params)
    {
        $query = UserActivity::find();

		/*1.定义关联user表，这个user是 UserActivity下的函数getUser
          注：如果该函数下的getUserLog的话，这个时候调用的命名应该为 userLog
		 (格式：第一个大写省略，第二个大写要写上，否则会报错)
		*/  
        $query->joinWith(['user']);  
        $sql ="user_activity.*,user.username"; 

        $query->select(new Expression($sql));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //2.关联表的字段入库，否则在列表页不能搜索
        $dataProvider->setSort([
            'attributes' =>
                ArrayHelper::merge(
                    [
                        'username'
                    ], array_keys(parent::attributeLabels())
                )
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'activity_id' => $this->activity_id,
            'ticket_type' => $this->ticket_type,
            'amount' => $this->amount,
            'status' => $this->status,
            'used_at' => $this->used_at,
            'valid_start_at' => $this->valid_start_at,
            'valid_end_at' => $this->valid_end_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            //3.增加搜索条件
            ->andFilterWhere(['like','username',$this->username]);

        return $dataProvider;
    }



}

```

## 3列表页显示
index.php
```php
		[//关联表user.name
            'attribute' => 'username',
            'value' => function ($model) {
                return isset($model->user->username) ? $model->user->username : null;
            },
            'filter' => Html::activeTextInput($searchModel, 'username', ['class' => 'form-control']),
        ],

```

# search关联用户表取mobile以别名形式

>举例场景 
>在用户奖券表(user_activity表)里显示的列表里，显示有user表的mobile,与上面例子不一样的是，我们以别名的方式（关联多表时，相同的字段是有的，即会采用别名）

![](model/activity_list_mobile.png)

- 配置model
	- [a.配置UserActivity模型](model.md#a配置useractivity模型)
	- [b.配置UserActivitySearch模型](model.md#b配置useractivity_search模型)
	- [c.列表页显示](model.md#c列表页显示)

## a配置useractivity模型
```php

class UserActivity extends \common\models\***Model{
  //1.定义变量
 public $user_mobile;   #关联user表

  //2.设置显示的命名
    public function attributeLabels()
    {
        return [
			...
            'user_mobile' => '手机号码',
            ...

        ];
    }
 
  //3.关联的表
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
```

## b配置useractivity_search模型

```php
 class UserActivitySearch extends UserActivity{

   //1.在规则里将 username列入safe   
    public function rules()
    {
        return [
            [
 			...
            [['user_mobile'], 'safe'],
			...
        ];
    }

  public function search($params)
    {
        $query = UserActivity::find();

		/*1.定义关联user表，这个user是 UserActivity下的函数getUser
          注：如果该函数下的getUserLog的话，这个时候调用的命名应该为 userLog
		 (格式：第一个大写省略，第二个大写要写上，否则会报错)
		*/  
        $query->joinWith(['user']);  
        $sql ="user_activity.*,user.mobile as user_mobile"; 

        $query->select(new Expression($sql));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        //2.关联表的字段入库，否则在列表页不能搜索
        $dataProvider->setSort([
            'attributes' =>
                ArrayHelper::merge(
                    [
                        'user_mobile'
                    ], array_keys(parent::attributeLabels())
                )
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

		...
        $query
            ->andFilterWhere(['like', 'name', $this->name])
            //3.增加搜索条件(注：搜索的字段不能别名来搜索)
            ->andFilterWhere(['like','user.mobile',$this->user_mobile]);

        return $dataProvider;
    }

}

```

## c列表页显示

```php
index.php
```php
[//关联表user.name
    'attribute' => 'username',
    'value' => function ($model) {
        return isset($model->user->user_mobile) ? $model->user->user_mobile : null;
    },
    'filter' => Html::activeTextInput($searchModel, 'user_mobile', ['class' => 'form-control']),
],

```

# 条件过滤_where
##where_普通使用
```php
$query->andwhere('product.sys_category_id = ' . $type);
```

## where_大于小于
```php
 $query->andFilterWhere(['>', 'pricelist.start_at', $_start_at]);
 $query->andFilterWhere(['<', 'pricelist.end_at', $_end_at]);

//控制器-使用过程 
$query = Pricelist::find();
$query->select($select)->where(['product_id' => $product_id, 'status' => 1, 'type' => 1]);
$res = $query->andFilterWhere(['<', 'pricelist.start_at', strtotime($date)])->asArray()->all();
if (!$res) {
    throw new \Exception('该日期没有价目');
} 
```

## where_in
```php
['id' => [1, 2, 3], 'status' => 2]
```

## where_or
```php
$users = User::find()
   ->andWhere(['or', ['username'=>$this->username], ['mobile'=>$this->username]])
   ->all();
```

## where_like
```php
  $query->andFilterWhere(['like', 'product.name', $_product_name]);
  $query->andFilterWhere(['like', 'pricelist.name', $_pricelist_name]);
```

## Expression()  查询不过滤
### sql语句不过滤
**可以在本文件里搜索一下 Expression 有更多的例子**

```php
$_db_all_dates = Pricelist::find()->select(new Expression("(FROM_UNIXTIME(pricelist . start_at, '%Y-%m-%d')) as _start_at"))->where(['product_id' => $product_id, 'type' => 2, 'city_id' => $city_id])->asArray()->all();
```


---
# 自定义场景
- [model定义](model.md#model定义)
- [controller使用](model.md#controller使用)

## model定义
```php
class SignupForm extends Model{
 const SCENARIO_GET_SMS = 'sms';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
		...
		//场景要过滤的字段
        $scenarios[self::SCENARIO_GET_SMS] = ['username', 'mobile', 'password', 'user_type'];
    }
}
```

## controller使用
```php
class SignInController extends BaseController{
    public function registerSmsBeforeCallback($action)
    {
        $model = new SignupForm();
        //1.定义场景
        $model->setScenario(SignupForm::SCENARIO_GET_SMS);
        $model->load(Yii::$app->getRequest()->post());
        if ($model->validate()) {
            return true;
        }
        $action->error = current($model->getErrors())[0];
        return false;
    }
}
```
---
# 指定字段的自定义函数过滤
common/models/Activity.php
## 场景
当这个字段valid_type为0时，必须要输入天数，为1时，必须要输入时间
```php
    public function rules() {
        return [
            [['from_at', 'to_at', 'name','amount','sent_condition'], 'required'],
            [['valid_type'], 'check_valid_type'],
        ];
    }

    /**
     * 奖券有效期选择后判断
     * @author cmk
     */
    function check_valid_type() {
        if (!$this->hasErrors()) {
            if ($this->valid_type == 0) {
                if (!$this->valid_period) {
                    $this->addError('valid_period', '奖券有效期 - 天数还没有填');
                }
            }
            if ($this->valid_type == 1) {
                if (!$this->valid_end_at) {
                    $this->addError('valid_end_at', '奖券有效期 - 截止日期还没有填');
                }
            }
        }
    }
```

### 登陆获取公司ID_复杂的例子_zhou
```php
public function validateCompanyId($attr)
    {
        $usernameOrMobile = Yii::$app->user->getPreLoginMobile();
        $this->username = $usernameOrMobile;
        //do not validate ucenter login
        if ($this->company_id == 0) {
            $user = $this->getPersonUser();
            $this->user = $user;
            return true;
        }
        $user = $this->getCompanyUser();
        if (!$user) {
            $this->addError($attr, '公司登录错误!');
        }
        $company = Company::findOne($this->$attr);
        if (!$company) {
            $this->addError($attr, '请选择公司!');
        }
        $dimissioned = UserCompany::find()->where(['user_id' => $user->id, 'company_id' => $company->id, 'staff_status' => 0])->count() > 0;
        if ($dimissioned) {
            $this->addError($attr, '已经离职，不能登录!');
        }
        if (!AuthAssign::userHasRole($user->id, $company->id, 4) && $user->id != $company->user_id) {
            $this->addError($attr, '您没有权限登录此公司!');
        }
        $this->user = $user;
    }
```


## rules
### 自定义过滤函数filter

```php
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
//                [['reception_status','trip_status'],'in', 'range' => [1, 2]],
                [
                    ['revoke_reason'],
                    'filter',
                    'filter' => function ($value) {
                        return \Yii::$app->formatter->asHtml($value);
                    }
                ],

                [['reception_status', 'trip_status'], 'filter', 'filter' => 'intval', 'skipOnArray' => true]
                //['customer_pay_amount', 'default', 'value' => 0.00],
                //['customer_pay_amount','compare','compareValue' => 0,'operator' => '>','message'=>'客户支付的值必须大于']
            ]
        );

    }
```

## setIsNewRecord()使用

### 创建账户，如果不存在才创建.存在则直接返回账户id_wqw
common/models/Account.php  
```php
    /**
     * 创建账户，如果不存在才创建.存在则直接返回账户id
     * @param $id
     * @return bool
     */
    public function createAccount($id)
    {
        $this->setIsNewRecord(true); //设置插入模式! 因为 save有可能是修改！
        $exists = self::findOne($id);
        if (!$exists) {
            $this->balance = 0;
            $this->frozen = 0;
            $this->reward = 0;
            return $this->save();
        }
        return $id;
    }
```

或者 等于复制，如查询出来的结果，然后修改，使用save()插入一个新的数据  

```php
$record = Record::find(123);
$record->primaryKey = null;
$record->isNewRecord = true;
$record->save();
```

##  link() 更新
>注: link()第一参数名为定义关联表名字,如 getCustomer()

### 获取Customer的主键,然后order表同时存储该Customer主键

```php
//传统的方法
$customer = Customer::findOne(123);
$order = new Order();
$order->subtotal = 100;
// ...

// setting the attribute that defines the "customer" relation in Order
$order->customer_id = $customer->id;
$order->save();

//使用link()

public function getCustomer()
{
    return $this->hasOne(Customer::className(), ['user_id' => 'id']);
}

$customer = Customer::findOne(123);
$order = new Order();
$order->subtotal = 100;
// ...

$order->link('customer', $customer);

```

### 双向更新_link
common/models/User.php  
```php

    /** 分析1 定义名字
     * 关联个人资料
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /** 分析2 定义名字
     * @inheritdoc
     */
    public function getAccount()
    {
    	return $this->hasOne(Account::className(), ['id' => 'id']);
    }

    /** 分析3 定义名字
     * 关联公司
     */
    public function getCompanyInfo()
    {
        return $this->hasOne(Company::className(), ['user_id' => 'id']);
    }

  /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh(); //分析1

        /**
		 * 向timeline_event表插入数据,意图是标识注册来源,什么时候注册等基本信息	  
        */
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));

        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);//分析1

        $account = new Account();
        $account->id = $this->getId();
        $this->link('account', $account); //分析2

        if ($this->user_type == self::USER_TYPE_COMPANY) {
            //添加公司
            $company = new Company();
            $company->name = $this->username;
            $company->brand_name = $this->username;
            $company->user_id = $this->getId();
            $company->city_name = '1';
            $company->address = '';
            $company->contact_name = '';
            $company->contact_phone = '';
            $company->delegate_name = '';
            $company->delegate_idcard = '';
            $company->license_path = '';
            $company->business_license = '';
            $company->request_by = $this->getId();
            $this->link('companyInfo', $company); //分析3

            //插入公司后自动初始化产品默认产品分类数据
            if ($this->companyInfo && is_object($this->companyInfo)) {
                $company_id = $this->companyInfo->id;
                $result = Yii::$app->db->createCommand('CALL p_add_com_product_category_a(:company_id)')
                    ->bindValue(':company_id', $company_id)
                    ->execute();
            }
/*            $userCompany = new UserCompany();
            $userCompany->user_id = $this->getId();
            $userCompany->company_id = '';*/
        }

        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
//        $auth = Yii::$app->authManager;
//        $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
    }

```

## save
**company/models/UserCompany.php**   

### beforeSave
```php
/**
 * @param bool $insert
 * @return bool
 */
public function beforeSave($insert)
{
    if (parent::beforeSave($insert)) {
        $this->company_id = Yii::$app->user->getCompanyId();
        $this->user_id = $this->mobileUser->id;
        return true;
    } else {
        return false;
    }
}
```

### afterSave

```php
/**
 * @param bool $insert
 * @param array $changedAttributes
 * @throws Exception
 */
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    if (!empty($changedAttributes['department_id'])) {
        Department::updateAllCounters(['staff_amount' => -1], ['id' => $changedAttributes['department_id'], 'company_id' => $this->company_id]);
    }
    Department::updateAllCounters(['staff_amount' => 1], ['id' => $this->department_id, 'company_id' => $this->company_id]);
    // 添加和更新个人店铺
    $company_store = DpStore::find()->where(['company_id' => $this->company_id, 'type' => DpStore::TYPE_COMPANY])->one();
    if ($company_store) {
        $conditions = ['company_id' => $this->company_id, 'ower_id' => $this->user_id, 'type' => DpStore::TYPE_PERSON];
        $model = DpStore::findOne($conditions) ?: new DpStore();
        $model->setAttributes($conditions + [
            'status' => $this->is_opened_store,
            'parent_store_id' => DpStore::getParentId(['company_id' => $this->company_id]) ?: 0,
            'store_url' => get_person_html5_shop_url($this->company_id, $this->user_id),
            'created_by' => Yii::$app->user->id
        ]);

        if (!$model->save()) {
            throw new Exception(Json::encode($model->firstErrors));
        }
    }
}
```

### afterDelete

```php
public function afterDelete()
{
    parent::afterDelete();
    AuthAssign::deleteAll(['user_id' => $this->user_id, 'company_id' => $this->company_id]);
}
```