<style>
    div.required label:after {
        content: " * ";
        color: #ff0c18;
    }
</style>
<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($article,'name')->hint('请输入文章名称');
echo $form->field($article,'intro')->textarea()->hint('请输入文章简介');
echo $form->field($article,'article_category_id')->dropDownList(\backend\models\ArticleCategory::find()->select(['name','id'])->
indexBy('id')->column(),['prompt'=>'请选择文章分类']);
echo $form->field($article,'sort')->hint('请输入文章排序');
echo $form->field($article,'status',['inline'=>true])->radioList([1=>'正常',0=>'隐藏'])->hint('请选择状态');
echo $form->field($article_detail,'contend')->textarea()->hint('请输入文章内容');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success btn-xs']);
\yii\bootstrap\ActiveForm::end();