<table class="table table-responsive table-hover table-bordered">
    <thead>
    <tr>
        <td>权限名称</td>
        <td>描述</td>
        <td>操作</td>
    </tr>
    </thead>
    <tbody>
    <?php foreach($permissions as $permission):?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td>
            <?=\yii\bootstrap\Html::a('修改',['edit-permission','name'=>$permission->name],['class'=>'btn btn-warning btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['del-permission','name'=>$permission->name],['class'=>'btn btn-danger btn-xs'])?>
        </td>
    </tr>
    <?php endforeach?>
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