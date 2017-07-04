<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170608_072622_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->notNull()->comment('品牌'),
            'intro'=>$this->text()->comment('简介'),
            'logo'=>$this->string(255)->comment('品牌logo'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
