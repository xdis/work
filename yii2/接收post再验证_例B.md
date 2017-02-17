

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