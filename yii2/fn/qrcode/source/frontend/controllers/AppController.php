<?php
/**
 * 应用下载链接 和二维码
 * Created by PhpStorm.
 * Author: HuangYeWuDeng
 * Date: 1/12/17
 * Time: 12:46 PM
 */

namespace frontend\controllers;

use League\Flysystem\FileNotFoundException;
use Yii;
use dosamigos\qrcode\QrCode;
use dosamigos\qrcode\lib\Enum;
use \yii\base\Controller;

class AppController extends Controller
{
    protected function parseConfig()
    {
        $base_app_dir = Yii::getAlias('@frontend/web/app/');
        $conf = parse_ini_file($base_app_dir . 'ver.txt', true);
        return $conf;
    }

    protected function createUrl($app_platform, $app_ver)
    {
        $conf = $this->parseConfig();
        if (!isset($conf[$app_platform]) || !isset($conf[$app_platform][$app_ver])) {
            throw new FileNotFoundException('sys config err.');
        }
        switch ($app_platform) {
            case 'ios':
                $url = $conf[$app_platform][$app_ver];
                break;
            case 'android':
                $url = (strpos($conf[$app_platform][$app_ver], 'http') === 0 )?  $conf[$app_platform][$app_ver] :
                    Yii::$app->urlManagerFrontend->createAbsoluteUrl(['app/download', 'app_platform' => $app_platform, 'app_ver' => $app_ver]);
                break;
        }
        return $url;
    }

    public function actionQrcode()
    {
        Yii::$app->getResponse()->format = 'raw';
        $app_platform = Yii::$app->request->get('app_platform', 'android');
        $app_ver = Yii::$app->request->get('app_ver', 'latest');
        $size = Yii::$app->request->get('size', '3');
        $margin = Yii::$app->request->get('margin', '3');
        $size = ($size > 1 && $size < 256) ? $size : 6;
//        $url = $this->createUrl($app_platform, $app_ver);
        $url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.vding.wang';
        return QrCode::png($url, false, Enum::QR_ECLEVEL_L, $size, $margin);
    }

    public function actionDownload()
    {
        $app_platform = Yii::$app->request->get('app_platform', 'android');
        //latest or stable
        $app_ver = Yii::$app->request->get('app_ver', 'latest');
        $app_platform = strtolower($app_platform);
        $conf = $this->parseConfig();
        if (!isset($conf[$app_platform]) || !isset($conf[$app_platform][$app_ver])) {
            throw new FileNotFoundException('sys config err.');
        }
        switch ($app_platform) {
            case 'ios':
                $url = $conf[$app_platform][$app_ver];
                break;
            case 'android':
            default:
                $url = sprintf('http://www.vding.wang/app/%s/%s', $app_platform, $conf[$app_platform][$app_ver]) ;
                break;
        }
        Yii::$app->response->redirect($url);
    }
}