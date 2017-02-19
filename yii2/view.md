
##时间字段年月日显示
````php
[  
  'attribute' => 'to_at',  
   //'label' => '充值日期',  
  'value' => function ($model) {  
   return date('Y-m-d H:i:s', $model->to_at);  
   },  
],  
``