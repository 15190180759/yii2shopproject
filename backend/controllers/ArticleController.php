<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Controller;

class ArticleController extends BackendController
{
    public function actionIndex(){
        //分页,每页显示2条
        //获取所有的分类
        $query = Article::find();
        //总条数 每页显示多少条 当前在第几页
        $total = $query->count();
        $page = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>2
            ]
        );
        //限制每页输出的条数
        $articles = $query->offset($page->offset)->limit($page->limit)->all();
        //将查找的数据传送给视图
        return  $this->render('index',['articles'=>$articles,'page'=>$page]);
    }
    public function actionAdd(){
        //实例化文章模型
        $article = new Article();
        $article_detail = new ArticleDetail();
        if($article->load(\Yii::$app->request->post())
            && $article_detail->load(\Yii::$app->request->post())
            && $article->validate()
            && $article_detail->validate()){
            $article->save();
            $article_detail->article_id = $article->id;
            $article_detail->save();
            \Yii::$app->session->setFlash('success','文章添加成功');
            return $this->redirect(['article/index']);
        }else{
            var_dump($article->getErrors());
            var_dump($article_detail->getErrors());
        }

        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail]);
    }
    public function actionEdit($id){
        $article = Article::findOne(['id'=>$id]);
        $article_detail = ArticleDetail::findOne(['article_id'=>$id]);
        if($article->load(\Yii::$app->request->post())
            && $article_detail->load(\Yii::$app->request->post())
            && $article->validate()
            && $article_detail->validate()
        ){
            //保存数据
            $article->create_time = time();
            $article->save();
            $article_detail->save();
            \Yii::$app->session->setFlash('success','文章修改成功');
            return $this->redirect(['article/index']);
        }else{
            //失败打印错误
            var_dump($article->getErrors());
            var_dump($article_detail->getErrors());
        }
        return $this->render('add',['article'=>$article,'article_detail'=>$article_detail]);
    }

    //逻辑删除
        public function actionDelete($id){
            //查找出需要删除的字段
            $article = Article::findOne(['id'=>$id]);
            $article->status = -1;
            $article->save();
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['article/index']);

    }

}
