<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $status
 * @property integer $log_time
 * @property integer $last_ip
 * @property integer $auth_key
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    //静态状态选项
    static public $statusOption=[1=>'正常',0=>'隐藏',-1=>'删除'];
    public $code;//保存验证码的对象
    //定义场景常量
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    //定义角色属性
    public $roles=[];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }
    //定义场景字段
    public function scenarios(){
        $scenarios =  parent::scenarios();//定义场景时一定要调用一下父类的场景,不然会被覆盖
        $scenarios[self::SCENARIO_ADD] = ['username', 'password','code','status','roles'];
        $scenarios[self::SCENARIO_EDIT] = ['username','code','status','roles'];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password','status','code','roles'],'required','on'=>self::SCENARIO_ADD],
            [['password'],'required','skipOnEmpty' => false,'on'=>self::SCENARIO_EDIT],
            [['username','status','code','roles'],'required','on'=>self::SCENARIO_EDIT],
            [['username'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 100 ,'on'=>self::SCENARIO_EDIT],
            ['code','captcha','captchaAction'=>'user/captcha'],
            ['username','unique','message'=>'此账号已被占用'],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '账号',
            'password' => '密码',
            'status' => '状态',
            'log_time' => '登录时间',
            'last_ip' => '最后的登录ip',
            'code'=>'验证码',
            'auth_key' => 'Auth Key',
            'roles'=>'角色'
        ];
    }
    //保存之前执行的代码
    public function beforeSave($insert)
    {
        //只在添加的时候设置
        if($insert){
            $this->password = Yii::$app->security->generatePasswordHash($this->password);

        }else{
            //更新 ,如果密码被修改，则重新加密。如果密码没有改，不需要操作
            $oldPassword = $this->getOldAttribute('password');//获取旧属性
            if($this->password != $oldPassword){
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
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
    /*
     * 在添加用户的时候会保存一个Auth_key，当我点击记住登录的时候就会
     *
     */
    //得到时数据库保存的Auth_key
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
    //验证Auth_key
    public function validateAuthKey($authKey)
    {
        return $this->auth_key == $authKey;
    }

//生成Auth_key的方法
    public function makeAuthKey()
    {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }
    //所有角色选项
    public static function getRolesOptions(){
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();
        return ArrayHelper::map($roles,'name','description');
    }
    public function loadData($id){
         $roles = \Yii::$app->authManager->getRolesByUser($id);
        foreach($roles as $role){
           $this->roles[]=$role->name;
         }
        }

    //分配角色
    public function assignRole(){
        $auth = Yii::$app->authManager;
            //得到所有的一个角色对象
        foreach($this->roles as $roleName){
            $role = $auth->getRole($roleName);
            //关联角色
            if($role)$auth->assign($role,$this->id);
        }

    }
    //修改关联角色的方法
    public function updateRole($id){
        $auth = Yii::$app->authManager;
        foreach($this->roles as $roleName){
            //去掉此用户的所有关联
            $auth->revokeAll($id);
            $role = $auth->getRole($roleName);
            //关联角色
            if($role)$auth->assign($role,$id);
        }
    }

}
