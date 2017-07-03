<?php
namespace frontend\models;
use yii\base\Model;

class PasswdForm extends Model{
    //定义表单字段
    public $oldPassword;
    public $newPassword;
    public $rePassword;
    //验证规则
    public function rules()
    {
        return [
            [['oldPassword','newPassword','rePassword'],'required'],
            //旧密码要正确
            ['oldPassword','validatePassword'],
            //新密码和确认新密码要一致
            ['rePassword','compare','compareAttribute'=>'newPassword','message'=>'两次密码必须一致'],
        ];
    }
    //字段属性
    public function attributeLabels()
    {
        return [
            'oldPassword'=>'旧密码',
            'newPassword'=>'新密码',
            'rePassword'=>'确认密码',
        ];
    }
    //自定义验证旧密码的方法
    public function validatePassword(){
        //从user组件查找出来的密码
        $passwordHash = \Yii::$app->user->identity->password;
        //输入的旧密码
        $password = $this->oldPassword;
        //做出判断
        if(!\Yii::$app->security->validatePassword($password,$passwordHash)){
            $this->addError('oldPassword','旧密码不正确');
        };
    }
}