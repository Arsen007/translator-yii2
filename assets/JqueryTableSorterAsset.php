<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryTableSorterAsset extends AssetBundle
{
    public $sourcePath = '@bower/tablesorter';
    public $css = [
        'themes/blue/style.css',
    ];
    public $js = [
        'jquery.tablesorter.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
