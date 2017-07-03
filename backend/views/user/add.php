<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
if(!$model->username){
    echo $form->field($model,'password')->passwordInput();
}
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',2=>'隐藏']);
echo $form->field($model,'roles',['inline'=>true])->checkboxList(\backend\models\User::getRolesOptions(),['prompt'=>'请选择角色']);
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),
    [ 'captchaAction'=>'user/captcha','template'=>'<div class="row">
<div class="col-lg-2">{input}</div><div class="col-lg-1">{image}</div></div>']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();