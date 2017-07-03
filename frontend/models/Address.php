<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $username
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property string $tel
 * @property integer $status
 */
class Address extends \yii\db\ActiveRecord
{

    public $default = true;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'province', 'city', 'district', 'address', 'tel'], 'required'],
            [['status'], 'integer'],
            [['username', 'province', 'city', 'district', 'address', 'tel'], 'string', 'max' => 255],
            ['default', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '收货人',
            'province' => '省',
            'city' => '市',
            'district' => '区',
            'address' => '详细住址',
            'tel' => '手机号码',
            'status' => '状态',
            'default'=>'默认地址'
        ];
    }
    public function SaveData(){
        if($this->default){
            $models = self::find()->all();
            foreach($models as $model){
                $model->status = 0;
                $model->save();
            }
            $this->status = 1;
            $this->save();
        }
        return true;
    }
}
