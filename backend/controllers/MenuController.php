<?php
namespace backend\controllers;
use backend\models\Menu;

class MenuController extends BackendController
{
    public function actionIndex()
    {

        $menus = Menu::find()->all();
        return $this->render('index',['menus'=>$menus]);
    }
    public function actionAdd(){
        $model = new Menu();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['menu/index']);
        }
        return $this->render('add',['model'=>$model]);
    }
    public function actionDelete($id){
        $model = Menu::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['menu/index']);
    }

}
