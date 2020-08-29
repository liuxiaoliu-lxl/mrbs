<?php
namespace MRBS;

require_once "../functions.inc";

http_headers(array("Content-type: application/x-javascript"),60*30);  // 30 minute expiry


// Add a class of "js" so that we know if we're using JavaScript or not.
// jQuery hasn't been loaded yet, so use JavaScript ?>
var html = document.getElementsByTagName('html')[0];
html.classList.add('js');

window.onload = function () {

}

$(document).on('page_ready', function() {

  <!-- 右侧抽屉关闭icon -->
  $(".user_container_close").on("click",function(){
    $(this).parent().parent().hide(100);
    $('.header_user_info').removeClass("displayed");
    $('.header_set_info').removeClass("displayed");
  })
  <!-- header 用户icon控制右侧用户抽屉展示/隐藏 -->
  $('.header_user_info').on("click",function(){
    $('.header_set_info').removeClass("displayed");
    $('.user_container').eq(1).hide();

    if($(this).hasClass("displayed")){
      $(this).removeClass("displayed");
      $('.user_container').eq(0).hide(100);
    }else{
      $(this).addClass("displayed");
      $('.user_container').eq(0).show(100);
    }

  })
  <!-- header 设置icon 控制右侧设置抽屉展示/隐藏 -->
  $('.header_set_info').on("click",function(){
    $('.header_user_info').removeClass("displayed");
    $('.user_container').eq(0).hide();

    if($(this).hasClass("displayed")){
      $('.user_container').eq(1).hide(100);
      $(this).removeClass("displayed");
    }else{
      $(this).addClass("displayed");
      $('.user_container').eq(1).show(100);
    }
  })

});
