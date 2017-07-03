<?php
//加载静态资源管理器，注册静态资源到当前布局文件
\frontend\assets\LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= \yii\helpers\Html::csrfMetaTags() ?>
    <title><?= \yii\helpers\Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- 顶部导航 start -->
<div class="topnav">
    <div class="topnav_bd w990 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <?php if(Yii::$app->user->isGuest):?>
                    <li> 您好，请<?=\yii\helpers\Html::a('登录',['user/login'])?></li>
                <?php else:?>
                    <li>您好,<?=Yii::$app->user->identity->username?>
                        <?=\yii\helpers\Html::a('注销',['user/logout'])?></li>
                <?php endif;?>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<!-- 顶部导航 end -->

<div style="clear:both;"></div>

<?=$content?>

<div style="clear:both;"></div>
<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
    <p class="links">
        <a href="">关于我们</a> |
        <a href="">联系我们</a> |
        <a href="">人才招聘</a> |
        <a href="">商家入驻</a> |
        <a href="">千寻网</a> |
        <a href="">奢侈品网</a> |
        <a href="">广告服务</a> |
        <a href="">移动终端</a> |
        <a href="">友情链接</a> |
        <a href="">销售联盟</a> |
        <a href="">京西论坛</a>
    </p>
    <p class="copyright">
        © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
    </p>
    <p class="auth">
        <a href=""><?=\yii\helpers\Html::img('@web/images/xin.png')?></a>
        <a href=""><?=\yii\helpers\Html::img('@web/images/kexin.jpg')?></a>
        <a href=""><?=\yii\helpers\Html::img('@web/images/police.jpg')?></a>
        <a href=""><?=\yii\helpers\Html::img('@web/images/beian.gif')?></a>
    </p>
</div>
<!-- 底部版权 end -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
