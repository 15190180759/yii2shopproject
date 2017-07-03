<?php
use yii\web\JsExpression;
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
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
      $('#goods-logo').val(data.fileUrl);
      //将上传成功后的图片地址(data.fileUrl)写入logo字段
      $('#img_logo').attr('src',data.fileUrl).show();
        
        
    }
}
EOF
        ),
    ]
]);

if($model->logo){//如果传过来的logo有值(修改的情况下);
    echo \yii\bootstrap\Html::img($model->logo,['height'=>'50']);
}else{
    //在添加的情况下
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','id'=>'img_logo','height'=>'50']);
}
echo $form->field($model,'goods_category_id')->hiddenInput();
echo '<ul id="treeDemo" class="ztree"></ul>';
echo $form->field($model,'brand_id')->dropDownList($brands,['prompt'=>'=请选择商品品牌=']);
echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
echo $form->field($model,'is_on_sale',['inline'=>true])->radioList([1=>'是',0=>'否']);
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',0=>'隐藏']);
echo $form->field($model,'sort');
echo $form->field($goods_intro,'content')->widget('kucha\ueditor\UEditor',[]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-success btn-xs']);
\yii\bootstrap\ActiveForm::end();

//使用ztree，加载2个静态资源
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$zNodes = \yii\helpers\Json::encode($categories);
$js = new \yii\web\JsExpression(
    <<<JS
var zTreeObj;
    // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
    var setting = {
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "parent_id",
                rootPId: 0
            }
        },
        callback: {
		    onClick: function(event, treeId, treeNode) {
                //console.log(treeNode.id);
                //将选中节点的id赋值给表单parent_id
               $('#goods-goods_category_id').val(treeNode.id);
            }
	    }
    };
    // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
    var zNodes = {$zNodes};
    
    zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
    zTreeObj.expandAll(true);//展开所有节点
   //获取当前节点的父节点（根据id查找）
    var node = zTreeObj.getNodeByParam("id", $("#goods-goods_category_id").val(), null);
    zTreeObj.selectNode(node);//选中当前节点的父节点
JS

);
$this->registerJs($js);
?>
