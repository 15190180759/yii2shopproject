<?php
/**
 * Created by PhpStorm.
 * user: wang
 * Date: 2017/6/11 0011
 * Time: 下午 14:05
 */
namespace backend\models;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class GoodsCategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}