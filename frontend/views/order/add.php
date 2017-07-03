<?php
/**
 * @var yii\web\view;
 */
$this->registerCssFile('style/fillin.css',['depends'=>['frontend\assets\IndexAsset']]);
?>
    <!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/images/logo.png" alt="京西商城"></a></h2>
			<div class="flow fr flow2">
				<ul>
					<li>1.我的购物车</li>
					<li class="cur">2.填写核对订单信息</li>
					<li>3.成功提交订单</li>
				</ul>
			</div>
		</div>
	</div>
	<!-- 页面头部 end -->

	<div style="clear:both;"></div>
<form action="<?=\yii\helpers\Url::to(['order/index'])?>" method="post">
    <input type="hidden" name="_csrf-frontend" value="<?=Yii::$app->request->csrfToken?>">
	<!-- 主体部分 start -->
	<div class="fillin w990 bc mt15">
		<div class="fillin_hd">
			<h2>填写并核对订单信息</h2>
		</div>

		<div class="fillin_bd">
			<!-- 收货人信息  start-->
			<div class="address">
				<h3>收货人信息</h3>
				<div class="address_info">
                    <?php foreach($addresses as $address):;?>
				<p>
					<input type="radio" value="<?=$address->id?>" name="address_id" <?=$address->status?'checked':'';?>/><?=$address->username.'&nbsp;'.$address->tel.
                        '&nbsp;'.$address->tel.
                        '&nbsp;'.\frontend\models\Region::findOne(['id'=>$address->province])->name.
                        '&nbsp;'.\frontend\models\Region::findOne(['id'=>$address->city])->name.
                        '&nbsp;'.\frontend\models\Region::findOne(['id'=>$address->district])->name.
                        '&nbsp;'.$address->address
                        ?>
                </p>
                    <?php endforeach;?>
				</div>




			</div>
			<!-- 收货人信息  end-->

			<!-- 配送方式 start -->
			<div class="delivery">
				<h3>送货方式 </h3>


				<div class="delivery_select">

					<table>
						<thead>
							<tr>
								<th class="col1">送货方式</th>
								<th class="col2">运费</th>
								<th class="col3">运费标准</th>
							</tr>
						</thead>
						<tbody>
                        <?php foreach(\frontend\models\Order::$delivery as $k=>$delivery):?>
							<tr class="cur">
								<td>
									<input type="radio" name="delivery_id" value="<?=$delivery['id']?>" <?=$k?'':'checked'?>/><?=$delivery['name']?>
                                </td>
								<td><?=$delivery['price']?></td>
								<td><?=$delivery['desc']?></td>
							</tr>
                        <?php endforeach;?>
						</tbody>
					</table>


				</div>
			</div>

			<!-- 配送方式 end --> 

			<!-- 支付方式  start-->
			<div class="pay">
				<h3>支付方式 </h3>


				<div class="pay_select">


					<table>
                        <?php foreach(\frontend\models\Order::$payment as $k=>$payment):?>
						<tr class="cur">
							<td class="col1"><input type="radio" name="payment_id" value="<?=$payment['id']?>"<?=$k?'':'checked'?> /><?=$payment['name']?></td>
							<td class="col2"><?=$payment['desc']?></td>
						</tr>
                        <?php endforeach;?>
					</table>

				</div>
			</div>
            <!-- 商品清单 start -->
			<div class="goods">
				<h3>商品清单</h3>
				<table>
					<thead>
						<tr>
							<th class="col1">商品</th>
							<th class="col3">价格</th>
							<th class="col4">数量</th>
							<th class="col5">小计</th>
						</tr>	
					</thead>
					<tbody>
                        <?php foreach($carts as $cart):?>
						<tr id="cart">
							<td class="col1"><a href=""><?=\yii\helpers\Html::img('http://admin.yiishop.com/'.$cart->goods->logo);?></a>  <strong><a href="">【1111购物狂欢节】等你开启剁手之旅</a></strong></td>
							<td class="col3">￥<?=$cart->goods->shop_price?></td>
							<td class="col4"><?=$cart->amount?></td>
							<td class="col5"><span><?=$cart->goods->shop_price*$cart->amount?></span></td>
						</tr>
                    <?php endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<ul id="end">
									<li>
										<span></span>件商品，总商品金额:￥
										<em>0.00</em>
									</li>
									<li>
                                        <span>运费：￥</span>
                                        <em>0.00</em>
                                    </li>
									<li>
										<span>应付总额：￥</span>
										<em>0.00</em>
									</li>
								</ul>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<!-- 商品清单 end -->
		
		</div>

		<div class="fillin_ft">
            <?=\yii\bootstrap\Html::submitButton('提交订单',['class'=>'btn btn-danger btn-lg','id'=>'order'])?>
            <input type="hidden" name="total" id="total" value="">
			<p>应付总额：￥<strong></strong></p>
			
		</div>
	</div>
</form>
	<!-- 主体部分 end -->
<?php
/**
 * @var yii\web\view;
 */

$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $(function(){
            //商品数量
            var amount = 0;
           $('#cart .col4').each(function(){
              // console.debug($(this).text())
              amount +=  parseInt($(this).text());
           });
          $('#end li:eq(0) span').text(amount);
          //小计
          var money = 0;
          $('#cart .col5').each(function(){
               //console.debug($(this).text())
              money +=  parseInt($(this).text());
           });         
          $('#end li:eq(0) em').text(money); 
          
         //默认选中
          var delivery_money = $('input[name=delivery_id][checked]').closest('tr').find('td:eq(1)').text();
          $('#end li:eq(1) em').text(delivery_money);
          var total = parseFloat($('#end li:eq(0) em').text()) + parseFloat($('#end li:eq(1) em').text());
            $('#end li:eq(2) em').text(total);
            $('.fillin_ft p strong').text(total);
            //选择默认情况下给total隐藏域传的值
            $('#total').val(total);
         //点击配送方式绑定事件
          $('input[name=delivery_id]').click(function(){
            //console.debug($(this).closest('tr').find('td:eq(1)').text());
            var delivery_money = $(this).closest('tr').find('td:eq(1)').text();
            $('#end li:eq(1) em').text(delivery_money);
            var total = parseFloat($('#end li:eq(0) em').text()) + parseFloat($('#end li:eq(1) em').text());
            $('#end li:eq(2) em').text(total);
            $('.fillin_ft p strong').text(total);
            $('#total').val(total);//点击事件给隐藏域传的值
            $('#end').val(total);
           });
          
          //没有订单的时不提交
            $('#order').click(function(){
              if($('#end li:eq(0) span').text()==0){
                  layer.msg('没有订单可以提交');
                  return false;
              }
            })
        })

JS

));