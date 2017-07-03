<?php
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo \xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['c-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['goods_id'=>$goods->id],//上传文件的同时传参相册表的goods_id等于商品表的id
        'width' => 120,
        'height' => 40,
        'onUploadError' => new \yii\web\JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadSuccess' => new \yii\web\JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
         var html='<tr data-id="'+data.goods_id+'" id="gallery_'+data.goods_id+'">';
        html += '<td><img src="'+data.fileUrl+'" height="100"/></td>';
        html += '<td><button type="button" class="btn btn-danger del_btn">删除</button></td>';
        html += '</tr>';
        $("table").append(html);
        
        
    }
}
EOF
        ),
    ]
]);
?>
<table class="table">
    <tr>
        <td>图片</td>
        <td>操作</td>
    </tr>
    <?php foreach($goods->galleries as $gallery):?>
        <tr id="gallery_<?=$gallery->id?>" data-id="<?=$gallery->id?>">
            <td><?=\yii\bootstrap\Html::img($gallery->photo,['width'=>'100'])?></td>
            <td><?=\yii\bootstrap\Html::button('删除',['class'=>'btn btn-danger del_btn'])?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
$url = \yii\helpers\Url::to(['del-gallery']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
    $('table').on('click','.del_btn',function(){
       if(confirm("确定删除该图片吗?")){
            var id = $(this).closest("tr").attr("data-id");
            var that = this; //定义一个全局变量方便函数内部使用,这里的this如果放在函数内部就不是同一个this了
             $.post("{$url}",{id:id},function(data){
                 //console.debug(data);
                if(data=="success"){
                    $(that).closest("tr").remove();
                    //从页面移除
                    //$("#gallery_"+id).remove();
                }
                
            });
        }
    })
JS

));
