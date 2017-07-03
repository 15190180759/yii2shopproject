<?= \yii\bootstrap\Html::a('添加文章',['article/add'], ['class' => 'btn btn-success']) ?>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>文章名称</th>
            <th>文章简介</th>
            <th>文章分类</th>
            <th>文章内容</th>
            <th>排序</th>
            <th>状态</th>
            <th>发表时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($articles as $article):?>
            <tr>
                <td><?=$article->id?></td>
                <td><?=$article->name?></td>
                <td><?=$article->intro?></td>
                <td><?=$article->articleCategory?$article->articleCategory->name:'';?></td>
                <td><?=$article->articleDetail?$article->articleDetail->contend:'未知内容';?></td>
                <td><?=$article->sort?></td>
                <td><?=\backend\models\Article::$statusOptions[$article->status]?></td>
                <td><?=date('Y-m-d H:i:s',$article->create_time)?></td>
                <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$article->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$article->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
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


