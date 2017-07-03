<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170621_062017_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'username'=>$this->string()->notNull()->comment('收货人'),
            'province'=>$this->string()->notNull()->comment('省'),
            'city'=>$this->string()->notNull()->comment('市'),
            'district'=>$this->string()->notNull()->comment('区'),
            'address'=>$this->string()->notNull()->comment('详细住址'),
            'tel'=>$this->string()->notNull()->comment('手机号码'),
            'status'=>$this->integer(1)->defaultValue(0)->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
