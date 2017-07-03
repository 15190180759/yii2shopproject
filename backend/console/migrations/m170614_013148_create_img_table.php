<?php

use yii\db\Migration;

/**
 * Handles the creation of table `img`.
 */
class m170614_013148_create_img_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('img', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer()->comment('商品id'),
            'photo'=>$this->string(100)->comment('照片')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('img');
    }
}
