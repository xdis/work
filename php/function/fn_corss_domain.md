# 跨域

### php后端跨越的代码

**ApiTrait.php**

```php
/**
 * API扩展
 *
 * Class ApiTrait
 */
trait ApiTrait
{
    /**
     * 设置允许跨域访问的域名白名单
     */
    protected $_ALLOWED_ORIGINS = [
        'test.icewingcc.com'
    ];


    /**
     * 通过指定的参数生成并显示一个特定格式的JSON字符串
     *
     * @param int|array $status 状态码, 如果是数组,则为完整的输出JSON数组
     * @param array     $data
     * @param string    $message
     */
    protected function render_json($status = 200, $data = [], $message = '')
    {

        /*
         * 判断跨域请求,并设置响应头
         */
        $cross_origin = $this->_parse_cross_origin_domain();

        if($cross_origin){
            @header("Access-Control-Allow-Origin: {$cross_origin}");
        }


        @header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie');
        @header('Access-Control-Allow-Credentials: true');

        @header('Content-type: application/json');
        @header("Cache-Control: no-cache, must-revalidate");


        /*
         * 输出格式化后的内容
         */
        echo json_encode([
            'status'  => $status,
            'data'    => $data,
            'message' => $message
        ]);
    }

    /**
     * 解析跨域访问, 如果访问来源域名在 config.inc.php 中预定义的允许的列表中,
     * 则返回完整的跨域允许域名 , 否则将返回FALSE
     *
     * @return bool|string
     */
    private function _parse_cross_origin_domain()
    {
        $refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        $refer = strtolower($refer);

        /*
         * 没有来源地址时直接返回false
         */
        if(! $refer){
            return FALSE;
        }

        /*
         * 解析引用地址, 取出 host 部分
         */
        $refer_parts = parse_url($refer);

        if(! $refer_parts){
            return FALSE;
        }

        $host = isset($refer_parts['host']) ? $refer_parts['host'] : '';
        $scheme = isset($refer_parts['scheme']) ? $refer_parts['scheme'] : 'http';

        if(! $host){
            return FALSE;
        }

        /*
         * 检查引用地址是否在预配置的允许跨域域名列表中,如果不在,返回 FALSE
         */
        if(in_array($host, $this->_ALLOWED_ORIGINS)){

            return ($scheme ? : 'http') . '://' . $host;

        }

        return $host;

    }
}

```

**BaseApiControl.php**

```php
/**
 * 基础API访问类
 *
 * Class BaseApiControl
 */
 abstract class BaseApiControl
 {

    use ApiTrait;

    protected function __construct()
    {
        /*
         * 判断 OPTIONS 请求,如果 请求方式为
         * OPTIONS ,输出头部直接返回
         */
        if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            $this->render_json([]);
            exit();
        }

    }


    // ...

 }
```
