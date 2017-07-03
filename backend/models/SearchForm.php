<?php
namespace backend\models;

use yii\base\Model;
use yii\db\ActiveQuery;
class SearchForm extends Model{
    public $name;
    public $sn;
    public $minPrice;
    public $maxPrice;

    public function rules()
    {
        return [
            ['name','string','max'=>50],
            ['sn','string'],
            ['minPrice','double'],
            ['maxPrice','double'],

        ];
    }

    public function search(ActiveQuery $query)
    {
        //加载表单提交的数据
        $this->load(\Yii::$app->request->get());
        //商品名
        if($this->name){
            $query->andWhere(['like','name',$this->name]);
        }
        //货号
        if($this->sn){
            $query->andWhere(['like','sn',$this->sn]);
        }
        //最大价格
        if($this->maxPrice){
            $query->andWhere(['<=','shop_price',$this->maxPrice]);
        }
        //最低价格
        if($this->minPrice){
            $query->andWhere(['>=','shop_price',$this->minPrice]);
        }
    }
}