## 抽象类
### 基本的定义与使用

>定义方法,不需要{}和{}里的内容  

**抽象定义**
```php
abstract class Message
{
    protected $mobileList = [];
    protected $errorList = [];
    abstract public function send();
    abstract protected function getErrors();
}
```

**抽象使用**
```php
class Aliyun extends Message
{
  public function __construct( $mobile )
  {
      $this->mobileList[] = $mobile;
  }
  public function send($int,$info = 'xxxx')
  {
      if( $int )
          echo '消息发送给：'.implode(',', $this->mobileList).$info;
  }
  public function getErrors(){
      return '错误信息：'.implode(',', $this->errorList);
  }
}
$aliyun_message = new Aliyun('18682395282');
$aliyun_message->send(1);
```

## Traits	代码复用的一个方法
### 删除的公共方法例子

**定义**
```php
namespace common\service;
/**
 * 删除的公共方法
 * @author Jack    
 */
trait DeleteService {
    /**
     * 通过主键删除
     * @param int $id
     * @return int | boolean
     */
    public static function deleteByPk( $id )
    {
        $model = static::find($id);
        return $model->delete();
    }
    /**
     * 批量删除
     * @param string $condition
     * @param array $params
     */
    public static function deleteAll( $condition = '', $params = [] )
    {
        return static::deleteAll( $condition = '', $params = [] );
    }
}
```

**使用**
```php
<?php
namespace common\service;

use common\service\IUser;
use common\service\DeleteService;
use common\service\UpdateService;
/**
 * @author Jack
 * from www.aipanshi.com
 */
class UserService extends \models\User implements IUser
{
    use DeleteService, UpdateService;
    
}
```




```php

```