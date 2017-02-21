

## 接收post再验证_标准例子

```

/** 
 * 1,将之前的栏目全部删除 BlogCategory 
 * 2.将前台选中栏目多个批量入库_BlogCategory栏目 
 *  
 */ 
public function actionUpdate($id) 
{ 
    $model = $this->findModel($id); 
 
    if ($model->load(Yii::$app->request->post()) && $model->validate()) 
    { 
 
        $transaction = Yii::$app->db->beginTransaction(); 
        try 
        { 
 
            /** 
             * current model save 
             */ 
            $model->save(false); 
 
            // 注意我们这里是获取刚刚插入blog表的id 
            $blogId = $model->id; 
 
            /** 
             * batch insert category 
             * 我们在Blog模型中设置过category字段的验证方式是required,因此下面foreach使用之前无需再做判断 
             */ 
            $data = []; 
            foreach ($model->category as $k => $v) 
            { 
                // 注意这里的属组形式[blog_id, category_id]，一定要跟下面batchInsert方法的第二个参数保持一致 
                $data[] = [$blogId, $v]; 
            } 
 
            // 获取BlogCategory模型的所有属性和表名 
            $blogCategory = new BlogCategory; 
            $attributes = array_keys($blogCategory->getAttributes()); 
            $tableName = $blogCategory::tableName(); 
 
            // 先全部删除对应的栏目 
            $sql = "DELETE FROM `{$tableName}`  WHERE `blog_id` = {$blogId}"; 
            Yii::$app->db->createCommand($sql)->execute(); 
 
            // 再批量插入栏目到BlogCategory::tableName()表 
            Yii::$app->db->createCommand()->batchInsert( 
                $tableName, 
                $attributes, 
                $data 
            )->execute(); 
 
            // 提交 
            $transaction->commit(); 
 
            return $this->redirect(['index']); 
 
        } 
        catch (Exception $e) 
        { 
            // 回滚 
            $transaction->rollback(); 
            throw $e; 
        } 
 
    } 
    else 
    { 
 
        // 获取博客关联的栏目 
        $model->category = BlogCategory::getRelationCategorys($id); 
 
        return $this->render('update', [ 
                                 'model' => $model, 
                             ]); 
    } 
} 



```

##接收post再验证_例a

路径：company/modules/admin/controllers/RechargeController.php  

```php
 public function actionCreate() {

        $model = new Recharge();
        $model->setScenario(Recharge::OFFLINE_ADD);

        if (Yii::$app->request->isPost) {
            $posts = Yii::$app->request->post();
            try {
                //开起事务  
                $trans = Yii::$app->db->beginTransaction();
                //1.插入数据  recharge
                $data = $posts;
                $data['Recharge']['pay_at'] = strtotime($posts['Recharge']['pay_at']);
                $data['Recharge']['order_no'] = getRangNUm(10);
                $data['Recharge']['created_by'] = getMyId();
                $data['Recharge']['type'] = 0;
//                dp($data);
                $model->load($data);
                $model->setIsNewRecord(true);
                $result = $model->save();
                $relation_id = Yii::$app->db->getLastInsertID();
                if (!$result) {
                    throw new \Exception('充值入库失败');
                }

                //2.日志入库 operation-log
                $data['OperationLog'] = ['user_id' => getMyId(), 'relation_type' => 3, 'relation_id' => $relation_id, 'name' => '线下充值'];
                $order = new OperationLog;
                $order->load($data);
                $result = $order->save(FALSE);
                if (!$result) {
                    throw new \Exception('日志入库失败');
                }

                $trans->commit();
                Yii::$app->session->setFlash('noty', [
                    'text' => '操作成功',
                    'type' => Noty::SUCCESS
                ]);
                return $this->redirect('index');
            } catch (\Exception $e) {
                $trans->rollback();
                Yii::$app->session->setFlash('noty', [
                    'text' => $e->getMessage(),
                    'type' => Noty::ERROR
                ]);
                return $this->render('create', ['model' => $model]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }



```

##接收post再验证_例b
>(注:$model->save(false))

