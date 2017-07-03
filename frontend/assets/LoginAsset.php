<?php
/**
 * Created by PhpStorm.
 * User: wang
 * Date: 2017/6/19 0019
 * Time: 下午 14:34
 */
namespace frontend\assets;
use yii\web\AssetBundle;

class LoginAsset extends AssetBundle{
    public $basePath = '@webroot';//静态资源的硬盘路径
    public $baseUrl = '@web';//静态资源的url路径
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/address.css',
        'style/home.css',
        'style/bottomnav.css',
        'style/login.css',
        'style/footer.css',

    ];
    //需要加载的js文件
    public $js = [
    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}

