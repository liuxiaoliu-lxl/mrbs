<?php

namespace MRBS;

require '../defaultincludes.inc';

http_headers(array("Content-type: application/x-javascript"),
  60*30);  // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}
?>

$(document).on('page_ready', function() {

  //js date begin
  //url拼接，跳转
  var onMinicalChange = function(dateStr) {
    var href = 'index.php';
    href += '?view=' + args.view;
    href += '&page_date=' + dateStr;
    href += '&area=' + args.area;
    href += '&room=' + args.room;
    if (args.site)
    {
      href += '&site=' + encodeURIComponent(args.site);
    }
    updateBody(href);
  };

  //初始化日期
  var config = {
    format: 'YYYY-MM-DD',
    locale: moment.locale('zh-cn'),
    inline: true,
    sideBySide: true,
  };
  //初始化日期
  if(args.pageDate){
    config.defaultDate = args.pageDate
  }
  var picker = $('#leftNavDatepicker').datetimepicker(config);

  //日期change事件
  picker.on('dp.change', function (e) {
    var date = new Date(e.date);
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    month = month >= 10 ? month : '0'+month;
    day = day >= 10 ? day : "0"+day;
    var dateStr = year + "-" + month + "-" + day;
    onMinicalChange(dateStr);
  });

  $(".leftNav--topbar-iconbtn").on("click",function(e){
    const expanded = $(this).data('expanded');
    if(expanded){ //左侧抽屉展开状态下，进行收起操作
      $(".leftNav--large").hide();
      $(".leftNav--small").show();
      $('.main_calendar').addClass('leftNav--small-state');
    }else{ //左侧抽屉收起状态，进行展开操作
      $(".leftNav--small").hide();
      $(".leftNav--large").show();
      $('.main_calendar').removeClass('leftNav--small-state');
    }
  })
  //js date end

  <?php
  if (!empty($display_mincals)){
  ?>
  if (!isMobile())
  {
    $('.minicalendars').addClass('formed');
  }
  <?php
  }
  ?>
  $('.view_container').removeClass('js_hidden');
});
