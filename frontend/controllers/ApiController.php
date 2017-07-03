<?php
namespace frontend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller{
    public $enableCsrfValidation = false;//关闭跨域访问
    public function init()
    {//构造函数执行完执行的方法
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init();
    }
    //注册
    public function actionRegister(){
        $request = \Yii::$app->request;
        if($request->isPost){
            $model = new Member();
            $model->scenario = Member::SCENARIO_API_ADD;
            $model->username = $request->post('username');
            $model->password_hash = \Yii::$app->security->generatePasswordHash($request->post('password_hash'));
            $model->email = $request->post('email');
            $model->auth_key = $model->makeAuthKey();
            $model->tel = $request->post('tel');
            $model->code = $request->post('code');
            $model->smsCode = $request->post('smsCode');
            if($model->validate()){
                $model->save(false);
                return ['status'=>'1','msg'=>'注册成功','data'=>$model];
            }
            return ['status'=>'-1','msg'=>'注册失败'];
        }
        return ['status'=>'-1','msg'=>'不是post提交'];
    }
    //获取当前登录用户信息
    public function actionGetCurrentUser()
    {
        if(\Yii::$app->user->isGuest){
            return ['status'=>'-1','msg'=>'请先登录'];
        }
        return ['status'=>'1','msg'=>'','data'=>\Yii::$app->user->identity->toArray()];
    }
    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return ['status'=>'1','msg'=>'注销成功'];
    }
    //登录
    public function actionLogin(){
        $request = \Yii::$app->request;
        if($request->isPost){
            $user = Member::findOne(['username'=>$request->post('username')]);
            if($user && \Yii::$app->security->validatePassword($request->post('password'),$user->password_hash)){
                \Yii::$app->user->login($user);
                //登录完成后
                //判断cookie中是否有数据同步到数据表
                $cookies = \Yii::$app->request->cookies;
                $cookie = $cookies->get('cart');
                if($cookie == null){
                    $cart = [];
                }else{
                    $cart = unserialize($cookie->value);
                }
                $member_id = \Yii::$app->user->getId();
                //如果cookie中数据存在就存放在数据库
                foreach($cart as $goods_id=>$amount) {
                    $model = Cart::findOne(['member_id' => $member_id]);
                    if($model){
                        //如果数据库存在就修改数量并保存数据库
                        $model->amount += $amount;
                        $model->save();
                    }else{
                        //如果数据库不存在就增加一条数据
                        $model = new Cart();
                        $model->goods_id = $goods_id;
                        $model->amount = $amount;
                        $model->member_id = $member_id;
                        $model->save();
                    }

                }
                \Yii::$app->response->cookies->remove($cookie);
                return ['status'=>'1','msg'=>'登录成功','data'=>$user];
            }
            return ['status'=>'-1','msg'=>'账号或密码错误'];
        }
        return ['status'=>'-1','msg'=>'请使用post请求'];
    }
    //添加收货地址的接口
    public function actionAddAddress(){
        $request = \Yii::$app->request;
        if($request->isPost){
            $model = new Address();
            $model->username = $request->post('username');
            $model->province = $request->post('province');
            $model->city = $request->post('city');
            $model->district = $request->post('district');
            $model->address = $request->post('address');
            $model->tel = $request->post('tel');
            if($model->validate()){
                $model->save();
                return ['status'=>'1','msg'=>'保存数据成功','data'=>$model];
            }
            return ['status'=>'-1','msg'=>'保存数据失败'];
        }
        return ['status'=>'-1','msg'=>'不是post请求'];
    }
    //修改地址
    public function actionEditAddress(){
        $request = \Yii::$app->request;
        if($request->isPost){
            //接收数据
            $id = $request->post('id');
            $model = Address::findOne(['id'=>$id]);
            if($model){
                $model->id = $id;
                $model->username = $request->post('username');
                $model->province = $request->post('province');
                $model->city = $request->post('city');
                $model->district = $request->post('district');
                $model->address = $request->post('address');
                $model->tel = $request->post('tel');
                if($model->validate()){
                    $model->save();
                    return ['status'=>'1','msg'=>'修改数据成功','data'=>$model];
                }
                return ['status'=>'-1','msg'=>'修改数据失败'];
            }
        }
        return ['status'=>'-1','msg'=>'不是post请求'];
    }

