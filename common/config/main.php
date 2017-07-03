<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=> 'zh-CN',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        //使用数据库的方式实现rbac
        'authManager'=>[
//            'class'=>yii\rbac\DbManager::className(),
            'class'=>'yii\rbac\DbManager',
        ]
    ],
];
