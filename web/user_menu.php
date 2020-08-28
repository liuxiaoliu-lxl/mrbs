<?php
namespace MRBS;

require_once "defaultincludes.inc";

$username = $_SESSION['UserName'];
$userinfo = get_user_info($username);
$email    = $userinfo['email'];

echo <<<EOF
<div class="user_container">
  <div class="user_container_title">
    <span>我的账户</span>
    <div class="user_container_close">
    <img src="images/close_icon.png"/>
    </div>
  </div>
  <div class="user_container_infos">
    <div class="user_info_icon">
        <img src="images/user-filling.png" />
    </div>
    <div class="user_infos_info">
        <p>$username</p>
        <p>$email</p>
EOF;
print_logoff();
echo <<<EOF
    </div>
  </div>
</div>
EOF;
echo <<<EOF
<div class="user_container">
  <div class="user_container_title">
    <span>设置</span>
    <div class="user_container_close">
      <img src="images/close_icon.png"/>
    </div>
  </div>
  <div class="user_container_navs">
    <a href="edit_users.php">用户设置</a>
    <a href="admin.php">会议室设置</a>
  </div>
</div>
EOF;
