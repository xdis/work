# demo

## yii测试目录结构
- test
    - _data
    - _output # 输出文件夹 
    - _support
    - acceptance # 验收测试代码放置的文件夹
    - bin # 放置的是Yii2测试环境下，可以执行命令文件
    - functional # 功能测试代码放置的文件夹
    - unit  # 单元测试代码放置的文件夹
    - _bootstrap.php # 初始化文件
    - acceptance.suite.yml.example  # 验收测试配置文件
    - functional.suite.yml # 功能测试配置文件
    - unit.suite.yml  # 单元测试配置文件

## yii测试执行
> 将directory>/vendor/bin 增加到你的 PATH 环境变量中
- codecept build,构建测试
- codecept run，运行所有可以运行的测试代码
- codecept run acceptance，运行验收测试的代码
- codecept run functional，运行功能测试的代码
- codecept run unit，运行单元测试的代码

- php vendor/bin/codecept build 或 vendor/bin/codecept build
- php vendor/bin/codecept run 或 vendor/bin/codecept run

```
codecept run //运行正常

codecept run unit 或
codecept run unit SmsTest 会出现下面的问题

>>>>>>>>>>>
[RuntimeException]
  Suite 'unit' could not be found
<<<<<<<<<<<


```
## 一行代码验证短信

![](images/sms.png)

**调用的代码**
```php
['sms_verify_code', SmsValidator::className(), 'template' => 'ihacklog\sms\template\verify\Login', 'mobileNumberAttribute' => 'mobile'],
```

## 短信单元测试_zhou
**tests/codeception/common/unit/SmsTest.php**
```php
namespace tests\codeception\common\unit;

use Yii;
use Codeception\Specify;
use yii\codeception\TestCase as Yii2TestCase;
use ihacklog\sms\template\notice\AdminAuditPass;
use ihacklog\sms\template\notice\AdminAuditReject;
use ihacklog\sms\models\Sms;
use ihacklog\sms\template\verify\Login;
use ihacklog\sms\demo\LoginForm;

class SmsTest extends Yii2TestCase
{
    public $appConfig = '@tests/codeception/config/common/unit.php';

    /**
     * @var \tests\codeception\common\UnitTester
     */
    protected $tester;


    protected function _before()
    {
    }


    protected function _after()
    {
    }

    /**
     * 测试模板解析（通过审核）
     */
    public function testTemplateAdminAuditPass()
    {
        $smsAuditPass = new AdminAuditPass();
        $content = $smsAuditPass->parseTemplate('x科技公司', '银行卡审核');
        echo $content;
        $this->assertTrue(!empty($content));
        $this->assertTrue($content === '您提交的x科技公司的银行卡审核申请已通过审核。');
    }

    /**
     * 测试模板解析（拒绝审核）
     */
    public function testTemplateParseAdminAuditReject()
    {
        $smsAuditPass = new AdminAuditReject();
        $content = $smsAuditPass->parseTemplate('x科技公司', '银行卡审核', '信息不完整');
        echo $content;
        $this->assertTrue(!empty($content));
        $this->assertTrue($content === '您提交的x科技公司的银行卡审核申请未通过审核，拒绝原因：信息不完整。');
    }

    /**
     * 测试验证码类短信发送与验证(106通道）
     */
    public function testVerifySmsSendAndVerify() {
        $sms = new Sms();
        $mobile = $sms->getModule()->testMobileNumber;
        $veryCode = mt_rand(1000, 9999);
        $loginTemplate = new Login();
        $sendRs = $sms->sendVerify($mobile, $loginTemplate, $veryCode);
//        var_dump($sms->getErrors());die();
        $this->assertTrue($sendRs == true);
        //验证
        $verRs = $sms->verify($mobile, $loginTemplate, $veryCode);
        $this->assertTrue($verRs == true);
    }

    /**
     * 测试通知类短信发送
     */
    public function testNoticeSmsSend() {
        sleep(1);
        $sms = new Sms();
        $sms->getModule()->resendTimeSpan = 1;
        $mobile = $sms->getModule()->testMobileNumber;
        $auditTemplate = new AdminAuditReject();
        $sendRs = $sms->sendNotice($mobile, $auditTemplate,
            'super-man科技有限公司', '银行卡6228480********' . mt_rand(1000, 9999) . '审核', '资料不全');
        $this->assertTrue($sendRs == true);
    }

    public function testValidatorOK()
    {
        sleep(1);
        $sms = new Sms();
        $mobile = $sms->getModule()->testMobileNumber;
        $sms->getModule()->resendTimeSpan = 1;
        $veryCode = mt_rand(1000, 9999);
        $loginTemplate = new Login();
        $sendRs = $sms->sendVerify($mobile, $loginTemplate, $veryCode);
        $this->assertTrue($sendRs == true);

        //let's start validate
        $form = new LoginForm();
        $form->mobile = $mobile;
        $form->sms_verify_code = $veryCode;
        $this->assertTrue($form->validate() == true);
    }

    public function testValidatorErr()
    {
        sleep(1);
        $sms = new Sms();
        $sms->getModule()->resendTimeSpan = 1;
        $mobile = $sms->getModule()->testMobileNumber;
        $veryCode = mt_rand(1000, 9999);
        $loginTemplate = new Login();
        $sendRs = $sms->sendVerify($mobile, $loginTemplate, $veryCode);
        $this->assertTrue($sendRs == true);

        //let's start validate
        $form = new LoginForm();
        $form->mobile = $mobile;
        $form->sms_verify_code = '1234';
        $this->assertFalse($form->validate() == true);
    }
}

```
