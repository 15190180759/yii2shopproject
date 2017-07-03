<?php
namespace backend\models;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions=[];
    //定义规则
    public function rules()
    {
        return [
            [['name','description','permissions'],'required']
        ];
    }
    //定义属性
    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permissions'=>'权限'
        ];
    }
    //得到所有的权限
    public static function getPermissionOptions(){
        $permission = \Yii::$app->authManager->getPermissions();
        return ArrayHelper::map($permission,'name','description');
    }
    //处理添加角色的数据
    public function addRole(){
        $authManager = \Yii::$app->authManager;
        //判断添加的角色是否存在
        if($authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
        }else{
            //创建角色
            $role = $authManager->createRole($this->name);
            $role->description = $this->description;

            if($authManager->add($role)){//保存数据成功
                //角色和权限关联
                foreach($this->permissions as $permissionName){
                    //得到权限的对象
                    $permission = $authManager->getPermission($permissionName);
                    //关联角色
                    return $authManager->addChild($role,$permission);
                }
                return true;
            }
        }
        return false;
    }
    //加载数据
    public function loadData($role){
        $this->name = $role->name;
        $this->description = $role->description;
        foreach(\Yii::$app->authManager->getPermissionsByRole($role->name) as $permission){
            $this->permissions[] = $permission->name;
        }

    }
    public function upodateRole($name){
        $authManager = \Yii::$app->authManager;
        //得到角色对象
        $role = $authManager->getRole($name);
        //给权限赋值
        $role->name = $this->name;
        $role->description = $this->description;
        if($name != $this->name && $authManager->getRole($this->name)){
            $this->addError('name','角色已存在');
        }else{
            if($authManager->update($name,$role)){
                //去掉所有与该角色关联的权限
                $authManager->removeChildren($role);
                foreach($this->permissions as $permissionName){
                    //得到一个权限对象
                    $permission = $authManager->getPermission($permissionName);
                    $authManager->addChild($role,$permission);
                }
                return true;
            }
        }
        return false;
    }

}