<?php

namespace company\assets;

use yii\web\AssetBundle;

class CompanyAuthAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/company_auth_treeview.css'
    ];

    public $js = [
        'js/Validform.min.js',
        'js/layer/layer.js',
        'js/company_auth_treeview.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}

?>