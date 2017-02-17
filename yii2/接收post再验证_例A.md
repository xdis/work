

路径：company/modules/admin/controllers/RechargeController.php<br />

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



