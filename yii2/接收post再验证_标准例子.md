

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