<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/morris/morris.css',
        'css/font-awesome.min.css',
        'css/ionicons.min.css',
        'css/AdminLTE.css',
        'css/fileinput.min.css',
    ];
    public $js = [
        'js/plugins/bootstrap.min.js',
        'js/plugins/jquery-ui.min.js',
        'js/plugins/raphael-min.js',
        'js/plugins/morris/morris.min.js',
        'js/plugins/sparkline/jquery.sparkline.min.js',
        'js/plugins/jqueryKnob/jquery.knob.js',
        'js/plugins/jquery.cookie.js',
        'js/plugins/jquery.fancytree-all.min.js',
        'js/plugins/jquery.dotdotdot.js',
        'js/plugins/fileinput.min.js',
        'js/AdminLTE/app.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
