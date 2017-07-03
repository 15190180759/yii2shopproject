<?php
namespace frontend\models;
use yii\base\Model;

class LoginForm extends Model {
    public $username;
    public $password;
    public $rememberMe = true;
    public $code;
    public function rules(){
        return [
            //[字段名，验证方法]
            [['username','password','code'],'required'],
            ['rememberMe', 'boolean'],
            ['code','captcha','captchaAction'=>'site/captcha'],

            ['password','checkPassword'],
        ];
    }
    public function attributeLabels(){
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'rememberMe'=>'保存登录信息',
            'code'=>'验证码',

        ];
    }
    //自定义验证方法
    public function login(){
            $account = Member::findOne(['username'=>$this->username]);
            $duration = $this->rememberMe?7*24*3600:0;//点击记住登陆,设置cookie的有效期
            \Yii::$app->user->login($account,$duration);
            return true;
    }


    public function saveData(){
            $member = Member::findOne(['username'=>$this->username]);
            Member::updateAll(['last_login_time'=>time(),'last_login_ip'=>ip2long(\Yii::$app->request->userIP)],['id'=>$member->id]);
            return true;
    }

    public function checkPassword(){
            $user = Member::findOne(['username'=>$this->username]);
            if(!$user || !Member::validatePassword($this->password,$user->password_hash)){
                $this->addError('password','用户名或者密码不正确');
        }
    }

}