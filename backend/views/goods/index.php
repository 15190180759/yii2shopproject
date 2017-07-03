<?php
//$form = \yii\bootstrap\ActiveForm::begin([
//    'method' => 'get',
//    //get方式提交,需要显式指定action
//    'action'=>\yii\helpers\Url::to(['goods/index']),
//    'options'=>['class'=>'form-inline']
//]);
//echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
//echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
//echo $form->field($model,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
//echo $form->field($model,'maxPrice')->textInput(['placeholder'=>'￥'])->label('-');
//echo \yii\bootstrap\Html::submitButton('搜索');
//\yii\bootstrap\ActiveForm::end();
?>

<div>
    <?=
    \yii\bootstrap\Html::a('添加商品',['goods/add'],['class'=>'btn btn-info'])
    ?>
</div>
<div style="float: right">
    <?php
    $form = \yii\bootstrap\ActiveForm::begin();
    echo isset($keyword)?\yii\bootstrap\Html::input('text','keyword',"$keyword"):\yii\bootstrap\Html::input('text','keyword','Search');
    echo \yii\bootstrap\Html::submitButton('Go!',['class'=>'img-circle search']);
    \yii\bootstrap\ActiveForm::end();
    //?>
</div>
    <table class="table">
        <tr>
            <th>商品ID</th>
            <th>品牌名称</th>
            <th>货号</th>
            <th>产品图</th>
            <th>商品分类</th>
            <th>商品品牌</th>
            <th>市场价格</th>
            <th>商品价格</th>
            <th>库存</th>
            <th>是否上架</th>
            <th>状态</th>
            <th>排序</th>
            <th>添加时间</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goodes as $goods):?>
            <tr>
                <td><?=$goods->id?></td>
                <td><?=$goods->name?></td>
                <td><?=$goods->sn?></td>
                <td><?=$goods->logo?\yii\bootstrap\Html::img($goods->logo,['height'=>'50','class'=>'img-rounded']):'';?></td>
                <td><?=\backend\models\GoodsCategory::findOne(['id'=>$goods->goods_category_id])?\backend\models\GoodsCategory::findOne(['id'=>$goods->goods_category_id])->name:"未知分类"?></td>
                <td><?=\backend\models\Brand::findOne(['id'=>$goods->brand_id])?\backend\models\Brand::findOne(['id'=>$goods->brand_id])->name:"未知品牌"?></td>
                <td><?=$goods->market_price?></td>
                <td><?=$goods->shop_price?></td>
                <td><?=$goods->stock?></td>
                <td><?=$goods->is_on_sale == 1?'是':'否';?></td>
                <td><?=\backend\models\Goods::$statusOptions[$goods->status];?></td>
                <td><?=$goods->sort;?></td>
                <td><?=date('Y-m-d H:i:s',$goods->create_time);?></td>
                <td><?=\backend\models\GoodsIntro::findOne(['goods_id'=>$goods->id])?\backend\models\GoodsIntro::findOne(['goods_id'=>$goods->id])->content:'未知内容';?></td>
                <td>
                    <?= \yii\bootstrap\Html::a('', ['edit','id'=>$goods->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$goods->id], ['class' => 'glyphicon glyphicon-trash']) ?> <?= \yii\bootstrap\Html::a('', ['photo','id'=>$goods->id], ['class' => 'glyphicon glyphicon-picture']) ?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
//分页工具条
echo \backend\components\GoLinkPager::widget([
    'pagination'=>$page,
    'go'=>true,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',

]);


