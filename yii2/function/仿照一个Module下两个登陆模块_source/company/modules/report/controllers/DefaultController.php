<?php

namespace frontend\modules\report\controllers;

use yii\web\Controller;

/**
 * Default controller for the `report` module
 */
class DefaultController extends Controller {
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * ysk.dev/report/default/demo-module
     * @author cmk
     */
    public function actionDemoModule() {

        $module = \Yii::$app->controller->module;
       echo  $module->mycomponent->welcome();
       exit;
    }
}
