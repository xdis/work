# Restful Api
## 使用自带与个性化_DEMO版本
>默认有create, delete, update, index, view等的一下方法   
所创建的 API 包括：   
GET /users: 逐页列出所有用户   
POST /users: 创建一个新用户   
GET /users/123: 返回用户 123 的详细信息   
PATCH /users/123 and PUT /users/123: 更新用户123   
DELETE /users/123: 删除用户123   
但是如果你要是不想用他的某些方法,我们可以通过下面的方法来自己覆盖对应的方法   
例如:   

```php
public function actions()  
{  
    $actions = parent::actions();  
  
    // 注销系统自带的实现方法  
    unset($actions['index']);  
      
    //unset($actions['create']);  
    //unset($actions['update']);  
    //unset($actions['delete']);  
  
  return $actions;  
}  
  
//覆盖父类的actionIndex方法,并进行重写  
public function actionIndex()  
{  
    //获取用户所有信息  
    ......  
}  
```

## 使用自带与个性化_syg
rest/versions/v1/callcar/controllers/OrderController.php  
 

**请求URL：** 
- ` http://xx.com/v1/car/order `
  
**请求方式：**
- GET 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|access_token |是  |string |token   |
|type |是  |int |1待接单2已接单3已失效   |



```php
  public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);  //注释掉,不使用该方法
        return ArrayHelper::merge(
            $actions,
            [
                'index'=> [
                    'class' => 'yii\rest\IndexAction',
                    'modelClass' => $this->modelClass,
                    'prepareDataProvider' => function ($action) {
//                        //1待处理，2行程中，3已完成 ，4已取消
                        $type = Yii::$app->request->get('type',1);
                        $company_id = Yii::$app->request->get('company_id',null);
                        $user_id = Yii::$app->user->getId();

                        //订单支付超时 更改状态
                        $this->OverOrderOpt($user_id);

                        $model = new $this->modelClass;
                        $query = $model::find();

                        $query->andWhere(['from_user_id'=>$user_id,'order_type'=>2,'from_company_id'=>$company_id]);

                        $dataProvider = new ActiveDataProvider(
                            [
                                'query' => $query,
                                'pagination' => [
                                    'pageSize' => 10,
                                ],
                                'sort' => [
                                    'defaultOrder'=>['created_at'=>SORT_DESC],
                                ],
                            ]
                        );

                        switch ($type){
                            case 2:
                                $query->andFilterWhere(['and',['reception_status'=> 1],['<>','trip_status',2]]);
                                break;
                            case 3:
                                //已完成订单“待付款”状态的订单排序靠前
                                $query->andFilterWhere(['and',['reception_status'=>1],['trip_status'=>2]])->addOrderBy(['pay_status' => SORT_ASC]);
                                break;
                            case 4:
                                $query->andFilterWhere(['or',['>=','reception_status',2],['<','expiry_at',time()]]);
                                break;
                            default:
                                $query->andFilterWhere(['reception_status'=>0]);
                                break;
                        }
                        return $dataProvider;
                    }
                ],
            ]
        );
    }
```