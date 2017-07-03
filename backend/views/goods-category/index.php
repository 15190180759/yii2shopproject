<?= \yii\bootstrap\Html::a('添加商品分类',['goods-category/add'], ['class' => 'btn btn-success']) ?>
    <table class="table table-responsive table-hover">
        <tr>
            <th>商品分类ID</th>
            <th>树id</th>
            <th>左值</th>
            <th>右值</th>
            <th>层级</th>
            <th>商品分类名称</th>
            <th>上级分类名称</th>
            <th>简介</th>
            <th>操作</th>
        </tr>
        <?php foreach ($goods_categorys as $goods_category):?>
            <tr data-lft="<?=$goods_category->lft?>" data-rgt="<?=$goods_category->rgt?>" data-tree="<?=$goods_category->tree?>">
                <td><?=$goods_category->id?></td>
                <td><?=$goods_category->tree?></td>
                <td><?=$goods_category->lft?></td>
                <td><?=$goods_category->rgt?></td>
                <td><?=$goods_category->depth?></td>
                <td><?=str_repeat(' - ',$goods_category->depth).$goods_category->name?>
                <span class="glyphicon glyphicon-circle-arrow-up expand" style="float:right"></span>
                </td>
                <td><?=$goods_category->parent_id ? \backend\models\GoodsCategory::findOne(['id'=>$goods_category->parent_id])->name:'顶级分类'?></td>
                <td><?=$goods_category->intro?></td>
                <td><?= \yii\bootstrap\Html::a('', ['edit','id'=>$goods_category->id], ['class' => 'glyphicon glyphicon-pencil']) ?> <?= \yii\bootstrap\Html::a('', ['delete','id'=>$goods_category->id], ['class' => 'glyphicon glyphicon-trash']) ?></td>
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
$js = <<<JS
    $('.expand').click(function(){
        var show = $(this).hasClass('glyphicon glyphicon-circle-arrow-up');//判断选定的类是是否存在
        //切换样式
        $(this).toggleClass('glyphicon glyphicon-circle-arrow-down');
        $(this).toggleClass('glyphicon glyphicon-circle-arrow-up');
        //找出当前分类同一棵树下的子孙分类   同一颗树左值大于当前分类左值并且右值小于当前分类右值
        var current_tr = $(this).closest("tr");//获取当前点击图标所在tr
        var current_lft = current_tr.attr("data-lft");//当前分类左值
        var current_rgt = current_tr.attr("data-rgt");//当前分类右值
        var current_tree = current_tr.attr("data-tree");//当前分类tree值
        $('table tr:not(:first)').each(function() {
          var lft = $(this).attr('data-lft');
          var rgt = $(this).attr('data-rgt');
          var tree = $(this).attr('data-tree');
          //一般情况下是字符串的优先级比较大，所以对比之前要先转换为整数型，在js中其实就是字符串优先级>数字>null
          if(parseInt(tree) == parseInt(current_tree) && parseInt(lft)>parseInt(current_lft) && parseInt(rgt)<parseInt(current_rgt)){        
              show?$(this).fadeToggle():$(this).fadeToggle();
          }
        });
    })
JS;
$this->registerJs($js);



