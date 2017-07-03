<?php

namespace backend\controllers;

use backend\components\SphinxClient;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use backend\models\Img;
use backend\models\SearchForm;
use crazyfd\qiniu\Qiniu;
use xj\uploadify\UploadAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class GoodsController extends BackendController
{
    public function actionIndex()
    {
        $query = Goods::find();
        if($keyword = \Yii::$app->request->post('keyword')) {
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

            //将查找的数据传送给视图

            return $this->render('index', ['goodes' => $goodes, 'page' => $page, 'keyword' => $keyword]);
        }else{
            $total = $query->count();
            //实例化分页工具条
            $page = new Pagination(
                [
                    'totalCount' => $total,
                    'defaultPageSize' => 2
                ]
            );

            $goodes = $query->offset($page->offset)->limit($page->limit)->orderBy(['id' => 3])->all();
            return $this->render('index', ['goodes' => $goodes, 'page' => $page, 'keyword' => $keyword]);
        }


    }
    public function actionAdd(){
        $model = new Goods();
        $goods_intro = new GoodsIntro();
        if($model->load(\Yii::$app->request->post()) && $goods_intro->load(\Yii::$app->request->post())){
            if($model->validate() && $goods_intro->validate()){
                $model->save();
                $goods_intro->goods_id=$model->id;
                $goods_intro->save();
                \Yii::$app->session->setFlash('success','商品添加成功');
                return $this->redirect(['goods/index']);
            }else {
                var_dump($model->getErrors());
            }

        }

        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        $brands = ArrayHelper::map(Brand::find()->where(['status'=>1])->all(),'id','name');
        return $this->render('add',['model'=>$model,'brands'=>$brands,'goods_intro'=>$goods_intro,'categories'=>$categories]);
    }


    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $goods_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        if($model->load(\Yii::$app->request->post()) && $goods_intro->load(\Yii::$app->request->post())){
            if($model->validate() && $goods_intro->validate()){
                $model->save();
                $goods_intro->goods_id=$model->id;
                $goods_intro->save();
                \Yii::$app->session->setFlash('success','商品修改成功');
                return $this->redirect(['goods/index']);
            }else {
                var_dump($model->getErrors());
            }
        }
        //合并数组
        $categories = ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());
        $brands = ArrayHelper::map(Brand::find()->where(['status'=>1])->all(),'id','name');
        return $this->render('add',['model'=>$model,'brands'=>$brands,'goods_intro'=>$goods_intro,'categories'=>$categories]);
    }
    //逻辑删除
    public function actionDelete($id){
        //查找出需要删除的字段
        $model = Goods::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods/index']);

    }
    public function actions() {

        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ],
            's-upload' => [//接受logo的地址
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    //设置七牛云需要的参数
//                    $ak = "9NviPZwaHe_UTMFotYDod5SvW96j0pMScBLPwwmS";
//                    $sk = "uJ-zc2Vjk2hbH80EFsgvfONZFZsVkynRC6QuEyVb";
//                    $domain = 'http://or9siglcd.bkt.clouddn.com/';
//                    $bucket = "yii2shop";
//                    //实例化七牛模型
//                    $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
//                    //要上传文件的文件的路径
//                    $fileName = \Yii::getAlias('@webroot').$action->getWebUrl();
//                    //$key是上传文件的路径,方便后面得到青牛云的地址
//                    $key = $action->getWebUrl();
//                    //上传到青牛云
//                    $qiniu->uploadFile($fileName,$key);
//                    $url = $qiniu->getLink($key);//获取七牛云的地址
//                    $action->output['fileUrl'] = $url;//将七牛云的地址赋值给本地的上传地址
                    /*
                     * 思路：首先文件上传和表单的提交没有直接的关系,如果文件上传成功会在添加的页面得到上传的地址
                     * 然后将上传成功后的地址给他保存在隐藏域随表单一起提交,如果要上传到七牛云,只需将传到七牛云的
                     * 地址赋值给本地上传的地址,随着表单一起保存在数据库
                     *
                     */
                    //上传到本地logo
                    $action->output['fileUrl'] = $action->getWebUrl();
                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
            'c-upload' => [//接受相册的地址
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    //设置七牛云需要的参数
//                    $ak = "9NviPZwaHe_UTMFotYDod5SvW96j0pMScBLPwwmS";
//                    $sk = "uJ-zc2Vjk2hbH80EFsgvfONZFZsVkynRC6QuEyVb";
//                    $domain = 'http://or9siglcd.bkt.clouddn.com/';
//                    $bucket = "yii2shop";
//                    //实例化七牛模型
//                    $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
//                    //要上传文件的文件的路径
//                    $fileName = \Yii::getAlias('@webroot').$action->getWebUrl();
//                    //$key是上传文件的路径,方便后面得到青牛云的地址
//                    $key = $action->getWebUrl();
//                    //上传到青牛云
//                    $qiniu->uploadFile($fileName,$key);
//                    $url = $qiniu->getLink($key);//获取七牛云的地址
//                    $action->output['fileUrl'] = $url;//将七牛云的地址赋值给本地的上传地址
                    /*
                     * 思路：首先文件上传和表单的提交没有直接的关系,如果文件上传成功会在添加的页面得到上传的地址
                     * 然后将上传成功后的地址给他保存在隐藏域随表单一起提交,如果要上传到七牛云,只需将传到七牛云的
                     * 地址赋值给本地上传的地址,随着表单一起保存在数据库
                     *
                     */
                    //图片上传成功后和商品关联
                    $model = new Img();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->photo = $action->getWebUrl();//得到上传成功后的地址父赋值给相册模型
                    $model->save();
                    $action->output['fileUrl'] = $model->photo;
                },
            ],
        ];

    }
    public function actionPhoto($id){
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('photo',['goods'=>$goods]);
    }
    /*
    * AJAX删除图片
    */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = Img::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

    }
//全文索引
    public function actionTest(){
        $cl = new SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);
//$cl->SetServer ( '10.6.0.6', 9312);
//$cl->SetServer ( '10.6.0.22', 9312);
//$cl->SetServer ( '10.8.8.2', 9312);
        $cl->SetConnectTimeout ( 10 );
        $cl->SetArrayResult ( true );
// $cl->SetMatchMode ( SPH_MATCH_ANY);
        $cl->SetMatchMode ( SPH_MATCH_ALL);
        $cl->SetLimits(0, 1000);
        $info = '老酸奶';//需要搜索的词
        $res = $cl->Query($info, 'goods');//shopstore_search
//print_r($cl);
        var_dump($res);
    }
}
