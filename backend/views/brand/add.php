<?php
use yii\web\JsExpression;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo $form->field($model,'logo')->hiddenInput();
echo \xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'width' => 120,
        'height' => 40,
        'onUploadError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadSuccess' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //把图片地址保存在隐藏域
      $('#brand-logo').val(data.fileUrl);
      //将上传成功后的图片地址(data.fileUrl)写入logo字段
      $('#img_logo').attr('src',data.fileUrl).show();
        
        
    }
}
EOF
        ),
    ]
]);
if($model->logo){//如果传过来的logo有值(修改的情况下);
    echo \yii\bootstrap\Html::img('@web'.$model->logo,['height'=>'50']);
}else{
    //在添加的情况下
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','id'=>'img_logo','height'=>'50']);
}

echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',0=>'隐藏']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success btn-xs']);
\yii\bootstrap\ActiveForm::end();
