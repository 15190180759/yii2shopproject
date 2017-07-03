<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo \yii\bootstrap\Html::submitButton('login',['class'=>'btn btn-info btn-sm']);
echo "&emsp;";
echo \yii\bootstrap\Html::resetButton('reset',['class'=>'btn btn-danger btn-sm']);
echo $form->field($model,'rememberMe')->checkbox();
\yii\bootstrap\ActiveForm::end();
