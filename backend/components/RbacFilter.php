<?php
namespace backend\components;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action)
    {
        //判断用户是否拥有权限
        if(!\Yii::$app->user->can($action->uniqueId)){
            //用户未登录就引导用户登录
            if(\Yii::$app->user->isGuest){
                return $action->controller->redirect(\Yii::$app->user->loginUrl);
            }
            //如果用户登录了没有权限就抛出异常
            throw new ForbiddenHttpException('sorry,你没有权限');
            return false;
        }
        return parent::beforeAction($action);
    }
}