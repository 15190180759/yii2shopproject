<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\bootstrap\Html;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use yii\web\HttpException;

class MemberController extends \yii\web\Controller
{//加载对应的布局文件
    public $layout = 'login';
//注册
    public function actionRegister(){
        $model = new Member(['scenario'=>Member::SCENARIO_ADD]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($user = $model->signup()){
                    //跳转页面
                    \Yii::$app->user->login($user);
                    return $this->goHome();
            }

        }
        return $this->render('register',['model'=>$model]);
    }
    public function actionLogin(){
        //登录一定要实现认证接口的类
        if(!\Yii::$app->user->isGuest){
            return $this->goHome();
        }
        $model = new LoginForm();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->login()){
                \Yii::$app->session->setFlash('success','登录成功');
                $model->saveData();
            }
            //登录完成后
            //判断cookie中是否有数据同步到数据表
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
            }
            $member_id = \Yii::$app->user->getId();
            //如果cookie中数据存在就存放在数据库
            foreach($cart as $goods_id=>$amount) {
                $model = Cart::findOne(['member_id' => $member_id]);
                if($model){
                    //如果数据库存在就修改数量并保存数据库
                    $model->amount += $amount;
                    $model->save();
                }else{
                    //如果数据库不存在就增加一条数据
                    $model = new Cart();
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                    $model->member_id = $member_id;
                    $model->save();
                }

            }
            \Yii::$app->response->cookies->remove($cookie);
            return $this->redirect(['list/cart']);
        }else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }

    }

    //退出 注销
    public function actionLogout()
    {//注销
        \Yii::$app->user->logout();
        \Yii::$app->session->setFlash('success','注销成功');
        return $this->goHome();

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
    //显示收获地址页面
    public function actionAddress(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        $model = new Address();
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
                if($model->saveData()){
                    \Yii::$app->session->setFlash('success','收货地址保存成功');
                    return $this->redirect(['member/address']);
                };
        }
        return $this->render('address',['model'=>$model]);
    }
    //三级联动
    public function actions()
    {
        $actions=parent::actions();
        $actions['get-region']=[
            'class'=>\chenkby\region\RegionAction::className(),
            'model'=>\frontend\models\Region::className()
        ];
        return $actions;
    }
    //删除地址
    public function actionAddressDel($id){
        $model = Address::findOne(['id'=>$id]);
        $model->delete();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['member/address']);
    }
//修改地址
    public function actionAddressEdit($id){
        $model = Address::findOne(['id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            if($model->saveData()){
                \Yii::$app->session->setFlash('success','收货地址修改成功');
                return $this->redirect(['member/address']);
            };
        }
        return $this->render('address',['model'=>$model]);
    }
    //默认选中
    public function actionAddressDefault($id){
        $address = Address::findOne(['id'=>$id]);
        if($address == null){
            throw new HttpException('404','地址不存在');
        }
        $models = Address::find()->all();
        foreach($models as $model){
            $model->status = 0;
            $model->save();
        }
        $address->status=1;
        $address->save();
        return $this->redirect(['member/address']);
    }

    //发送短信
    public function actionSend(){
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            \Yii::$app->session->setFlash('error','手机号码不正确');
            exit;
        }
        $code = rand('1000','9999');
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
            \Yii::$app->cache->set('tel'.$tel,$code,5*60);
            echo 'success'.$code;
        }else{
            \Yii::$app->session->setFlash('error','发送失败');
        }
    }

    //阿利大于测试短信发送
    public function actionSms(){

            // 配置信息
            $config = [
            'app_key'    => '24478763',
            'app_secret' => '7bc94e08d82b9c47689c1ffbea3bc1f8',
            // 'sandbox'    => true,  // 是否为沙箱环境，默认false
            ];


            // 使用方法一
            $client = new Client(new App($config));
            $req    = new AlibabaAliqinFcSmsNumSend;
            $code = rand(1000, 9999);
            $req->setRecNum('15190180759')//设置发给谁（手机号码）
            ->setSmsParam([
                'code' => $code//申请的模板中的$code
            ])
            ->setSmsFreeSignName('空白人生')//设置短信签名，必须是已审核的签名
            ->setSmsTemplateCode('SMS_71560150');

            $resp = $client->execute($req);//设置短信模板id，必须审核通过
            var_dump($resp);
            var_dump($code);


    }

    //邮箱测试
    public function actionMail()
    {
        //通过邮箱重设密码
        $result = \Yii::$app->mailer->compose()
            ->setFrom('15190180759@163.com')//谁的邮箱发出的邮件
            ->setTo('15190180759@163.com')//发给谁
            ->setSubject('邮箱测试')//邮件的主题
            //->setTextBody('Plain text content')//邮件的内容text格式
            ->setHtmlBody('<b style="color: red">创办于2011年的七牛</b>')//邮件的内容 html格式
            ->send();

       // Failed to authenticate on SMTP server with username "15190180759@163.com" using 2 possible authenticators
        //错误原因是因为没有开启 POP3/SMTP服务  IMAP/SMTP服务
        //SMTP服务器: smtp.163.com
        //最重要的是开启客户端授权密码

        var_dump($result);
    }

}
