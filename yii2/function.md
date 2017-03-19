# function
## ArrayHelper
```php
  //例A
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }

```
```php

  //例B
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
//                [['reception_status','trip_status'],'in', 'range' => [1, 2]],
                [
                    ['revoke_reason'],
                    'filter',
                    'filter' => function ($value) {
                        return \Yii::$app->formatter->asHtml($value);
                    }
                ],

                [['reception_status', 'trip_status'], 'filter', 'filter' => 'intval', 'skipOnArray' => true]
                //['customer_pay_amount', 'default', 'value' => 0.00],
                //['customer_pay_amount','compare','compareValue' => 0,'operator' => '>','message'=>'客户支付的值必须大于']
            ]
        );

    }
```