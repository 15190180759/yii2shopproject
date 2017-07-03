<table class="table table-hover table-striped">
    <thead>
    <tr>
        <th>ID</th>
        <th>栏目名</th>
        <th>路由</th>
        <th>父类id</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($menus as $menu):?>
        <tr>
            <td><?=$menu->id?></td>
            <td><?=$menu->label?></td>
            <td><?=$menu->url;?> </td>
            <td><?=$menu->parent_id;?> </td>
            <td><?=$menu->sort;?> </td>
            <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$menu->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$menu->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>
<?php

/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({

});');
