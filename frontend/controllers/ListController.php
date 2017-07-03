<?php
namespace frontend\controllers;
use backend\components\SphinxClient;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use frontend\models\Cart;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;

class ListController extends Controller{
    public $layout="index";
    public function actionList(){

        if($keyword = \Yii::$app->request->get('keyword')){
            $query = Goods::find();
            $cl = new SphinxClient();
            $cl->SetServer('127.0.0.1', 9312);
            $cl->SetConnectTimeout(10);
            $cl->SetArrayResult(true);
            $cl->SetMatchMode(SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $res = $cl->Query($keyword, 'goods');
            if (!isset($res['matches'])) {
//                throw new NotFoundHttpException('没有找到xxx商品');
                $query->where(['id' => 0]);
            } else {

                //获取商品id
                //var_dump($res);exit;
                $ids = ArrayHelper::map($res['matches'], 'id', 'id');
                $query->where(['in', 'id', $ids]);
            }
            $total = $query->count();
            //实例化分页工具条
            $page = new Pagination(
                [
                    'totalCount' => $total,
                    'defaultPageSize' => 2
                ]
            );

            $goodes = $query->offset($page->offset)->limit($page->limit)->orderBy(['id' => 3])->all();
            if($res['words'] == null){
                $res['words'] = [];
            }
            $keywords = array_keys($res['words']);
            $options = array(
                'before_match' => '<span style="color:red;">',
                'after_match' => '</span>',
                'chunk_separator' => '...',
                'limit' => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
            );
            //关键字高亮
            //        var_dump($models);exit;
            foreach ($goodes as $index => $item) {
                $name = $cl->BuildExcerpts([$item->name], 'goods', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
                $goodes[$index]->name = $name[0];
                //            var_dump($name);
            }
            $goodsCategories = GoodsCategory::findAll(['parent_id'=>0]);
            return $this->render('list',['goodes'=>$goodes,'goodsCategories'=>$goodsCategories]);
        }else{
            $goodsCategories = GoodsCategory::findAll(['parent_id'=>0]);
            $goodes = Goods::find()->all();
            return $this->render('list',['goodsCategories'=>$goodsCategories,'goodes'=>$goodes]);
        }

    }
    //显示商品详情页
    public function actionGoods($id){
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new HttpException('404','商品不存在');
        }
        return $this->render('goods',['goods'=>$goods]);
    }
    //加入购物车
    public function actionAdd(){
        //分两种情况 未登录操作session 登录操作cookie 加入购物车需得到goods_id和商品数量
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if($goods == null){
            throw new HttpException('404','商品你不存在');
        }
        if(\Yii::$app->user->isGuest){//未登录
            //判断cookie中是否有数据
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
            }

            //对Cookie进行增删改时调用的response , 对Cookie读取时使用的是Request
              $cookies = \Yii::$app->response->cookies;
            //保存cookie之前检查购物车是否有该商品,有就累加数量
            if(key_exists($goods_id,$cart)){
                $cart[$goods_id]+=$amount;
            }else{
                $cart[$goods_id] = $amount;
            }
              //$cart = [$goods_id=>$amount];
              $cookie = new Cookie(
                  ['name'=>'cart','value'=>serialize($cart)]
              );
            //保存cookie
            $cookies->add($cookie);
        }else{
            $member_id = \Yii::$app->user->getId();
            $cart = Cart::findOne(['goods_id'=>$goods_id]);
            if($cart){
                $cart->member_id = $member_id;
                $cart->amount += $amount;
                $cart->save();
            }else{
                //如果数据库不存在就增加一条数据
                $model = new Cart();
                $model->goods_id = $goods_id;
                $model->amount = $amount;
                $model->member_id = $member_id;
                $model->save();
            }

            }
        return $this->redirect(['list/cart']);
    }
    //购物车
    public function actionCart(){
        if(\Yii::$app->user->isGuest){
            //首先从cookie中查找
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                $cart  = [];
            }else{
                $cart = unserialize($cookie->value);
            }

            $models = [];//保存所有的商品
            foreach($cart as $goods_id=>$amount){
                $goods = Goods::findOne(['id'=>$goods_id])->attributes;//得到一个商品将它住那还为数组
                $goods['amount']=$amount;//将商品的数量加到对应的商品下
                $models[]=$goods;
            }

        }else{
            //从数据库查找
            $carts = Cart::find()->all();
            if($carts){
                $models = [];
                foreach($carts as $cart){
                    $goods = Goods::findOne(['id'=>$cart->goods_id])->attributes;
                    $goods['amount'] = $cart->amount;
                    $models[]=$goods;
                }

            }else{
                $models = [];
            }

        }

        //显示购物车页面
        return $this->render('cart',['models'=>$models]);
    }

    //修改购物车数据
    public function actionUpdateCart(){
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        $goods = Goods::findOne(['id'=>$goods_id]);
        if($goods_id == null){
            throw new HttpException('404','商品不存在');
        }
        if(\Yii::$app->user->isGuest){
            //得到cookie的数据
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                $cart = [];
            }else{
                $cart = unserialize($cookie->value);
            }
            //修改cookie
            $cookies = \Yii::$app->response->cookies;
            if($amount){
                $cart[$goods_id]=$amount;
            }else{
                if(key_exists($goods['id'],$cart)) unset($cart[$goods_id]);
            }
            $cookie = new Cookie([
                'name'=>'cart','value'=>serialize($cart)
            ]);
            $cookies->add($cookie);

        }else{
            $cart = Cart::findOne(['goods_id'=>$goods_id]);
            if($amount){
                $cart->amount = $amount;
                $cart->save();
            }else{
              $cart->delete();
            }
            return true;

        }
    }

}