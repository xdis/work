
##时间字段年月日显示
````php
[  
  'attribute' => '字段名',  
   //'label' => '充值日期',  
  'value' => function ($model) {  
   return date('Y-m-d H:i:s', $model->字段名);  
   },  
],  
``