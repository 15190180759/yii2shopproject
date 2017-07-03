<?= \yii\bootstrap\Html::a('添加品牌',['brand/add'], ['class' => 'btn btn-success']) ?>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>品牌LOGO</th>
            <th>品牌名称</th>
            <th>简介</th>
            <th>排序</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php foreach ($brands as $brand):?>
            <tr>
                <td><?=$brand->id?></td>
                <td><?=$brand->logo?\yii\bootstrap\Html::img($brand->logo,['height'=>'50']):'';?></td>
                <td><?=$brand->name?></td>
                <td><?=$brand->intro?></td>
                <td><?=$brand->sort?></td>
                <td><?=\backend\models\Brand::$statusOptions[$brand->status]?></td>
                <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$brand->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$brand->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
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

