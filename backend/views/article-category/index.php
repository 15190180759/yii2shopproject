<?= \yii\bootstrap\Html::a('添加文章分类',['article-category/add'], ['class' => 'btn btn-success']) ?>
    <table class="table">
        <tr>
            <th>文章分类ID</th>
            <th>文章分类名称</th>
            <th>分类简介</th>
            <th>文章分类排序</th>
            <th>状态</th>
            <th>帮助类的文章</th>
            <th>操作</th>
        </tr>
        <?php foreach ($art_cats as $art_cat):?>
            <tr>
                <td><?=$art_cat->id?></td>
                <td><?=$art_cat->name?></td>
                <td><?=$art_cat->intro?></td>
                <td><?=$art_cat->sort?></td>
                <td><?=\backend\models\ArticleCategory::$statusOptions[$art_cat->status]?></td>
                <td><?=\backend\models\ArticleCategory::$is_helpOptions[$art_cat->is_help]?></td>
                <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$art_cat->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$art_cat->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
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


