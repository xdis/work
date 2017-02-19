
##managesearch方法_个性化+自定义配置
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