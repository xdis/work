
##时间字段年月日显示
```php
[  
  'attribute' => '字段名',  
   //'label' => '充值日期',  //强制自定义标题
  'value' => function ($model) {  
   return date('Y-m-d H:i:s', $model->字段名);  
   },  
  //'filter'=>''  //为空，表搜索框隐藏
],  
```

#时间区间范围的选择
>[官方](http://demos.krajee.com/date-range)   
>[composer安装](#安装)  

>使用
>[model设置](#model设置)  
>[view设置](#view设置)  
##安装
```
加入composer.json
"kartik-v/yii2-date-range": "*"
 php composer update
```
##model层
```php
  class UserSearchextendsUser{
	// This attribute will hold the values to filter our database datapublic $created_at_range; 
		
	  //1.将定义区间名字列入safe
		return ArrayHelper::merge(
			[
				[['created_at_range'], 'safe'] // add a rule to collect the values
			],
			parent::rules()
			);
	}
		
	public functionsearch($params){
		$query = $this->finder->getUserQuery();
		$dataProvider = new ActiveDataProvider(
			[
				'query' => $query,
			]);
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
				

		
		//2.获取值
		if(!empty($this->created_at_range) && strpos($this->created_at_range, '-') !== false) {
		    //3.对获取的值，进行切分，赋值要查询两个字段
			list($start_date, $end_date) = explode(' - ', $this->created_at_range);
			$query->andFilterWhere(['between', 'user.created_at', strtotime($start_date), strtotime($end_date)]);
		}		
		// ... more filters here ...return $dataProvider
	}
}

```

##view设置
```php
/* @var $searchModel common\models\UserSearch */// ... lots of code here <?= GridView::widget([
	// ... more code here'columns' => [
		// ... other columns 
		[
                // the attribute
                'attribute' => 'created_at',
                // format the value
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                },
                // some styling? 
                'headerOptions' => [
                    'class' => 'col-md-2'
                ],
                // here we render the widget
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
//                                'language'=>$config['language'],
                    'attribute' => 'created_at_range',
                    'pluginOptions' => [
//				'format' => 'Y-m-d H:i:s',
                        'locale' => [
                            'format' => 'YYYY-MM-DD',
                            'applyLabel' => '确定',
                            'cancelLabel' => '取消',
                            'fromLabel' => '起始时间',
                            'toLabel' => '结束时间',
                            'customRangeLabel' => '自定义',
                            'daysOfWeek' => ['日', '一', '二', '三', '四', '五', '六'],
                            'monthNames' => ['一月', '二月', '三月', '四月', '五月', '六月',
                                '七月', '八月', '九月', '十月', '十一月', '十二月'],
                            'firstDay' => 1
                        ],
                        'autoUpdateInput' => false
                    ]
                ])
            ],
	]
]); ?>
```