<?php

namespace frontend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $repassword;
    public $code;
    public $smsCode;//短信验证码
    //场景
    const SCENARIO_ADD = 'add';
    const SCENARIO_API_ADD = 'api_add';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'email'],
            [['username', 'email'],'required'],
            ['password_hash','required','on'=>self::SCENARIO_ADD],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'email'], 'string', 'max' => 100],
            [['tel'], 'string', 'max' => 11],
            ['code','captcha','captchaAction'=>'site/captcha','on'=>self::SCENARIO_ADD],
            ['code','captcha','captchaAction'=>'api/captcha','on'=>self::SCENARIO_API_ADD],
            ['repassword','compare','compareAttribute'=>'password_hash','message'=>'两次密码输入不一致'],
            ['smsCode','validateSms','on'=>self::SCENARIO_ADD],
            ['smsCode','validateSms','on'=>self::SCENARIO_API_ADD]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'email' => '邮箱',
            'tel' => '电话',
            'last_login_time' => '最后的登录时间',
            'last_login_ip' => '最后的登录ip',
            'status' => '状态(1正常,0删除)',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'repassword' =>'确认密码',
            'code'=>'验证码',
            ////'smsCode'=>'短信验证码'
        ];
    }

    //短信验证码验证
    public function validateSms(){
        $value = Yii::$app->cache->get('tel'.$this->tel);
        if(!$value || $this->smsCode!= $value){
            $this->addError('smsCode','短信验证码错误');
        }

    }
    //保存之前执行的代码
    public function beforeSave($insert)
    {
        //只在添加的时候设置
        if($insert){

            $this->created_at = time();
            //$this->password_hash = Yii::$app->security->generatePasswordHash($this->password_hash);
        }else{
            //更新 ,如果密码被修改，则重新加密。如果密码没有改，不需要操作
            $oldPassword = $this->getOldAttribute('password');//获取旧属性
            if($this->password_hash != $oldPassword){
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password_hash);
            }
        }


        return parent::beforeSave($insert);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        //通过id获取账号
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[user::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[user::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

//生成Auth_key的方法
    public function makeAuthKey()
    {
        return Yii::$app->security->generateRandomString();
    }

    public function signup()
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($this->password_hash);
        $this->auth_key = $this->makeAuthKey();
        if($this->save(false)){
            return self::getByUser($this->username);
        }

        return null;

    }

    public static function getByUser($username)
    {
        return self::findOne(['username'=>$username]);
    }

    public static function validatePassword($password,$passwordHush)
    {
        return \Yii::$app->security->validatePassword($password,$passwordHush);
    }
}
