<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 */
class Goods extends \yii\db\ActiveRecord
{
    //状态选项
    static public $statusOptions=[1=>'正常',0=>'隐藏',-1=>'删除'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','goods_category_id','brand_id','market_price','shop_price','stock','is_on_sale','status','sort'],'required'],
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'LOGO图片',
            'goods_category_id' => '商品分类id',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售（1在售，0下架）',
            'status' => '状态(1正常,0回收站)',
            'sort' => '排序',
            'create_time' => '添加时间',
        ];
    }
    public function beforeSave($insert)
    {
        if($insert){
            $this->create_time = time();
            $recored = GoodsDayCount::findOne(['day' => date('Ymd', time())]);
            if ($recored){
                $this->sn = date('Ymd', time()) . str_pad($recored->count + 1, 6, '0', STR_PAD_LEFT);
                $recored->count = $recored->count+1;
                $recored->save();
            }else{
                $gdc = new GoodsDayCount();
                $gdc->day = date('Ymd', time());
                $gdc->count = 1;
                $gdc->save();
                $this->sn = date('Ymd', time()) . str_pad(1, 6, '0', STR_PAD_LEFT);
            }
        }
        return parent::beforeSave($insert);
    }
    /*
    * 商品和相册关系 1对多
    */
    public function getGalleries()
    {
        return $this->hasMany(Img::className(),['goods_id'=>'id']);
    }
    public function getBrand(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }

}
