<?php
namespace MRBS;

require_once "../functions.inc";

http_headers(array("Content-type: application/x-javascript"),
             60*30);  // 30 minute expiry


// Add a class of "js" so that we know if we're using JavaScript or not.
// jQuery hasn't been loaded yet, so use JavaScript ?>
var html = document.getElementsByTagName('html')[0];
html.classList.add('js');


window.onload = function () {
  $(".new_booking").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,550);
  })
  $(".edit_booking").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,550);
  })

  $(".monthday").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,550);
  })

  function closeIFrame() {
    console.log("关闭子页面");
    $("#iFrameWrap").remove();
    $("#iFrameWrapBg").remove();
  }
  function showIframe(url, w, h) {
    //添加iframe
    var if_w = w;
    var if_h = h;
    //allowTransparency='true' 设置背景透明
    $("<iframe width='" + if_w + "' height='" + if_h + "' id='iFrameWrap' name='iFrameWrap' style='position:absolute;z-index:999;'  frameborder='no' marginheight='0' marginwidth='0' allowTransparency='true'></iframe>").prependTo('body');
    var st = document.documentElement.scrollTop || document.body.scrollTop;//滚动条距顶部的距离
    var sl = document.documentElement.scrollLeft || document.body.scrollLeft;//滚动条距左边的距离
    var ch = document.documentElement.clientHeight;//屏幕的高度
    var cw = document.documentElement.clientWidth;//屏幕的宽度
    var objH = $("#iFrameWrap").height();//浮动对象的高度
    var objW = $("#iFrameWrap").width();//浮动对象的宽度
    var objT = Number(st) + (Number(ch) - Number(objH)) / 2;
    var objL = Number(sl) + (Number(cw) - Number(objW)) / 2;
    $("#iFrameWrap").css('left', objL);
    $("#iFrameWrap").css('top', objT);

    $("#iFrameWrap").attr("src", url)

    //添加背景遮罩
    $("<div id='iFrameWrapBg' style='background-color: Gray;display:block;z-index:999;position:absolute;left:0px;top:0px;filter:Alpha(Opacity=30);/* IE */-moz-opacity:0.4;/* Moz + FF */opacity: 0.4; '/>").prependTo('body');
    var bgWidth = Math.max($("body").width(), cw);
    var bgHeight = Math.max($("body").height(), ch);
    $("#iFrameWrapBg").css({ width: bgWidth, height: bgHeight });

    // 点击背景遮罩移除iframe和背景
    $("#iFrameWrapBg").click(function () {
      $("#iFrameWrap").remove();
      $("#iFrameWrapBg").remove();
    });
  }
}
