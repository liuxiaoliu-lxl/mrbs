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
  <!-- 新建：天 -->
  $('table#day_main').on('click',function(e){
    showDialog(e || window.event);
  })

  <!-- 新建：周 -->
  $('table#week_main').on('click',function(e){
    showDialog(e || window.event);
  })

  <!-- 新建：月 -->
  $('table#month_main').on('click',function(e){
    var e = e || window.event;
    var target = e.target;
    var $target = $(target);
    var $parent = $target.parents('td.valid');

    if(target.nodeName == "A"){
      if($target.hasClass('edit_booking')){//如果点击的是已有会议，则直接弹出查看窗
        showDialog(e || window.event);
      }else{//点击的不是已有会议
        if($parent.hasClass('active')){ //如果是选中状态，则再次点击弹出新建窗
          showDialog(e || window.event);
        }else{//如果不是选中状态，则修改为选中状态
          var $table = $('table#month_main');
          var $tdChild = $table.find("td");
          $tdChild.each(function(index,ele){
            $(ele).removeClass("active");
          })
          $parent.addClass('active');

          modifyMonthBookList($parent);
        }
      }
    }
  })
  <!-- 动态计算月表格的高度，使其不出现滚动条 -->
function initMonthTRHeight(){
  if(args.view == 'month'){
    var $table = $('table#month_main > tbody');
    var $tr = $table.find('tr');
    var trLen = $tr.length;
    var h = $('.table_container').height() - 50;
    $('table#month_main > tbody').find('div.cell_container').css({ //3:边线和一位向下取整
     height: h / trLen -3
    });
    <!-- 月表格才展示某天的会议详情 -->
    $('div.booking--list').css({
      display:"block"
    })
  }
}

initMonthTRHeight();

  <!-- 初始切换到month表格，显示默认天会议 -->
function initMonthBookList(){
  if(args.view == 'month'){
    var day = args.pageDate.split("-")[2];
    day = parseInt(day);
    var $selectedTd = $('table#month_main > tbody').find('td[data-day="' + day + '"]');

    var $booking_list = $selectedTd.find('div.booking_list div'); //copy 会议列表
    if($booking_list.length == 0){
      $("div.booking--list-content").empty();
      $("div.booking--list-content").append('<div>暂无会议</div>');
    }else{
      $("div.booking--list-content").empty();
      $("div.booking--list-content").append($booking_list.clone(true));
    }

    //会议列表详情title
    var title = genBookListTitle(args.pageDate);
    $('div.booking--list-title').html(title);

  }
}

initMonthBookList();

  <!-- 点击表格时，展示选择的天的会议 -->
function modifyMonthBookList(ele){
  if(args.view == 'month'){
    var $selectedTd = ele;
    var $booking_list = $selectedTd.find('div.booking_list div');
    if($booking_list.length == 0){
      $("div.booking--list-content").empty();
      $("div.booking--list-content").append('<div>暂无会议</div>');
    }else{
      $("div.booking--list-content").empty();
      $("div.booking--list-content").append($booking_list.clone(true));
    }


    var dateStr = $selectedTd.data('date');
    var title = genBookListTitle(dateStr);
    $('div.booking--list-title').html(title);
  }
}

  <!-- 月表格，选中天，会议详情列表title -->
function genBookListTitle(str){
  var strArr = str.split("-");
  var dateStr = strArr[1] + "月" + strArr[2] + "日";
  var day = new Date(str).getDay();
  var week = "";
  switch(day){
    case 1:
      week = "周一";
      break;
    case 2:
      week = "周二";
      break;
    case 3:
      week = "周三";
      break;
    case 4:
      week = "周四";
      break;
    case 5:
      week = "周五";
      break;
    case 6:
      week = "周六";
      break;
    case 0:
      week = "周日";
      break;
  }
  dateStr += week;
  return dateStr;
}

  <!-- 月表格，选中天会议详情列表 查看/修改 -->
  $('div.booking--list-content').on('click',function(e){
    showDialog(e || window.event);
  })


  <!-- 新建：新建按钮 -->
  $('.leftNav--topbar-newBtn').on('click',function(e){
    showDialog(e || window.event);
  })

  <?php
  // Turn the room and area selects into fancy select boxes and then
  // show the location menu (it's hidden to avoid screen jiggling).
  ?>
  //$('.room_area_select').mrbsSelect({
  //  minimumResultsForSearch: -1,
  //  width:200
  //});

  $('select.room_area_select').wrap('<div></div>').select2({ //标题
    width:200,
    minimumResultsForSearch: -1
  })

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
