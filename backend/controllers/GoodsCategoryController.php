<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsCategoryController extends BackendController
{
    public function actionIndex(){
        //分页,每页显示2条
        //获取所有的分类
        $query = GoodsCategory::find();
        //总条数 每页显示多少条 当前在第几页
        $total = $query->count();
        $page = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>10
            ]
        );
        //限制每页输出的条数
        $goods_categorys = $query->orderBy('tree,lft')->offset($page->offset)->limit($page->limit)->all();
        //将查找的数据传送给视图
        return $this->render('index',['goods_categorys'=>$goods_categorys,'page'=>$page]);
    }
    public function actionAdd(){
        $model = new GoodsCategory();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //判断是否是添加一级分类（parent_id是否为0）
            if($model->parent_id){
                //添加非一级分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);//获取上一级分类
                $model->prependTo($parent);//添加到上一级分类下面
            }else{
                //添加一级分类
                $model->makeRoot();
            }

            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-category/index']);
        }

        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
    public function actionEdit($id)
    {
        $model = GoodsCategory::findOne(['id'=>$id]);
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //判断是否是添加一级分类（parent_id是否为0）
            if($model->parent_id){
                //添加非一级分类
                $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);//获取上一级分类
                $model->prependTo($parent);//添加到上一级分类下面
            }else{
                if($model->getAttribute('parent_id') == 0) {
                    $model->save();
                }else{
                    //添加一级分类
                    $model->makeRoot();
                }

            }
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['goods-category/index']);
        }
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());


        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }
    //逻辑删除
    public function actionDelete($id){
        //查找出需要删除的字段
        $model = GoodsCategory::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods-category/index']);

    }
    public function actionText(){
        //生成顶级分类
//        $jydq = new GoodsCategory(['name' => '家用电器','parent_id'=>0]);
//        $jydq->makeRoot();
//        var_dump($jydq);
        //生成二级分类
//        $parent = GoodsCategory::findOne(['id'=>7]);
//        $sj = new GoodsCategory(['name' => '手机','parent_id'=>$parent->id]);
//        $sj->prependTo($parent);
//        var_dump($sj);
        //二级分类
//        $shyp = new GoodsCategory(['name' => '生活用品','parent_id'=>0]);
//        $shyp->makeRoot();
//        var_dump($shyp);
    }
    public function actionZtree(){
//        $categories = GoodsCategory::find()->asArray()->all();
//        return $this->render('zTree',['categories'=>$categories]);
        /*
         * 渲染YII布局文件一定需要用render不渲染布局文件的时候用renderParise
         * 渲染视图的时候如果不想渲染布局可以用renderParise
         * yii\web\controller里面有一个属性 layout，可以通过更改layout=false来禁用布局
         * public $layout=false; //重写这个属性就可以了 这是就有效果//return $this->render('zTree');
         */
        return $this->renderPartial('zTree');

    }

}
