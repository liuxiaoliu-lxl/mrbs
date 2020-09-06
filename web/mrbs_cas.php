<?php

error_reporting(0);

//require_once "defaultincludes.inc";
//require_once "functions_table.inc";
require_once "phpCAS-master/source/CAS.php";
//require_once "session/session_php.inc";
//
// phpCAS simple client
//

// import phpCAS lib
// 引入文件
//  开启log，注意目录读写权限
phpCAS::setDebug('tmp/cas.log');
// initialize phpCAS
// 四个参数分别是
// cas server 版本
// cas server 域名
// cas server 端口
// cas server 路径
phpCAS::client(CAS_VERSION_2_0, 'sso.idgcapital.com', 443, '/cas');

// 不验证SSL证书
phpCAS::setNoCasServerValidation();

// force CAS authentication
// 这个是强制认证模式，查看 client.php 可以找到集中不同的方式
// forceAuthentication
// checkAuthentication
// renewAuthentication
// 根据自己需要调用即可
phpCAS::forceAuthentication();

// 处理登出请求。cas服务端会发送请求通知客户端。如果没有同步登出，可能是服务端跟客户端无法通信（比如我的客户端是localhost, 服务端在云上）
phpCAS::handleLogoutRequests();

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().
// 获取用户名
$username = phpCAS::getUser();
// 获取完整用户信息（在上一篇文章中介绍了如何扩展用户信息）
$userinfo = phpCAS::getAttributes();

$_SESSION['UserName'] = $username;

MRBS\authUserCAS($username);

// logout if desired
if (isset($_REQUEST['logout'])) {
  // 这里貌似可以指定退出后返回的页面，但是我没有成功
  // phpCAS::logout(['service'=>'http://localhost/cas-client/index.php']);
  phpCAS::logout();
}