```php
use common\models\Blog; 
use yii\base\Exception; 
use common\models\BlogCategory; 
public function actionCreate() 
{ 
    $model = new Blog(); 
    // 注意这里调用的是validate，非save,save我们放在了事务中处理了 
    if ($model->load(Yii::$app->request->post()) && $model->validate()) { 
        // 开启事务 
        $transaction = Yii::$app->db->beginTransaction(); 
        try { 
            /** 
             * current model save 
             */ 
            $model->save(false);  //前面$model->validate()已经验证，没有必要再验证，设置为false则不需要再验证
            // 注意我们这里是获取刚刚插入blog表的id 
            $blogId = $model->id; 
            /** 
             * batch insert category 
             * 我们在Blog模型中设置过category字段的验证方式是required,因此下面foreach使用之前无需再做判断 
             */ 
            $data = []; 
            foreach ($model->category as $k => $v) { 
                // 注意这里的属组形式[blog_id, category_id]，一定要跟下面batchInsert方法的第二个参数保持一致 
                $data[] = [$blogId, $v]; 
            } 
            // 获取BlogCategory模型的所有属性和表名 
            $blogCategory = new BlogCategory; 
            $attributes = array_keys($blogCategory->getAttributes()); 
            $tableName = $blogCategory::tableName(); 
            // 批量插入栏目到BlogCategory::tableName()表,第一个参数是BlogCategory对应的数据表名，第二个参数是该模型对应的属性字段，第三个字段是我们需要批量插入到该模型的字段，记得第二个参数和第三个参数对应值一致哦 
            Yii::$app->db->createCommand()->batchInsert( 
                $tableName,  
                $attributes, 
                $data 
            )->execute(); 
            // 提交 
            $transaction->commit(); 
            return $this->redirect(['index']); 
        } catch (Exception $e) { 
            // 回滚 
            $transaction->rollback(); 
            throw $e; 
        } 
    } else { 
        return $this->render('create', [ 
            'model' => $model, 
        ]); 
    } 
} 


```

#yii2自带函数连接

##leftjoin
```php

```
public function actionView($id) {

        //操作记录
        //$query = OperationLog::find();
        //$query->

        /*  注：下面的 select($fields),查出来的数据只有一条，晕死，，坑。。。。即现在的解决方案就是select('operation_log.*')
        $fields = [
            'operation_log.*',
            'operation_log.user_id',
            'operation_log.name',
            'operation_log.memo',
            'operation_log.created_at',
            'operation_log.relation_type',
            'operation_log.relation_id',
            'user.*',
            'user.username',

        ];
        */

        $op_logs = OperationLog::find()->select('operation_log.*,user.username')->where('relation_id=:_id and relation_type = 9', [':_id' => $id]);
        $_op_logs = $op_logs->leftJoin('user', 'user.id = operation_log.user_id')->asArray()->all();

        //dp($_op_logs);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'op_logs' => $_op_logs,
        ]);
    }

##leftjoin_详细页
```php
    public function actionView($id)
    {
        $query = UserActivity::find()->select('user_activity.*,user.username,user.mobile,user_profile.real_name,activity.valid_type,activity.valid_period,activity.valid_end_at')->where('user_activity.id=:_id',[':_id'=>$id]);
        $query->leftJoin('activity','activity.id = user_activity.activity_id');
        $query->leftJoin('user_profile','user_profile.user_id = user_activity.user_id');
        $model = $query->leftJoin('user','user.id=user_activity.user_id')->one();

        return $this->render('view', [
            'model' =>$model,
        ]);
    }
```

##leftjoin_分页_韦庆韦
company/modules/shop/controllers/ProductController.php  
```php
public function pro_list($page, $type)
    {
    	$page = intval($page);
        $proSearch = ProductSearch::find()->select('t.*')->from('product t')->where([
							't.company_id' => $this->store_company_id,
							't.sys_category_id' => $type,
        					't.status' => 1,
        					't.is_published_store' => 1,
        					't.is_on_sale' => 1,
        					't.is_deleted' => 0,
        					't.status' => 1,
			                't.is_passed_card'=>0
    				    ]);
        //价目表日期过期的产品不能显示
        $proSearch->leftJoin('pricelist','pricelist.product_id = t.id');
        $_time = time();
        $proSearch->andWhere("pricelist.end_at+3600*24>{$_time}");

        if ($type == Product::SYS_CATE_SCENIC){
        	$proSearch->leftJoin('product p','p.id = t.original_product_id');
        	$order = 'IF (`p`.is_passed_card IS NULL, `t`.is_passed_card, `p`.`is_passed_card`)DESC, ';
        }else{
        	$order = '';
        }
        $pages = new Pagination([
        		'totalCount' =>$proSearch->count(), 
        		'pageSize' => $this->page_size,
        		'page' => $page <= 0 ? 0 : $page - 1,
		]);
        $models = $proSearch->offset($pages->offset)
        			->limit($pages->limit)
        			->orderBy($order.' t.updated_at DESC')
        			->all();
        
        $datas = array();
        if ($models){
        	foreach ($models as $model){
        		$data = array();
        		$data['id']		= $model->id;
        		$data['name']	= $model->name;
        		
        		if ($model->images && is_array($model->images)){
        			$data['img']= $model->images[0]->base_url.'/'.$model->images[0]->path;
        		}else {
        			$data['img'] = '';
        		}
        		
        		if ($type == Product::SYS_CATE_SCENIC){
        			$data['level']	= $model->supplierApp ? $model->supplierApp->level : 0;
        			$data['is_passed_card'] = $model->originalProduct->is_passed_card;
        		}
        		$data['retail_price']	= $model->defaultPricelist ? $model->defaultPricelist->retail_price : 0;
        		
        		//$data['is_free']= $model->is_free;
        		
        		$datas[] = $data;
        	}
        }
        
        $res['page']		= $pages->page + 1;
        $res['page_count']	= $pages->pageCount;
        $res['items']		= $datas;

        return $this->ajaxSuccess('成功', '', $res);
    }

```

