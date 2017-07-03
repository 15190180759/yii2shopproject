<?php

namespace backend\controllers;


use backend\components\RbacFilter;
use backend\models\LoginForm;
use backend\models\User;
use frontend\models\PasswdForm;
use yii\bootstrap\Html;
use yii\data\Pagination;
use yii\web\Controller;

class UserController extends Controller
{
    public function actionIndex()
    {
        $query = User::find();
        $total = $query->count();
        $page = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>2
            ]
        );

        $users = $query->offset($page->offset)->limit($page->limit)->orderBy(['id'=>3])->all();
        return $this->render('index',['users'=>$users,'page'=>$page]);
    }
        //添加用户
        public function actionAdd()
        {
            //实例化用户模型
            $model = new User(['scenario'=>User::SCENARIO_ADD]);
            //实例化请求方式
            $request = \Yii::$app->request;
            if ($request->isPost) {
                //接收数据
                $model->load($request->post());
                //模型验证数据
                if ($model->validate()) {
                    $model->makeAuthKey();
                    $model->save(false);
                    \Yii::$app->session->setFlash('success','注册成功,请登录');
                    $model->assignRole();
                    //跳转页面
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }
            }
            //显示添加页面,分配数据到页面
            return $this->render('add',['model'=>$model]);
        }
    public function actionEdit($id)
    {
        //实例化用户模型
        $model = User::findOne(['id'=>$id]);
        $model->scenario =User::SCENARIO_EDIT;
        $model->loadData($id);
        //实例化请求方式
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->updateRole($model->id);
                $model->save(false);
                \Yii::$app->session->setFlash('success','修改成功用户信息成功');
                //跳转页面
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        //显示添加页面,分配数据到页面
        return $this->render('add',['model'=>$model]);
    }
    //删除用户
    public function actionDelete($id){
        //根据id查找数据
        $model = User::findOne(['id'=>$id]);
        //删除数据
        $model->status = -1;
        $model->save(false);
        \Yii::$app->session->setFlash('success','删除成功');
        //跳转页面
        return $this->redirect(['user/index']);

    }
    //登录
    public function actionLogin(){
        if (!\Yii::$app->user->isGuest) {
            //已经登录
            return $this->goHome();
        }
        //实例化登陆表单模型
        $model = new LoginForm();
        $request = \Yii::$app->request;
        if($model->load($request->post()) && $model->login()){
            if($model->saveData()){
                \Yii::$app->session->setFlash('success','登录成功');
            }
                return $this->goBack();
            }else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    //检测登录
    public function actionCheck(){
        //实例化user组件
        $user = \Yii::$app->user;
        if($user->isGuest == false){
            echo '登录成功';
            echo Html::a('注销用户',['user/logout']);
        }else{
            echo '你还未登录<br/>';
            echo Html::a('请点这里',['user/login']);
        };
    }
    //退出 注销
    public function actionLogout()
    {//注销
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        return $this->goHome();

    }

    //验证码验证
    public function actions(){
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['add','edit','delete','check','logout','index','edit'],
            ]
        ];
    }
    //简单的acf授权
//    //过滤器
//    public function behaviors()
//    {
//        return [
//            'acf'=>[
//                'class'=>AccessControl::className(),
//                'only'=>['add','edit','delete','index','login','check','logout'],//该过滤器作用的操作 ，默认是所有操作
//                'rules'=>[
//                    [//未认证用户允许执行的操作
//                        'allow'=>true,//是否允许执行
//                        'actions'=>['login','check','add'],//指定操作
//                        'roles'=>['?'],//角色？表示未认证用户  @表示已认证用户
//                    ],
//                    [//已认证用户允许执行的操作
//                        'allow'=>true,//是否允许执行
//                        'actions'=>['add','edit','delete','index','check','logout'],//指定操作
//                        'roles'=>['@'],//角色？表示未认证用户  @表示已认证用户
////                        'matchCallback'=>function(){
//////
////                            return false;
////                        },
//
//                    ],
//
//                    //其他都禁止执行
//
//                ]
//            ],
//
//        ];
//    }
    public function actionPasswd(){
        //实例化表单模型
        $model = new PasswdForm();
        //实例化request组件判断请求方式
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                // Account::findOne(['id'=>\Yii::$app->user->id]);
                //如果旧密码正确那就将新密码的值赋值给user组件里面的identify保存
                $account = \Yii::$app->user->identity;
                //将新密码的值
                $account->password = $model->newPassword;
                if($account->save(false)){
                    \Yii::$app->session->setFlash('success','密码修改成功');
                    return $this->redirect(['account/index']);
                }else{
                    var_dump($account->getErrors());exit;
                }
            }
        }
        return $this->render('passwd',['model'=>$model]);
    }

}
