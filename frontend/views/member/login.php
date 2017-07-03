<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="../index/index.php"><?=\yii\helpers\Html::img('@web/images/logo.png')?></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10">
    <div class="login_hd">
        <h2>用户登录</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <?php
            $form = \yii\widgets\ActiveForm::begin();
            echo '<ul>';
            echo $form->field($model,'username',[
                'options'=>['tag'=>'li'],
                'errorOptions'=>['tag'=>'p']
            ])->textInput(['class'=>'txt']);
            echo $form->field($model,'password',[
                'options'=>['tag'=>'li'],
                'errorOptions'=>['tag'=>'p']
            ])->passwordInput(['class'=>'txt']);
            echo $form->field($model,'code',['options'=>['class'=>'checkcode']])->
            widget(\yii\captcha\Captcha::className(),['template'=>'{input}{image}']);
            echo '<li>
                <label for="">&nbsp;</label>
                <input type="checkbox" class="chb"  name="remmberMe"/> 保存登录信息
                </li>';
            echo  '<li>
                        <input type="submit" value="" class="login_btn" />
                    </li>';
            echo '</ul>';

            \yii\widgets\ActiveForm::end();
            ?>
            <div class="coagent mt15">
                <dl>
                    <dt>使用合作网站登录商城：</dt>
                    <dd class="qq"><a href=""><span></span>QQ</a></dd>
                    <dd class="weibo"><a href=""><span></span>新浪微博</a></dd>
                    <dd class="yi"><a href=""><span></span>网易</a></dd>
                    <dd class="renren"><a href=""><span></span>人人</a></dd>
                    <dd class="qihu"><a href=""><span></span>奇虎360</a></dd>
                    <dd class=""><a href=""><span></span>百度</a></dd>
                    <dd class="douban"><a href=""><span></span>豆瓣</a></dd>
                </dl>
            </div>
        </div>

        <div class="guide fl">
            <h3>还不是商城用户</h3>
            <p>现在免费注册成为商城用户，便能立刻享受便宜又放心的购物乐趣，心动不如行动，赶紧加入吧!</p>
            <?=\yii\helpers\Html::a('免费注册',['member/register'],['class'=>'reg_btn'])?>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->