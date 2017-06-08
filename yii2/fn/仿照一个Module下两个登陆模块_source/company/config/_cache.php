<?php
/**
 * @author Eugene Terentev <eugene@terentev.net>
 */

$cache = [
    'class' => 'yii\caching\FileCache',
    'cachePath' => '@company/runtime/cache'
];

if (YII_ENV_DEV) {
    $cache = [
        'class' => 'yii\caching\DummyCache'
    ];
}

return $cache;
