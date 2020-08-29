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
      <span>
        <svg width="70" height="70" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/>
          <path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
          <path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/>
        </svg>
      </span>
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
