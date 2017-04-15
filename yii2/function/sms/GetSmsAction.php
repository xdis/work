<?php
/**
 * 获取短信验证码
 * Created by PhpStorm.
 * User: hacklog
 * Date: 12/18/16
 * Time: 5:11 PM
 */

namespace common\actions;

use yii\base\Action;
use yii\base\InvalidParamException;
use Yii;
use ihacklog\sms\models\Sms;

class GetSmsAction extends Action
{
    const ST_CODE_SUCC = 1;
    const ST_CODE_FAIL = 0;

    /**
     * @var string 手机号码
     */
    public $mobile;

    public $initCallback;

    /**
     * @var \Closure
     */
    public $beforeCallback;


    /**
     * @var \Closure
     */
    public $afterCallback;

    public $error = null;

    protected function formatResponse($status, $message = '', $url = '', $data = [])
    {
        return ['status' => $status, 'message' => $message, 'url' => $url, 'data' => $data];
    }

    public function run()
    {
        if ($this->initCallback && ($this->initCallback instanceof \Closure || is_callable($this->initCallback))) {
            call_user_func_array($this->initCallback, [$this]);
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $sms = new Sms();
        if (empty($this->mobile)) {
            return $this->formatResponse(self::ST_CODE_FAIL, '手机号错误', '');
        }
        $beforeCbSucc = true;
        if ($this->beforeCallback && ($this->beforeCallback instanceof \Closure || is_callable($this->beforeCallback))) {
            $beforeCbSucc = call_user_func_array($this->beforeCallback, [$this,]);
            if (!$beforeCbSucc) {
                $error = is_null($this->error) ? 'beforeCallback fail' : $this->error;
                return $this->formatResponse(self::ST_CODE_FAIL, $error, '');
            }
        }
        $sendRs = $sms->sendVerify($this->mobile, mt_rand(1000, 9999), Yii::$app->sms->verifyTemplateId);
        $afterCallbackRet = null;
        if ($this->afterCallback && ($this->afterCallback instanceof \Closure || is_callable($this->afterCallback))) {
            $afterCallbackRet = call_user_func_array($this->afterCallback, [$this, $sendRs]);
        }
        if ($afterCallbackRet) {
            return $afterCallbackRet;
        }
        $statusRet = $sendRs ? self::ST_CODE_SUCC : self::ST_CODE_FAIL;
        return $this->formatResponse($statusRet, $sms->getFirstError('id'), '', ['resendTimeSpan' => Yii::$app->getModule('sms')->resendTimeSpan ]);
    }
}