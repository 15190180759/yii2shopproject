<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170615_164001_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(50)->comment('账号'),
            'password'=>$this->string(100)->comment('密码'),
            'status'=>$this->integer(2)->defaultValue(1)->comment('状态'),
            'log_time'=>$this->string()->comment('登录时间'),
            'last_ip'=>$this->string()->comment('最后的登录ip'),
            'auth_key'=>$this->string()->comment('auth_key')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
