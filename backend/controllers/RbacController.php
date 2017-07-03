<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;


class RbacController extends BackendController
{
    //添加权限
    public function actionAddPermission(){
        $model = new PermissionForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //调用模型的方法来处理数据
            if($model->addPermission()){
                \Yii::$app->session->setFlash('success','添加权限成功');
                return $this->redirect(['index-permission']);
            }
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    //权限列表
    public function actionIndexPermission(){
        //得到所有的权限名称
        $permissions = \Yii::$app->authManager->getPermissions();
        //显示页面
        return $this->render('index-permission',['permissions'=>$permissions]);
    }
    //修改权限
    public function actionEditPermission($name){
        $permission = \Yii::$app->authManager->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('此权限不存在');
        }
        $model = new PermissionForm();
        //将需要修改的权限交给表单赋值
        $model->loadData($permission);
        //接收并验证提交后的数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
           if($model->updatePermission($name)){
               \Yii::$app->session->setFlash('success','修改权限成功');
               return $this->redirect(['index-permission']);
           }
        }
        return $this->render('add-permission',['model'=>$model]);
    }
    public function actionDelPermission($name){
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        if($permission == null){
            throw new NotFoundHttpException('此权限不存在');
        }
        $authManager->remove($permission);
        \Yii::$app->session->setFlash('success','移除权限成功');
        return $this->redirect(['index-permission']);
    }
    public function actionAddRole(){
        $model = new RoleForm();
        //接收数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->addRole()){
                \Yii::$app->session->setFlash('success','添加角色成功');
                return $this->redirect(['index-role']);
            };
        }
        return $this->render('add-role',['model'=>$model]);
    }
    //展示所有的角色
    public function actionIndexRole(){
        $roles = \Yii::$app->authManager->getRoles();
        return $this->render('index-role',['roles'=>$roles]);
    }
    //修改角色
    public function actionEditRole($name){
        $role = \Yii::$app->authManager->getRole($name);
        if($role == null){
            throw new NotFoundHttpException('角色不存在');
        }
        $model = new RoleForm();
        //调用模型回显数据
        $model->loadData($role);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->upodateRole($name)){
                \Yii::$app->session->setFlash('success','修改角色成功');
                return $this->redirect(['index-role']);
            }
        }
        return $this->render('add-role',['model'=>$model]);
    }
    public function actionDelRole($name){
        //得到一个角色对象
        $role = \Yii::$app->authManager->getRole($name);
        if($role == null){
            throw new NotFoundHttpException('角色不存在');
        }
        \Yii::$app->authManager->remove($role);
        \Yii::$app->session->setFlash('success','删除角色成功');
        return $this->redirect(['index-role']);
    }


}
