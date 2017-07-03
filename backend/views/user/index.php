<table class="table table-hover table-striped">
    <tr>
        <th>用户ID</th>
        <th>用户名</th>
        <th>状态</th>
        <th>登录时间</th>
        <th>最后登录的ip</th>
        <th>操作</th>
    </tr>
    <?php foreach ($users as $user):?>
        <tr>
            <td><?=$user->id?></td>
            <td><?=$user->username?></td>
            <td><?=\backend\models\User::$statusOption[$user->status];?> </td>
            <td><?=$user->log_time?date('Y-m-d H:i:s',$user->log_time):'未登录'?></td>
            <td><?=$user->last_ip?$user->last_ip:'未登录'?></td>
            <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$user->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$user->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
        </tr>
    <?php endforeach;?>
</table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',

]);

