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

  $(".user_container_close").on("click",function(){
    $(this).parent().parent().hide();
  })

  $('.header_user_info').on("click",function(){
    $('.user_container').eq(0).show(100);
    $('.user_container').eq(1).hide(100);
  })

  $('.header_set_info').on("click",function(){
    $('.user_container').eq(1).show(100);
    $('.user_container').eq(0).hide(100);
  })

});
