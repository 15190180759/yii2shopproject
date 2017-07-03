<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;

class OrderController extends \yii\web\Controller
{   public $layout = 'index';
    public function actionIndex()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['user/login']);
        }
        $id = \Yii::$app->user->getId();
        if(\Yii::$app->request->isPost){
            $model = new Order();
            //接收post提交的数据
            $post = \Yii::$app->request->post();
//            var_dump($post['total']);exit;
            //收货地址
            $address_id = $post['address_id'];
            $address = Address::findOne(['id'=>$address_id]);
            $model->name = $address->username;
            $model->province = $address->province;
            $model->city = $address->city;
            $model->area = $address->district;
            $model->address = $address->address;
            $model->tel = $address->tel;

            //邮递方式
            $delivery_id = $post['delivery_id'];
            foreach(Order::$delivery as $v){
                if($v['id'] == $delivery_id){
                    $model->delivery_id = $v['id'];
                    $model->delivery_name = $v['name'];
                    $model->delivery_price = $v['price'];
                }
            }
            //支付方式
            $payment_id = $post['payment_id'];
            foreach(Order::$payment as $i){
                if($i['id'] == $payment_id){
                    $model->payment_id = $i['id'];
                    $model->payment_name = $i['name'];
                }
            }
            $model->create_time = time();
            $model->member_id = $id;
            $model->total = $post['total'];

            //开启事物
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $model->save();
                //订单详情
                //从购物车获取数据当前会员的商品信息
                $carts = Cart::findAll(['member_id'=>\Yii::$app->user->id]);
                foreach($carts as $cart){
                    $goods = Goods::findOne(['id'=>$cart->goods_id,'status'=>1]);
                    if($goods==null){
                        //商品不存在
                        throw new Exception($goods->name.'商品已售完');
                    }
                    if($goods->stock < $cart->amount){
                        //库存不足
                        throw new Exception($goods->name.'商品库存不足');
                    }
                    //订单模型赋值
                    $order_goods = new OrderGoods();
                    //订单表的id
                    $order_goods->order_id = $model->id;
                    $order_goods->total = $model->total;
                        //购物车得到的数据
                    $order_goods->goods_id = $cart->goods_id;
                    $order_goods->amount = $cart->amount;
                    //商品表的数据
                    $order_goods->goods_name = $goods->name;
                    $order_goods->logo = $goods->logo;
                    $order_goods->price = $goods->shop_price;
                    $order_goods->save();
                    //扣除库存
                    $goods->stock -= $cart->amount;
                    $goods->save();

                }
                //提交事物
                $transaction->commit();
            }catch(Exception $exception){
                //回滚事物
                $transaction->rollBack();
            }

            //清除购物车
            if(Cart::deleteAll(['member_id'=>$id])){
                return $this->render('flow3');
            }
        }

    }
   //添加订单
    public function actionAdd()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        //所有的收货地址
        $addresses = Address::find()->all();
        //购物车的所有订单
        $carts = Cart::find()->all();
        return $this->render('add',['addresses'=>$addresses,'carts'=>$carts]);
    }
    //订单详情页
    public function actionOrderGoods(){
        $order_goods = OrderGoods::find()->all();
        return $this->render('order',['order_goods'=>$order_goods]);
    }

//删除订单
    public function actionDel($id){
        $model = OrderGoods::findOne(['id'=>$id]);
        $model->delete();
        return $this->redirect(['order/order-goods']);
    }
}
