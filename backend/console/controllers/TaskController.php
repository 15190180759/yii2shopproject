<?php
namespace console\controllers;

use backend\models\Goods;
use yii\console\Controller;
use frontend\models\Order;
class TaskController extends Controller{
    public function actionClean(){
        set_time_limit(0);//不限制脚本执行时间
        while (1){
            //超时未支付订单  待支付状态1超过1小时==》已取消0
            $models = Order::find()->where(['status'=>1])->andWhere(['<','create_time',time()-3600])->all();
            foreach ($models as $model){
                $model->status = 0;
                $model->save();
                //返还库存
                foreach($model->goods as $goods){
                    Goods::updateAllCounters(['stock'=>$goods->amount],'id='.$goods->goods_id);
                }

            }
            //1秒钟执行一次
           // sleep(1);
        }
    }
}