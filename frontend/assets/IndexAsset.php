<?php
namespace frontend\assets;
use yii\web\AssetBundle;

class IndexAsset extends AssetBundle{
    public $basePath = '@webroot';//静态资源的硬盘路径
    public $baseUrl = '@web';//静态资源的url路径
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/index.css',
        'style/bottomnav.css',
        'style/footer.css',
        'style/common.css',
        'style/goods.css',
        'style/cart.css',
        'style/success.css'

    ];
    //需要加载的js文件
    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/header.js',
        'js/index.js',
        'js/list.js',
        'js/goods.js',
        'js/jqzoom-core.js',
        'js/cart1.js',
        'js/cart2.js',
        'js/home.js',
        'js/layer/layer.js'
    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];


}