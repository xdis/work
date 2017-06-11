<?php

namespace company\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use company\models\AuthItem as AuthItemModel;
//use common\models\CompanyApp;

/**
 * AuthItem represents the model behind the search form about `company\models\AuthItem`.
 */
class AuthItem extends AuthItemModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'company_id', 'parent_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'url', 'description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //得到审核通过的应用
        $company_app_data = CompanyApp::find()->where(['company_id'=>Yii::$app->user->getCompanyId(),'audit_status'=>2])->all();
        $industry_appid = [8];//企业基础应用
        if ($company_app_data && count($company_app_data)>0) {
            foreach ($company_app_data as $k=>$v) {
                $industry_appid[] = $v['industry_app_id'];
            }
            array_unique($industry_appid);//去重
        }
        
        $query = AuthItemModel::find();
        $query->andWhere(['or', ['company_id' => Yii::$app->user->getCompanyId()], ['in','industry_appid',$industry_appid]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
