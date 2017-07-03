<?php
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model {
    public $username;
    public $password;
    public $rememberMe = true;
    public function rules(){
        return [
            //[字段名，验证方法]
            [['username','password'],'required'],
            ['rememberMe', 'boolean']
        ];
    }
    public function attributeLabels(){
        return [
            'username'=>'账号',
            'password'=>'密码',
            'rememberMe'=>'记住登录'

        ];
    }
    //自定义验证方法
    public function login(){
        $account = User::findOne(['username'=>$this->username]);
        if($account){
            //账号存在 验证密码
            if(!\Yii::$app->security->validatePassword($this->password,$account->password)){
                $this->addError('password','密码不正确');
            }else{
                //账号密码正确，登录
                $duration = $this->rememberMe?7*24*3600:0;//点击记住登陆,设置cookie的有效期
                \Yii::$app->user->login($account,$duration);
                return true;
            }
        }else{
            //账号不存在  添加错误
            $this->addError('username','账号不正确');

        }
        return false;
    }
    public function saveData(){
        $user = User::findOne(['username'=>$this->username]);
        if($user == null){
            $this->addError('username','该用户不存在');
        }else{
            $user->log_time = time();
            $user->last_ip = $_SERVER["SERVER_ADDR"];
            $user->save(false);
            return true;
        }
        return false;
    }

}