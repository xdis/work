
#店铺

>本店铺采用接口方式获取后端数据
>

>1.使用vue-sui 开发 https://github.com/eteplus/vue-sui-demo  
>2.[安装与运行](#安装与运行)  
>3.[开发环境地址](#开发环境地址)  

##安装与运行
1. git clone git@github.com:eteplus/vue-sui-demo.git
2. npm install //在根目录里执行cmk 安装
3. npm run dev  //运行
4. npm run build //代码修改过后运行  


##核心技术
- [1后端跨域解决方案](shop.md#1后端跨域解决方案)  
- [2怎样在请求头加上Companyid和Ownerid两个参数](shop.md#2怎样在请求头加上Companyid和Ownerid两个参数)   
- [3在请求时候_后端怎样获取Header里的两个参数](shop.md#3在请求时候_后端怎样获取Header里的两个参数)
- [3_1php获取header的函数解析](shop.md#3_1php获取header的函数解析)

##开发环境地址
>localhost:8080


##店铺列表页
![](shop/1.1.shop_list.png)

##头部 Request Headers绑定两个参数 Companyid 和Ownerid

![](shop/1.2.request_header_field.png)

##1后端跨域解决方案
company/modules/shop/controllers/DpBaseController.php  
```php
public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => [
                        'http://localhost',
                        'http://www.*.com',
                        'http://localhost:8080',
                        'http://shop.*.com',
                    ],
                    'Access-Control-Request-Method' => ['POST', 'GET'],
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Companyid', 'Ownerid', 'Cookie', 'Set-Cookie'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }
```


##2怎样在请求头加上Companyid和Ownerid两个参数
>因为店铺是通过前后端分离，则通过前端vue来构建
src/util/util.js  
```js

// vue-resource 封装
export const ajax = (self,method,apiUrl,params,callback) => {
    let company_id = localStorage.getItem('company_id');
    let owner_id = localStorage.getItem('owner_id');
    Vue.http.headers.common['Companyid'] = company_id;
    Vue.http.headers.common['Ownerid'] = owner_id;


    loader.show();

    if (method == 'post'){
        self.$http.post(apiUrl, params)
            .then((response) => {
                loader.hide();
                callback(response);
            })
            .catch(function (response) {
                loader.hide();
            })
    } else {
        self.$http.get(apiUrl, {params: params})
            .then((response) => {
                loader.hide();
                callback(response);
            })
            .catch(function (response) {
                loader.hide();
            })
    }
}
```
###想了一下，如果使用PHP后端怎样构建？
>通过curl的就可以了 


##3在请求时候_后端怎样获取Header里的两个参数

###beforeAction设置
```php
 public function beforeAction($action)
    {
        if (!in_array(strtolower($action->id), $this->not_check_Action) && !in_array(strtolower($this->id), $this->not_check_Controller)) {
            //1. 调用获取header头的函数
			$headlist = getallheaders();
            foreach ($headlist as $k => $v) {
                $header[strtolower($k)] = $v;
            }
            if (!isset($header['companyid'])) {
                return $this->showError('请求头错误，缺少参数companyid');
            }
            if (!isset($header['ownerid'])) {
                return $this->showError('请求头错误，缺少参数ownerid');
            }
            $this->store_company_id = $header['companyid'];
            $this->store_owner_id = $header['ownerid'];
            if (!Yii::$app->shopUser->isGuest) {
                if (Yii::$app->getSession()->get('store_company_id') == $this->store_company_id
                    && Yii::$app->getSession()->get('store_owner_id') == $this->store_owner_id
                ) {
                } else {
                    if ($this->store_owner_id == Yii::$app->shopUser->id
                        && UserCompany::find()->where(['company_id' => $this->store_company_id, 'user_id' => $this->store_owner_id, 'is_opened_store' => 1, 'is_deleted' => 0])->one()
                    ) {
                        Yii::$app->getSession()->set('is_shopkeeper', 1);
                    } else {
                        Yii::$app->getSession()->set('is_shopkeeper', 0);
                    }
                    Yii::$app->getSession()->set('store_company_id', $this->store_company_id);
                    Yii::$app->getSession()->set('store_owner_id', $this->store_owner_id);
                }

            }


        }


        return parent::beforeAction($action);
    }

//2.该函数

```

##3_1php获取header的函数解析  
```php
if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
```


