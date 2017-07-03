<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "img".
 *
 * @property integer $goods_id
 * @property string $photo
 */
class Img extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id','photo'],'required'],
            [['photo'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => 'Goods ID',
            'photo' => '照片',
        ];
    }
}
