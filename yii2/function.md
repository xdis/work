# function
## ArrayHelper
```php
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