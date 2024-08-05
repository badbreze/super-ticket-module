<?php
namespace super\ticket\assets;

class EnjoyAsset extends \yii\web\AssetBundle {
    public $sourcePath = __DIR__.'/enjoy';

    public $js = [
        //'base/vendor.bundle.base.js',
        //'js/template.js',
        'js/enjoy.js',
        'js/menu.js',
    ];

    public $css = [
        //'mdi/css/materialdesignicons.min.css',
        //'base/vendor.bundle.base.css',
        'css/theme.css',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
        'css/style.css',
        'css/menu.css',
        'css/fileicon.css',
        //'css/styles.css',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        //'kartik\icons\FontAwesomeAsset',
    ];
}