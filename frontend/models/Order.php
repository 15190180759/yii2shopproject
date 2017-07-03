<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $name
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $tel
 * @property integer $delivery_id
 * @property string $delivery_name
 * @property double $delivery_price
 * @property integer $payment_id
 * @property string $payment_name
 * @property string $total
 * @property integer $status
 * @property string $trade_no
 * @property integer $create_time
 */
class Order extends \yii\db\ActiveRecord
{
    //送货方式
    public static $delivery = [
        ['id'=>1,'name'=>'普通快递送货上门','price'=>10.00,'desc'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
        ['id'=>2,'name'=>'特快专递','price'=>50.00,'desc'=>'每张订单不满499.00元,运费40.00元, 订单4...'],
        ['id'=>3,'name'=>'加急快递送货上门','price'=>80.00,'desc'=>'每张订单不满499.00元,运费40.00元, 订单4...'],
        ['id'=>4,'name'=>'平邮','price'=>20.00,'desc'=>'每张订单不满499.00元,运费15.00元, 订单4...'],
    ];
    //支付方式
    public static $payment = [
        ['id'=>1,'name'=>'货到付款','desc'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        ['id'=>2,'name'=>'在线支付','desc'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        ['id'=>3,'name'=>'上门自提','desc'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        ['id'=>4,'name'=>'邮局汇款','desc'=>'通过快钱平台收款 汇款后1-3个工作日到账'],
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'delivery_id', 'payment_id', 'status', 'create_time'], 'integer'],
            [['delivery_price', 'total'], 'number'],
            [['name'], 'string', 'max' => 50],
            [['province', 'city', 'area'], 'string', 'max' => 20],
            [['address', 'delivery_name', 'payment_name', 'trade_no'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => '用户id',
            'name' => '收货人',
            'province' => '省',
            'city' => '市',
            'area' => '县',
            'address' => '详细地址',
            'tel' => '电话号码',
            'delivery_id' => '配送方式id',
            'delivery_name' => '配送方式名称',
            'delivery_price' => '配送方式价格',
            'payment_id' => '支付方式的id',
            'payment_name' => '支付方式的名称',
            'total' => '订单金额',
            'status' => '订单状态(0已取消1待付款2待发货3待收货4完成)',
            'trade_no' => '第三方支付交易号',
            'create_time' => '创建时间',
        ];
    }
    //订单和订单商品关系
    public function getGoods()
    {
        return $this->hasMany(OrderGoods::classname(),['order_id'=>'id']);
    }

}
