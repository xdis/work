<?php
namespace company\modules\shop\controllers;

use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends BaseShopController {

    public function actionIndex() {
        return $this->render('index');
    }


}
