<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),
             60*30);  // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}
?>

$(document).on('page_ready', function() {

  var form = $('#view_del_form_1, #view_del_form_2');

  <!-- 删除触发弹窗关闭 -->
  form.on('submit', function() {
      setTimeout(function () {
        window.parent.location.replace(window.parent.location.href)//关闭iframe 并 刷新父级页面
      }, 500);

      return true;
  });
  <!-- 关闭按钮 -->
  $('.view_entry_close_icon').on('click',function(e){
    window.parent.closeIFrame();
  })
});
