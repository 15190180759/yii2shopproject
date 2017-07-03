<table class="table table-responsive table-hover table-bordered">
    <tr>
        <td width="8%">角色名称</td>
        <td width="8%">角色描述</td>
        <td width="70%">权限</td>
        <td>操作</td>
    </tr>
    <?php foreach($roles as $role):?>
        <tr>
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td><?php foreach(Yii::$app->authManager->getPermissionsByRole($role->name) as $permission){
                    echo $permission->description;
                    echo '&nbsp;';
                };?>
            </td>
            <td>
                <?=\yii\bootstrap\Html::a('修改',['edit-role','name'=>$role->name],['class'=>'btn btn-warning btn-xs'])?>
                <?=\yii\bootstrap\Html::a('删除',['del-role','name'=>$role->name],['class'=>'btn btn-danger btn-xs'])?>
            </td>
        </tr>
    <?php endforeach?>
</table>