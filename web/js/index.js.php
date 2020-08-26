<?php
namespace MRBS;

require '../defaultincludes.inc';

http_headers(array("Content-type: application/x-javascript"),
             60*30);  // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}


// Only show the bottom nav bar if no part of the top one is visible.
?>
var checkNav = function() {
    if ($('nav.main_calendar').eq(0).visible(true))
    {
      $('nav.main_calendar').eq(1).hide();
    }
    else
    {
      $('nav.main_calendar').eq(1).show();
    }
  };


<?php
// Replace the body elememt with the body in response, for the page href.
?>
var replaceBody = function(response, href) {
    <?php
    // We get the entire page HTML returned, but we are only interested in the <body> element.
    // That's because if we replace the whole HTML the browser will re-load the JavaScript and
    // CSS files which is unnecessary and will also cause problems if the CSS is not loaded in
    // time.
    //
    // Unfortunately, we can't use jQuery.replaceWith() on the body object as that doesn't work
    // properly.  So we have to replace the body HTML and then update the attributes for the body
    // tag afterwards.
    ?>
    var matches = response.match(/(<body[^>]*>)([^<]*(?:(?!<\/?body)<[^<]*)*)<\/body\s*>/i);
    var body = $('body');
    body.html(matches[2]);
    $('<div' + matches[1].substring(5) + '</div>').each(function() {
        $.each(this.attributes, function() {
            <?php
            // this.attributes is not a plain object, but an array
            // of attribute nodes, which contain both the name and value
            ?>
            if(this.specified) {
              <?php // Data attributes have to be updated differently from other attributes ?>
              if (this.name.substring(0, 5).toLowerCase() == 'data-')
              {
                body.data(this.name.substring(5), this.value);
              }
              else
              {
                body.attr(this.name, this.value);
              }
            }
          });
      });
    
    <?php
    // Trigger a page_ready event, because the normal document ready event
    // won't be triggered when we are just replacing the html.
    ?>
    $(document).trigger('page_ready');
    
    <?php // change the URL in the address bar ?>
    history.pushState(null, '', href);
  };
  
  
<?php
// Update the <body> element either via an Ajax call or using a pre-fetched response,
// in order to avoid flickering of the screen as we move between pages in the calendar view.
// 
// 'event' can either be an event object if the function is called from an 'on'
// handler, or else it as an href string (eg when called from flatpickr).
?>
var updateBody = function(event) {
    var href;
    
    if (typeof event === 'object')
    {
      href = $(this).attr('href');
      event.preventDefault();
    }
    else
    {
      href = event;
    }
    
    <?php // Add a "Loading ..." message ?>
    $('h2.date').text('<?php echo get_vocab('loading')?>')
                .addClass('loading');
                  
    if (updateBody.prefetched && updateBody.prefetched[href])
    {
      replaceBody(updateBody.prefetched[href], href);
    }
    else
    {
      $.get(href, 'html', function(response){
          replaceBody(response, href);
        });
    }
  };


<?php
// Pre-fetch the prev and next pages to improve performance.  They are probably
// the two most likely pages to be required.
?>
var prefetch = function() {
  
  <?php
  // Don't pre-fetch if it's been disabled in the config
  if (empty($prefetch_refresh_rate))
  {
    ?>
    return;
    <?php
  }
  
  // Don't pre-fetch and waste bandwidth if we're on a metered connection ?>
  if (isMeteredConnection())
  {
    return;
  }
  
  var delay = <?php echo $prefetch_refresh_rate?> * 1000;
  var hrefs = [$('a.prev').attr('href'), 
               $('a.next').attr('href')];
  
  <?php // Clear any existing pre-fetched data and any timeout ?>
  updateBody.prefetched = {};
  clearTimeout(prefetch.timeoutId);
  
  <?php
  // Don't pre-fetch if the page is hidden.  Just set another timeout
  ?>
  if (isHidden())
  {
    prefetch.timeoutId = setTimeout(prefetch, delay);
    return;
  }
  
  hrefs.forEach(function(href) {
    $.get({ 
        url: href, 
        dataType: 'html', 
        success: function(response) {
            updateBody.prefetched[href] = response;
            <?php // Once we've got all the responses back set off another timeout ?>
            if (Object.keys(updateBody.prefetched).length === hrefs.length)
            {
              prefetch.timeoutId = setTimeout(prefetch, delay);
            }
          }
      });
  });
  
};


$(document).on('page_ready', function() {

  $(".user_container_close").on("click",function(){
    $(this).parent().parent().hide();
  })
 
  $('.header_user_info').on("click",function(){
    $('.user_container').eq(0).show(100);
  })
  $(".user_container_close").on("click",function(){
    $(this).parent().parent().hide();
  })
 
  $('.header_set_info').on("click",function(){
    $('.user_container').eq(1).show(100);
  })
  <!-- 新增会议 -->
  $(".new_booking").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,550);
  })
  <!-- 编辑会议 -->
  $(".edit_booking").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,438);
  })
  <!-- 月新增会议 -->
  $(".monthday").on("click", function (e) {
    var jumpurl = $(this).data('jumpurl');
    showIframe(jumpurl,510,550);
  })
  <!-- 新增关闭按钮 -->
  $('.new_dialog_close_icon').on('click',function(e){
    window.parent.closeIFrame();
  })
  <!-- 父页面关闭iframe方法 -->
  function closeIFrame() {
    console.log("关闭子页面");
    $("#iFrameWrap").remove();
    $("#iFrameWrapBg").remove();
  }
  window.closeIFrame = closeIFrame;
  <!-- js动态创建iframe -->
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
  <?php
  // Turn the room and area selects into fancy select boxes and then
  // show the location menu (it's hidden to avoid screen jiggling).
  ?>
  $('.room_area_select').mrbsSelect({
    minimumResultsForSearch: -1
  });
  $('nav.location').removeClass('js_hidden');
  
  <?php
  // The bottom navigation was hidden while the Select2 boxes were formed
  // so that the correct widths could be established.  It is then shown if
  // the top navigation is not visible.
  ?>
  $('nav.main_calendar').removeClass('js_hidden');
  checkNav();
  $(window).on('scroll', checkNav);
  $(window).on('resize', checkNav);
  
  <?php
  // Only reveal the color key once the bottom navigation has been determined,
  // in order to avoid jiggling.
  ?>
  $('.color_key').removeClass('js_hidden');
  
  <?php
  // Replace the navigation links with Ajax calls in order to eliminate flickering
  // as we move between pages.
  ?>
  $('nav.arrow a, nav.view a').on('click', updateBody);
  
  <?php
  // Pre-fetch some pages to improve performance
  ?>
  prefetch();
});
