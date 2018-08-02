<?php
use app\model;
use think\Request;
use think\Response;

return [
  /**
   * @api {post} /v1/user/images 上传图片
   * @apiGroup user-images
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: [{
   *     id: 1,
   *     userId: 1,
   *     url: '',
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'post /v1/user/images' => function($req, $res) {
    $user = UserBLL::auth($req);
    $images = $req->file('images');
    if($user['images'] < 9) {
      $user = UserBLL::update(['images'=>++$user['images']], ['id'=>$user['id']]);
    } else {
      thrower('image', 'overLimit');
    }
    $info = $images->move(ROOT_PATH.'public/images/');
    $url = _::replace('/images/'.$info->getSaveName(), '\\', '/');
    $result = model('userImage')->add(['userId'=>$user['id'],'url'=>$url, 'createdAt'=>date('Y-m-d H:i:s')]);
    $res->return($result);
  },
  /**
   * @api {delete} /v1/user/images 删除图片
   * @apiGroup user-images
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {array} id body参数
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: null,
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'delete /v1/user/images' => function($req, $res) {
    $user = UserBLL::auth($req);
    $param = input('delete.')['id'];
    if(_::isArray($param)) {
      $condition = ['id'=>['in', $param], 'userId'=>$user['id']];
      $imagesData = model('userImage')->getList(['where'=>$condition]);
      $images = $user['images'] - $imagesData->count();
      model('userImage')->remove($condition);
      if($images < 0) {
        $images = 0;
      }
      $user = UserBLL::update(['images'=>$images], ['id'=>$user['id']]);
    } else {
      thrower('common', 'validation');
    }
    $res->success();
  },
  /**
   * @api {put} /v1/user/images/:imageId 修改图片
   * @apiGroup user-images
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiParam {file} image 图片文件
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: {
   *     id: 1,
   *     userId: 1,
   *     url: '',
   *     createdAt: "2018-07-31 17:43:48"
   *   },
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'put /v1/user/images/:imageId' => function($req, $res) {
    $user = UserBLL::auth($req);
    $id = $req->param('imageId');
    // TODO: 这样获取不了文件,改: file_get_contents('php://input', 'r'));
    $images = $req->file();
    if(!empty($images)) {
      $info = $images->move(ROOT_PATH.'public/images/');
      $url = _::replace('/images/'.$info->getSaveName(), '\\', '/');
      $result = model('userImage')->edit(['userId'=>$user['id'], 'id'=>$id], ['url'=>$url, 'createdAt'=>date('Y-m-d H:i:s')]);
      $res->return($result);
    } else {
      $res->fail();
    }
  },
  /**
   * @api {get} /v1/user/images 获取所有图片
   * @apiGroup user-images
   * 
   * @apiHeader {string} token 鉴权
   * 
   * @apiSuccessExample Success-Response:
   * HTTP/1.1 200 OK
   * {
   *   state: 'success',
   *   edata: [{
   *     id: 1,
   *     userId: 1,
   *     url: '',
   *     createdAt: "2018-07-31 17:43:48"
   *   }],
   *   ecode: 0,
   *   error: '',
   *   stack: ''
   * }
   */
  'get /v1/user/images' => function($req, $res) {
    $user = UserBLL::auth($req);
    $hql = ['where'=>['userId'=>$user['id']], 'limit'=>0];
    $result = model('userImage')->getList($hql);
    $res->paging($result);
  }
];

?>