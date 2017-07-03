<!-- 页面头部 start -->
<div class="header w990 bc mt15">
    <div class="logo w990">
        <h2 class="fl"><a href="../index/index.php"><?=\yii\helpers\Html::img('@web/images/logo.png')?></a></h2>
    </div>
</div>
<!-- 页面头部 end -->

<!-- 登录主体部分start -->
<div class="login w990 bc mt10 regist">
    <div class="login_hd">
        <h2>用户注册</h2>
        <b></b>
    </div>
    <div class="login_bd">
        <div class="login_form fl">
            <?php
            $form = \yii\widgets\ActiveForm::begin(
                ['fieldConfig'=>[
                    'options'=>[
                        'tag'=>'li',
                    ],
                    'errorOptions'=>[
                        'tag'=>'p'
                    ]
                ]]
            );
            echo '<ul>';
            echo $form->field($model,'username'
            )->textInput(['class'=>'txt']);
            echo '<P>3-20位字符，可由中文、字母、数字和下划线组成</P>';
            echo $form->field($model,'password_hash'
            )->passwordInput(['class'=>'txt']);
            echo '<P>6-20位字符，可使用字母、数字和符号的组合，不建议使用纯数字、纯字母、纯符号</P>';
            echo $form->field($model,'repassword'
            )->passwordInput(['class'=>'txt']);
            echo '<P>请再次输入密码</P>';
            echo $form->field($model,'email'
            )->textInput(['class'=>'txt']);
            echo '<P>邮箱必须合法</P>';
            echo $form->field($model,'tel'
            )->textInput(['class'=>'txt','id'=>'tel']);
            $button =  \yii\helpers\Html::button('发送短信验证码',['id'=>'get_captcha','style'=>'height: 25px;padding:3px 8px']);
            echo $form->field($model,'smsCode',['template'=>"{label}\n{input}$button\n{hint}\n{error}"]
            )->textInput(['class'=>'txt','id'=>'captcha','placeholder'=>'请输入短信验证码']);
            echo $form->field($model,'code',['options'=>['class'=>'checkcode']])->
            widget(\yii\captcha\Captcha::className(),['template'=>'{input}{image}']);
            echo '<li>
                        <label for="">&nbsp;</label>
                        <input type="checkbox" class="chb" checked="checked" /> 我已阅读并同意《用户注册协议》
                    </li>';
            echo '<li>
                        <label for="">&nbsp;</label>
                        <input type="submit" value="" class="login_btn">
                    </li>';
            echo '</ul>';
            \yii\widgets\ActiveForm::end();
            ?>

        </div>

        <div class="mobile fl">
            <h3>手机快速注册</h3>
            <p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
            <p><strong>1069099988</strong></p>
        </div>

    </div>
</div>
<!-- 登录主体部分end -->
<?php
/**
 * @var yii\web\view;
 *
 */

$url = \yii\helpers\Url::to(['member/send']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        //发送验证码事件
$('#get_captcha').click(function(){
    //console.debug(this);
    var tel = $('#tel').val();
    //console.debug(tel);
    //发送post请求时将
     $.post('$url',{tel:tel},function(data){
            if(data == 'success'){
                console.debug('短信发送成功');
            }else{
                console.log(data);
            }
        });
});

JS

));




?>