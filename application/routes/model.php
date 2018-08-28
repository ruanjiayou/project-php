<?php
/**
 * @api {get} /model/admin 管理员:admin
 * @apiName model-admin
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} phone 手机号
 * @apiParam {string} nickName 昵称
 * @apiParam {string} avatar 头像
 * @apiParam {string} password 密码
 * @apiParam {string} token 鉴权
 * @apiParam {string} salt 随机盐
 * @apiParam {string} isSA 是否是超级管理员
 * @apiParam {datetime} createAt 创建时间
 */
/**
 * @api {get} /model/admin_auth 管理员权限列表:admin_auth
 * @apiName model-admin-auth
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} adminId 管理员id
 * @apiParam {int} authorityId 权限id
 * @apiParam {string} authorityName 权限名称
 */
/**
 * @api {get} /model/area 区域:area
 * @apiName model-area
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int=0} pid 父级id
 * @apiParam {int=0} sort 排序序号
 * @apiParam {int=1} deep 层级深度
 * @apiParam {string} name 区域名称
 * @apiParam {string} region 区划
 */
/**
 * @api {get} /model/authority 权限:authority
 * @apiName model-authority
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} name 权限名称
 */
/**
 * @api {get} /model/catalog 分类:catalog
 * @apiName model-catalog
 * @apiGroup model
 * 
 * @apiParam {int} id 分类id
 * @apiParam {string} name 分类名称
 * @apiParam {string} type 标签类型,user:用户,comment:评论
 */
/**
 * @api {get} /model/tag 标签:tag
 * @apiName model-tag
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} name 标签名称
 * @apiParam {int} cataId 分类id
 * @apiParam {string} cataName 分类名称
 * @apiParam {string} type 标签类型,user:用户标签,comment:评论标签
 */
/**
 * @api {get} /model/invitation 邀请订单:invitation
 * @apiName model-invitation
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} buyerAgencyId 买家上级id
 * @apiParam {string} buyerAgencyName 买家上级昵称
 * @apiParam {string} buyerAgencyPhone 买家上级手机号
 * @apiParam {int} sellerAgencyId 卖家上级id
 * @apiParam {string} sellerAgencyName 卖家上级昵称
 * @apiParam {string} sellerAgencyPhone 卖家上级手机号
 * @apiParam {int} buyerId 买家id
 * @apiParam {string} buyerAvatar 买家头像
 * @apiParam {string} buyerName 买家昵称
 * @apiParam {string} buyerPhone 买家手机号
 * @apiParam {int} sellerId 卖家id
 * @apiParam {string} sellerAvatar 卖家头像
 * @apiParam {string} sellerName 卖家昵称
 * @apiParam {string} sellerPhone 卖家手机号
 * @apiParam {int} price 邀请订单价格
 * @apiParam {string='inviting','refused','canceling','canceled','accepted','confirmed','expired'} progress 订单详细进行态,inviting:邀请中,refused:邀请被拒绝,canceling:买家主动取消,canceled:卖家取消,accepted,卖家已接受,confirmed:买家已确认,expired:邀请订单过期
 * @apiParam {string='pending','success','fail'} status 订单状态
 * @apiParam {int=0,1} isComplaint 是否投诉了
 * @apiParam {string} complaint 投诉内容
 * @apiParam {string='not','sold','bought','yes','expired'} isComment 是否评论,not:没有被评论,sold:卖家已评论,bought:卖家已评论,yes:双方都评论了,expired:已过期
 * @apiParam {string} commentOfbuyer 买家评论
 * @apiParam {string} commentOfseller 卖家评论
 * @apiParam {string} scoreOfbuyer 买家评分
 * @apiParam {string} scoreOfseller 卖家评分
 * @apiParam {int=0,1} isRefund 是否退款
 * @apiParam {int} refund 退款额
 * @apiParam {int} x 经度
 * @apiParam {int} y 纬度
 * @apiParam {string} address 详细邀请地址
 * @apiParam {datetime} startAt 邀请开始时间
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/order 充值与提现:order
 * @apiName model-order
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {string} order_no 内部订单号
 * @apiParam {string} trade_no 交易订单号
 * @apiParam {string} reason 失败原因
 * @apiParam {int} price 金额
 * @apiParam {string='recharge','withdraw'} type 类型,recharge:充值,withdraw:提现
 * @apiParam {string='pending','success','fail'} status 交易状态
 * @apiParam {string} createdAt 创建时间
 */
/**
 * @api {get} /model/price 定价:price
 * @apiName model-price
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int=0} userId 用户id,为0类型为signin对所有人
 * @apiParam {int} value 定价
 * @apiParam {string='order','signin'} type 定价类型,signin:签到奖励,order:邀请订单定价,rebate:分成比例
 */
