## php_screw加密项目代码
时间: 2018-10-15 16:41:24

centos系统,php版本7.0,数据库是mysql
- 查看php-config位置: whereis php-config
- 安装必要的php扩展: yum install php70w-devel php70w-pear
- 安装zlib-devel: yum install zlib-devel
- 安装gcc(如果没安装): yum install gcc
- ~~~下载php_screw源码: wget http://nchc.dl.sourceforge.net/project/php-screw/php-screw/1.5/php_screw-1.5.tar.gz~~~
- ~~~解压文件: tar zxvf php_screw-1.5.tar.gz~~~
- ~~~进入源码目录: cd php_screw_1.5~~~
- ~~~修改加密算法: vim php_screw.h  具体的看参考文章~~~
- ~~~加密密匙: vim my_screw.h  具体的看参考文章~~~
- php_screw不支持php5.3以上版本,改用了php_screw_plus
- 将git php_screw_plus项目的zip包解压并上传到服务器
- 进入解压后的目录
- 运行命令查看信息并生成: phpize
- 设置配置: ./configure --with-php-config=/usr/bin/php-config
- 编译安装: make && make install
- 在php.ini中加入: extension=php_screw_plus.so
- 编译加密工具: 进入目录 cd tools  然后运行 make
- 将 tools 目录下加密用的工具 screw 拷贝到适当目录(添加screw命令,相对window的环境PATH): cp screw /usr/bin/
- 重启php: service php-fpm restart
- 项目加密脚本(加密后调API不变): 进入项目根目录,运行 screw ./
- 解密: 项目根目录执行 screw ./ -d

可能碰到的错误以及处理方法:
- 可能存在的报错,gcc没安装: configure: error: no acceptable C compiler found in $PATH
- make && make install
- 编译报错: 把所有 CG(extended_info) = 1;修改为 CG(compiler_options) |= ZEND_COMPILE_EXTENDED_INFO;
参考文章:
https://blog.csdn.net/gaoxuaiguoyi/article/details/53466860
https://blog.csdn.net/songtianyang01/article/details/36184375
https://github.com/del-xiong/screw-plus