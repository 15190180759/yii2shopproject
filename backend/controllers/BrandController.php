<?php

namespace backend\controllers;

use backend\models\Brand;
use crazyfd\qiniu\Qiniu;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use yii\web\Request;
use xj\uploadify\UploadAction;

class BrandController extends BackendController
{
    public function actionIndex(){
        //分页,每页显示2条
        //获取所有的分类
        $query = Brand::find();
        //总条数 每页显示多少条 当前在第几页
        $total = $query->count();
        $page = new Pagination(
            [
                'totalCount'=>$total,
                'defaultPageSize'=>2
            ]
        );
        //限制每页输出的条数
        $brands = $query->offset($page->offset)->limit($page->limit)->all();
        //将查找的数据传送给视图
        return  $this->render('index',['brands'=>$brands,'page'=>$page]);
    }
    public function actionAdd(){
        //实例化品牌模型
        $model = new Brand();
        $request = new Request();
        if($request->isPost){
             $model->load($request->post());
            //接收数据之前实例化文件上传
            if($model->validate()){
                //如果上传了文件的情况下
                //保存数据
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['brand/index']);
            }else{
                //失败打印错误
                var_dump($model->getErrors());exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改页面
    public function actionEdit($id){
        //根据id查找数据
        $model = Brand::findOne(['id'=>$id]);
        $request = new Request();
        if($request->isPost){
            $model->load($request->post());
            //接收数据之前实例化文件上传
            if($model->validate()){
                //如果上传了文件的情况下
                //保存数据
                $model->save();
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['brand/index']);
            }else{
                //失败打印错误
                var_dump($model->getErrors());exit;
            }
        }
        //将查找的数据传给修改页面
        return  $this->render('add',['model'=>$model]);
    }

    //逻辑删除
    public function actionDelete($id){
        //查找出需要删除的字段
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        $model->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);

    }
    public function actions() {
        return [
            's-upload' => [
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
                    //设置七牛云需要的参数
                    $ak = "9NviPZwaHe_UTMFotYDod5SvW96j0pMScBLPwwmS";
                    $sk = "uJ-zc2Vjk2hbH80EFsgvfONZFZsVkynRC6QuEyVb";
                    $domain = 'http://or9siglcd.bkt.clouddn.com/';
                    $bucket = "yii2shop";
                    //实例化七牛模型
                    $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
                    //要上传文件的文件的路径
                    $fileName = \Yii::getAlias('@webroot').$action->getWebUrl();
                    //$key是上传文件的路径,方便后面得到青牛云的地址
                    $key = $action->getWebUrl();
                    //上传到青牛云
                    $qiniu->uploadFile($fileName,$key);
                    $url = $qiniu->getLink($key);//获取七牛云的地址
                    $action->output['fileUrl'] = $url;//将七牛云的地址赋值给本地的上传地址
                    /*
                     * 思路：首先文件上传和表单的提交没有直接的关系,如果文件上传成功会在添加的页面得到上传的地址
                     * 然后将上传成功后的地址给他保存在隐藏域随表单一起提交,如果要上传到七牛云,只需将传到七牛云的
                     * 地址赋值给本地上传的地址,随着表单一起保存在数据库
                     *
                     */
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }

}