//删除地址
    public function actionDelAddress(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $id = $request->get('id');
            $model = Address::findOne(['id'=>$id]);
            if($model){
                $model->delete();
                return ['status'=>'1','msg'=>'删除数据成功'];
            }
            return ['status'=>'-1','msg'=>'修改数据失败'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
        }
//收货地址展示
    public function actionIndexAddress(){
        $models = Address::find()->all();
        if($models){
            return ['status'=>'1','msg'=>'数据展示成功','data'=>$models];
        }
        return ['status'=>'-1','msg'=>'数据不存在'];
    }

    //获取商品分类
    public function actionGetGoodsCategory(){
        $models = GoodsCategory::find()->all();
        if($models){
            return ['status'=>'1','msg'=>'数据展示成功','data'=>$models];
        }
        return ['status'=>'-1','msg'=>'数据不存在'];
    }
    //商品分类的子分类
    public function actionGoodsCategoryChild(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $id = $request->get('id');
            $models = GoodsCategory::findAll(['parent_id'=>$id]);
            if($models){
                return ['status'=>'1','msg'=>'数据存在','data'=>$models];
            }
            return ['status'=>'-1','msg'=>'数据不存在'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
    }
    //获取某商品分类的父分类
    public function actionParentByChild(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $parent_id = $request->get('parent_id');
            $model = GoodsCategory::findOne(['id'=>$parent_id]);
            if($model){
                return ['status'=>'1','msg'=>'数据存在','data'=>$model];
            }
            return ['status'=>'-1','msg'=>'数据不存在'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
    }

    //获取某分类下的所有的商品
    public function actionGoodsCategoryGoods(){
        $request = \Yii::$app->request;
        if($request->isGet){
           $goods_category_id = $request->get('goods_category_id');
           $category = GoodsCategory::findOne(['id'=>$goods_category_id]);
           if($category == null){
               return ['status'=>'-1','msg'=>'该商品分类不存在'];
           }
            switch($category->depth){
                case 2://三级分类
                    $goods = Goods::find()->andWhere(['goods_category_id'=>$goods_category_id])->all();
                    break;
                case 1://二级分类
                    $ids = ArrayHelper::map($category->children,'id','id');
                    $goods = Goods::find()->andWhere(['in','goods_category_id',$ids])->all();
                    break;
                case 0://顶级分类
                    $ids = ArrayHelper::map($category->leaves()->asArray()->all(),'id','id');
                //var_dump($ids);
                    $goods = Goods::find()->andWhere(['in','goods_category_id',$ids])->all();
                    break;
            }
            return ['status'=>1,'msg'=>'','data'=>$goods];

        }else{
            return ['status'=>-1,'msg'=>'不是get方式提交'];
        }

    }
    //获取某品牌下面的所有的商品
    public function actionGoodsByBrand(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $brand_id = $request->get('brand_id');
            $models = Goods::findAll(['brand_id'=>$brand_id]);
            if($models){
                return ['status'=>'1','msg'=>'数据存在','data'=>$models];
            }
            return ['status'=>'-1','msg'=>'数据不存在'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
    }
    //获取文章分类
    public function actionGetArticleCategory(){
        $models = ArticleCategory::find()->all();
        if($models){
            return ['status'=>'1','msg'=>'数据展示成功','data'=>$models];
        }
        return ['status'=>'-1','msg'=>'数据不存在'];
    }
    //获取文章分类下的所有文章
    public function actionArticleCategoryArticle(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $id = $request->get('article_category_id');
            $models = Article::findOne(['article_category_id'=>$id]);
            if($models){
                return ['status'=>'1','msg'=>'数据存在','data'=>$models];
            }
            return ['status'=>'-1','msg'=>'数据不存在'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
    }
    //获取某文章所属分类
    public function actionArticleCategoryByArticle(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $article_category_id = $request->get('article_category_id');
            $model = ArticleCategory::findAll(['id'=>$article_category_id]);
            if($model){
                return ['status'=>'1','msg'=>'数据存在','data'=>$model];
            }
            return ['status'=>'-1','msg'=>'数据不存在'];
        }
        return ['status'=>'-1','msg'=>'不是get请求'];
    }
    //添加商品到购物车
    public function actionAddCart(){
        if(\Yii::$app->request->isPost){
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
                return ['status'=>'1','msg'=>'cookie数据保存成功','data'=>$cookies->add($cookie)];
            }else{
                $member_id = \Yii::$app->user->getId();
                $cart = Cart::findOne(['goods_id'=>$goods_id]);
                if($cart){
                    $cart->member_id = $member_id;
                    $cart->amount += $amount;
                    $cart->save();
                    return ['status'=>'1','msg'=>'累加数据保存成功','data'=>$cart];
                }else{
                    //如果数据库不存在就增加一条数据
                    $model = new Cart();
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                    $model->member_id = $member_id;
                    $model->save();
                    return ['status'=>'1','msg'=>'新增数据保存成功','data'=>$model];
                }

            }
        }
       return ['status'=>'-1','msg'=>'不是post请求'];
    }
  //修改购物车某商品的数量
    public function actionUpdateCart(){
     if(\Yii::$app->request->isPost){
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
                 return ['status'=>'1','msg'=>'删除cookie中的数据成功'];
             }
             $cookie = new Cookie([
                 'name'=>'cart','value'=>serialize($cart)
             ]);
             $cookies->add($cookie);
             return ['status'=>'1','msg'=>'修改cookie中的数据成功','data'=>$cookies->add($cookie)];
         }else{
             $cart = Cart::findOne(['goods_id'=>$goods_id]);
             if($amount){
                 $cart->amount = $amount;
                 $cart->save();
                 return ['status'=>'1','msg'=>'修改数据库中的数据成功','data'=>$cart];
             }else{
                 $cart->delete();
                 return ['status'=>'1','msg'=>'删除数据库中的数据成功'];
             }

         }
     }
        return ['status'=>'-1','msg'=>'不是post请求'];
    }
    //清空购物车
    public function actionClearCart(){
        if(\Yii::$app->user->isGuest){
            //未登录就是清初
            $cookie = \Yii::$app->request->cookies->get('cart');
            \Yii::$app->response->cookies->remove($cookie);
            return ['status'=>'1','msg'=>'清空购物车的数据成功(未登录)'];
        }else{
            Cart::deleteAll();
            return ['status'=>'1','msg'=>'清空购物车的数据成功(登录)'];
        }
    }
    //获取购物车的所有商品
    public function actionGetCartAll(){
        if(\Yii::$app->user->isGuest){
            $cookies = \Yii::$app->request->cookies;
            $cookie = $cookies->get('cart');
            if($cookie == null){
                $cart = [];
                return ['status'=>'1','msg'=>'cookie中没有数据(未登录)','data'=>$cart];
            }else{
                $cart = unserialize($cookie->value);
            }
            return ['status'=>'1','msg'=>'cookie中得到数据成功(未登录)','data'=>$cart];
        }else{
            $cart = Cart::find()->all();
            if($cart){
                return ['status'=>'1','msg'=>'数据库中得到数据成功(登录)','data'=>$cart];
            }
            return ['status'=>'-1','msg'=>'数据库中没有数据(登录)','data'=>$cart];
        }
    }
    //订单
    //获取支付方式
    public function actionGetPayment(){
            $model = Order::$payment;
            if($model){
                return ['status'=>1,'msg'=>'所有的支付方式','data'=>$model];
            }
        return ['status'=>'-1','msg'=>'不存在的支付方式'];
    }
    //获取收获方式
    public function actionGetDelivery(){
        $model = Order::$delivery;
        if($model){
            return ['status'=>1,'msg'=>'所有的支付方式','data'=>$model];
        }
        return ['status'=>'-1','msg'=>'不存在的支付方式'];
    }
    //提交订单
    public function actionCommitOrder()
    {
        if (\Yii::$app->user->isGuest) {
            return ['status' => '-1', 'msg' => '未登录是不能提交订单的'];
        }
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $id = \Yii::$app->user->getId();
            $model = new Order();
            //接收post提交的数据
            $post = \Yii::$app->request->post();
//            var_dump($post['total']);exit;
            //收货地址
            $address_id = $post['address_id'];
            $address = Address::findOne(['id' => $address_id]);
            $model->name = $address->username;
            $model->province = $address->province;
            $model->city = $address->city;
            $model->area = $address->district;
            $model->address = $address->address;
            $model->tel = $address->tel;

            //邮递方式
            $delivery_id = $post['delivery_id'];
            foreach (Order::$delivery as $v) {
                if ($v['id'] == $delivery_id) {
                    $model->delivery_id = $v['id'];
                    $model->delivery_name = $v['name'];
                    $model->delivery_price = $v['price'];
                }
            }
            //支付方式
            $payment_id = $post['payment_id'];
            foreach (Order::$payment as $i) {
                if ($i['id'] == $payment_id) {
                    $model->payment_id = $i['id'];
                    $model->payment_name = $i['name'];
                }
            }
            $model->create_time = time();
            $model->member_id = $id;
            $model->total = $post['total'];

            //开启事物
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $model->save();
                //订单详情
                //从购物车获取数据当前会员的商品信息
                $carts = Cart::findAll(['member_id' => \Yii::$app->user->id]);
                foreach ($carts as $cart) {
                    $goods = Goods::findOne(['id' => $cart->goods_id, 'status' => 1]);
                    if ($goods == null) {
                        //商品不存在
                        throw new Exception($goods->name . '商品已售完');
                    }
                    if ($goods->stock < $cart->amount) {
                        //库存不足
                        throw new Exception($goods->name . '商品库存不足');
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

            } catch (Exception $exception) {
                //回滚事物
                $transaction->rollBack();
            }

            //清除购物车
            if (Cart::deleteAll(['member_id' => $id])) {
                return $this->render('flow3');
            }
            return ['status' => 1, 'msg' => '订单提交成功','data'=>$model];
        } else {
            return ['status' => '-1', 'msg' => '不是post提交'];
        }
    }
//获取当前用户的订单列表
    public function actionGetUserOrderList(){
        if(\Yii::$app->user->isGuest){
            return ['status' => '-1', 'msg' => '未登录'];
        }else{
            $id = \Yii::$app->user->id;
            $order = Order::findAll(['member_id'=>$id]);
            if($order){
                return ['status' => 1, 'msg' => '当前用户的订单信息','data'=>$order];
            }else{
                return ['status' => 0, 'msg' => '当前用户的订单信息不存在'];
            }
        }
    }
    //取消订单
    public function actionDelOrder(){
        $request = \Yii::$app->request;
        if($request->isGet){
            $id = $request->get('id');
            $order = Order::findOne(['id'=>$id]);
            if($order){
                $order->status = 0;
                $order->save();
                return ['status' => 1, 'msg' => '订单取消成功','data'=>$order];
            }else{
                return ['status' => 1, 'msg' => '订单不存在'];
            }
        }
    }
//文件上传
    public function actionUpload(){
        $img = UploadedFile::getInstanceByName('img');
        if($img){
            $filename = '/upload'.$this->uniqid().'.'.$img->extension;
            $result = $img->saveAs(\Yii::getAlias('@webroot').$filename,0);
            if($result){
                return ['status'=>'1','msg'=>'','data'=>$filename];
            }
            return ['status'=>'-1','msg'=>$img->error];
        }
        return ['status'=>'-1','msg'=>'没有文件上传'];

    }

    //-发送手机验证码
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            return ['status'=>'-1','msg'=>'电话号码不正确'];
        }
        //检查上次发送时间是否超过1分钟
        $value = \Yii::$app->cache->get('time_tel_'.$tel);
        $s = time()-$value;
        if($s <60){
            return ['status'=>'-1','msg'=>'请'.(60-$s).'秒后再试'];
        }

        $code = rand(1000,9999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel'.$tel,$code,5*60);
            \Yii::$app->cache->set('time_tel_'.$tel,time(),5*60);
            //echo 'success'.$code;
            return ['status'=>'1','msg'=>'','data'=>$code];
        }else{
            return ['status'=>'-1','msg'=>'短信发送失败'];
        }
    }
//-验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>3,
                'maxLength'=>3,
            ],
        ];
        //http://www.yii2shop.com/api/captcha.html 显示验证码
        //http://www.yii2shop.com/api/captcha.html?refresh=1 获取新验证码图片地址
        //http://www.yii2shop.com/api/captcha.html?v=59573cbe28c58 新验证码图片地址
    }

}