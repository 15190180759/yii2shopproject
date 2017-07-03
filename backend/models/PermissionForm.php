<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{
    //定义字段属性
    public $name;
    public $description;
    //验证规则
    public function rules()
    {
        return [
            [['name','description'],'required']
        ];
    }

    public function attributeLabels()
    {
        return [
          'name'=>'权限名称',
          'description'=>'权限描述'
        ];
    }
    //处理添加权限的数据
    public function addPermission(){
        //使用数据库的方式实现rbac
        $authManager = \Yii::$app->authManager;
        //添加权限之前判断权限是否存在
        if($authManager->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }else{
            //创建权限
            $permission = $authManager->createPermission($this->name);
            $permission->description = $this->description;
            //保存权限
            return $authManager->add($permission);
        }
      return false;
    }
    //从数据库中查找数据并回显
    public function loadData($permission){
        $this->name = $permission->name;
        $this->description = $permission->description;
    }
    //修改权限
    public function updatePermission($name){
        $authManager = \Yii::$app->authManager;
        //得到需要修改的权限对象
        $permission = $authManager->getPermission($name);
        //证明权限名称已被修改,并且可以从数据库查找到
        if($name != $this->name && $authManager->getPermission($this->name)){
            $this->addError('name','该权限已存在');
        }else{
            //给权限赋值
            $permission->name = $this->name;
            $permission->description = $this->description;
            return $authManager->update($name,$permission);
        }
        return false;
    }

}