<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryMobileAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-mobile-bower';
    public $css = [
        'css/jquery.mobile-1.4.5.min.css',
    ];
    public $js = [
        'js/jquery.mobile-1.4.5.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
