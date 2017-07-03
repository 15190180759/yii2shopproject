<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleCategoryController extends BackendController
{
    public function actionIndex(){
        //分页,每页显示2条
        //获取所有的分类
        $query = ArticleCategory::find();
        //总条数 每页显示多少条 当前在第几页
        $total = $query->count();
        $page = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>2
            ]
        );
        //限制每页输出的条数
        $art_cats = $query->offset($page->offset)->limit($page->limit)->all();
        //将查找的数据传送给视图
        return  $this->render('index',['art_cats'=>$art_cats,'page'=>$page]);

    }
    public function actionAdd(){
        //实例化品牌模型
        $model = new ArticleCategory();
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //保存数据
                $model->save();
                \Yii::$app->session->setFlash('success','文章分类添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                //失败打印错误
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //保存数据
                $model->save();
                \Yii::$app->session->setFlash('success','文章修改成功');
                return $this->redirect(['article-category/index']);
            }else{
                //失败打印错误
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }

    //逻辑删除
    public function actionDelete($id){
        //查找出需要删除的字段
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article-category/index']);

    }

}
