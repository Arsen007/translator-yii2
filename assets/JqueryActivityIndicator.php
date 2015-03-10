<?php

namespace app\assets;

use yii\web\AssetBundle;

class JqueryActivityIndicator extends AssetBundle
{
    public $sourcePath = '@app/web/js';
    public $css = [
    ];
    public $js = [
        'activity-indicator.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
