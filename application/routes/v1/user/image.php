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
   * @apiParam {array} images 图片url数组
   */
  'post /v1/user/images' => function($req, $res) {
    $userBLL = new UserBLL();
    $userImageBLL = new UserImageBLL();
    $user = $userBLL->auth($req);
    $imageNum = intval($user['images']);

    // 1.cos上传
    $images = $req->file('images');
    $info = $images->move(ROOT_PATH.'public/images/');
    $filepath = _::replace(ROOT_PATH.'public/images/'.$info->getSaveName(), '\\', '/');
    $cos = wxHelper::addFile('project/'.date('Y-m-d H:i:s').'-'._::random(16, 'imix'), fopen($filepath, 'rb'));
    if($imageNum < 9) {
      $userImageBLL->create(['userId'=>$user['id'],'url'=>$cos['ObjectURL'], 'createdAt'=>date('Y-m-d H:i:s')]);
      $user = $userBLL->update(['images'=>++$imageNum], ['id'=>$user['id']]);
    } else {
      thrower('image', 'overLimit');
    }
    // 2.文件上传
    // $images = $req->file('images');
    // if($imageNum < 9) {
    //   $user = $userBLL->update(['images'=>++$imageNum], ['id'=>$user['id']]);
    // } else {
    //   thrower('image', 'overLimit');
    // }
    // $info = $images->move(ROOT_PATH.'public/images/');
    // $url = _::replace('/images/'.$info->getSaveName(), '\\', '/');
    // $result = $userImageBLL->create(['userId'=>$user['id'],'url'=>$url, 'createdAt'=>date('Y-m-d H:i:s')]);
    // 3.字符串上传
    // $images = input('post.images/a');
    // $result = [];
    // if(_::type($images)==='array') {
    //   foreach($images as $index => $url) {
    //     if($imageNum<9) {
    //       $url = $userImageBLL->create(['userId'=>$user['id'],'url'=>$url, 'createdAt'=>date('Y-m-d H:i:s')]);
    //       array_push($result, $url);
    //       $imageNum++;
    //     }
    //   }
    // }
    // if($imageNum!==intval($user['images'])) {
    //   $user = $userBLL->update(['images'=>$imageNum], ['id'=>$user['id']]);
    // }
    $res->return(['url'=>$cos['ObjectURL']]);
  },
  /**
   * @api {delete} /v1/user/images 删除多张图片
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
    $userBLL = new UserBLL();
    $userImageBLL = new UserImageBLL();
    $user = $userBLL->auth($req);

    $param = input('delete.')['id'];
    if(_::isArray($param)) {
      $condition = ['id'=>['in', $param], 'userId'=>$user['id']];
      $imagesData = $userImageBLL->getList(['where'=>$condition]);
      $images = $user['images'] - $imagesData['count'];
      $userImageBLL->destroy($condition);
      if($images < 0) {
        $images = 0;
      }
      $user = $userBLL->update(['images'=>$images], $user['id']);
    } else {
      thrower('common', 'validation');
    }
    $res->success();
  },
  /**
   * @api {delete} /v1/user/images/:imageId 删除一张图片
   * @apiGroup user-images
   * 
   * @apiHeader {string} token 鉴权
   */
  'delete /v1/user/images/:imageId' => function($req, $res) {
    $userBLL = new UserBLL();
    $userImageBLL = new UserImageBLL();
    $user = $userBLL->auth($req);
    $userImageBLL->destroy($req->param('imageId'));
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
    $userBLL = new UserBLL();
    $userImageBLL = new UserImageBLL();
    $user = $userBLL->auth($req);

    $id = $req->param('imageId');
    // TODO: 这样获取不了文件,改: file_get_contents('php://input', 'r'));
    $images = $req->file();
    if(!empty($images)) {
      $info = $images->move(ROOT_PATH.'public/images/');
      $url = _::replace('/images/'.$info->getSaveName(), '\\', '/');
      $result = $userImageBLL->update(['userId'=>$user['id'], 'id'=>$id], ['url'=>$url, 'createdAt'=>date('Y-m-d H:i:s')]);
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
   *   stack: '',
   *   pagination: {
   *     page: 1,
   *     pages: 1,
   *     limit: 0,
   *     count: 1,
   *     total: 1,
   *   }
   * }
   */
  'get /v1/user/images' => function($req, $res) {
    $userBLL = new UserBLL();
    $userImageBLL = new UserImageBLL();
    $user = $userBLL->auth($req);

    $hql = ['where'=>['userId'=>$user['id']]];
    $result = $userImageBLL->getAll($hql);
    $res->paging($result);
  }
];

?>