/**
 * @api {get} /model/rccode 推荐码与合作关系:rccode
 * @apiName model-rccode
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} agencyId 中介id
 * @apiParam {string} agencyName 中介昵称
 * @apiParam {string} agencyAvatar 中介头像
 * @apiParam {int} userId 用户id
 * @apiParam {string} userName 用户昵称
 * @apiParam {string} userAvatar 用户头像
 * @apiParam {string} rccode 推荐码
 * @apiParam {string='pending','buyer','servant'} type 类型,pending:待接受,buyer:买家类型,servant: 卖家类型
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/signin 签到:signin
 * @apiName model-signin
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/sms 短信签名和模板:sms
 * @apiName model-sms
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} logicId id
 * @apiParam {string} text 签名或模板
 * @apiParam {string} image 图片
 * @apiParam {string='sign','tpl'} type 类型,sign:短信签名,tpl:短信模板
 * @apiParam {string='pending','using','fail','success'} status 状态,pending:腾讯审核中,success:审核成功,fail:审核失败,using:使用中,不可被删除
 * @apiParam {string} reason 失败原因
 * @apiParam {string} description 描述
 * @apiParam {string} createdAt 创建时间
 */
/**
 * @api {get} /model/sms-message 消息:sms_message
 * @apiName model-sms-message
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} title 消息标题
 * @apiParam {string=''} code 短信验证码
 * @apiParam {string} content 消息内容
 * @apiParam {string=''} phone 手机号
 * @apiParam {string='forgot','modify','zhuche','system','invite','cancel','refused','accepted','canceled'} type 消息类型,详细说明见sms-place表
 * @apiParam {string='pending','fail','success'} status 状态
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/sms-place 消息占位:sms_place
 * @apiName model-sms-place
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} signId 签名id
 * @apiParam {int} sign 签名名称
 * @apiParam {int} tplId 模板id
 * @apiParam {int} tpl 模板内容
 * @apiParam {int=0,1} isSms 是否短信(否则是内部消息)
 * @apiParam {string='forgot','modify','zhuche','invite','cancel','refused','accepted','canceled',} place 占位名称,forgot:忘记密码,modify:修改密码,zhuche:注册账号,invite:邀请,cancel:取消,refused:拒绝,accepted:接受,canceled:取消
 * @apiParam {string} description 描述
 */
/**
 * @api {get} /model/user 用户:user
 * @apiName model-user
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} phone 手机号
 * @apiParam {string} password 密码
 * @apiParam {string} token 鉴权
 * @apiParam {string} salt 随机盐
 * @apiParam {string} identity 身份证
 * @apiParam {string} rccode 推荐码
 * @apiParam {string} trueName 真实姓名
 * @apiParam {string} nickName 昵称
 * @apiParam {string} age 年龄
 * @apiParam {string} avatar 头像
 * @apiParam {string} introduce 介绍
 * @apiParam {string} tags 标签
 * @apiParam {int} height 身高
 * @apiParam {int} weight 体重
 * @apiParam {int} score 评分
 * @apiParam {float} x 经度
 * @apiParam {float} y 纬度
 * @apiParam {int} images 图片数量
 * @apiParam {int} popular 人气
 * @apiParam {int} money 钱包余额
 * @apiParam {string} address 详细地址
 * @apiParam {int} cityId 所在城市id
 * @apiParam {string} city 所在城市名称
 * @apiParam {string='buyer','servant','agency'} type 用户类型,buyer:买家,servant:卖家,agency:中介
 * @apiParam {string='registered','approving','approved','forbidden'} status 用户状态,registered:刚注册需填写资料,approving:已填资料待审核,approved:审核通过,forbidden:审核失败或封号
 * @apiParam {string='hot','recommend','normal'} attr 用户属性,hot:热门,recommend:推荐,normal:普通
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/user-bill 用户收支:user_bill
 * @apiName model-user-bill
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {string='income','expent'} type 类型,income:收入,expent:支出
 * @apiParam {string} value 收支数值
 * @apiParam {string} detail 收支描述
 * @apiParam {string} createdAt 创建时间
 */
/**
 * @api {get} /model/user-image 用户相册:user_image
 * @apiName model-banner
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {string} url 图片url
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/user-message 用户的消息:user_message
 * @apiName model-user-message
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {string} userName 用户昵称
 * @apiParam {string} content 内容
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/user-work
 * @apiName model-user-work
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {int} userId 用户id
 * @apiParam {datetime} workAt 工作日期
 * @apiParam {datetime} createdAt 创建时间
 */
/**
 * @api {get} /model/test test
 * @apiName model-test
 * @apiGroup model
 * 
 * @apiParam {int} id id
 * @apiParam {string} url url
 */
return [];
?>