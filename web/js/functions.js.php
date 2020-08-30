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


function isMobile()
{
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

$.fn.reverse = [].reverse;


jQuery.fn.extend({
  
  <?php
  // Turn a select element into a fancy Select2 field control.  This wrapper function also
  // (a) only does anything if we are not on a mobile device, because the native select
  //     elements on mobile devices tend to be better.
  // (b) wraps the select element in a <div> because in some places, eg in forms,  MRBS uses
  //     a table structure and because Select2 adds a sibling element the table structure is
  //     ruined.
  // (c) adjusts the width of the select2 container because Select2 doesn't always get it right
  //     resilting in a '...'
  ?>
  mrbsSelect: function(options) {
    if (!isMobile())
    {
      $(this).wrap('<div></div>')
             .select2(options)
             .next('.select2-container').each(function() {
                var container = $(this);
                container.width(container.width() + 5);
              });
    }
    return($(this));
  }
  
});


function getErrorList(errors)
{
  var result = {html: '', text: ''},
      patternSpan = /<span[\s\S]*span>/gi,
      patternTags = /<\S[^><]*>/g,
      str;
      
  result.html += "<ul>";
  
  for (var i=0; i<errors.length; i++)
  {
    result.html += "<li>" + errors[i] + "<\/li>";
    result.text += '(' + (i+1).toString() + ') ';
    <?php // strip out the <span> and its contents and then all other tags ?>
    str = errors[i].replace(patternSpan, '').replace(patternTags, '');
    <?php // undo the htmlspecialchars() ?>
    result.text += $('<div>').html(str).text();
    result.text += "  \n";
  }
  
  result.html += "<\/ul>";
  
  return result;
}


<?php
// Gets the correct prefix to use (if any) with the page visibility API.
// Returns null if not supported.
?>
var visibilityPrefix = function visibilityPrefix() {
    var prefixes = ['', 'webkit', 'moz', 'ms', 'o'];
    var testProperty;
    
    if (typeof visibilityPrefix.prefix === 'undefined')
    {
      visibilityPrefix.prefix = null;
      for (var i=0; i<prefixes.length; i++)
      {
        testProperty = prefixes[i];
        testProperty += (prefixes[i] === '') ? 'hidden' : 'Hidden';
        if (testProperty in document)
        {
          visibilityPrefix.prefix = prefixes[i];
          break;
        }
      }
    }

    return visibilityPrefix.prefix;
  };

<?php
// Determine if the page is hidden from the user (eg if it has been minimised
// or the tab is not visible).    Returns true, false or null (if not known).
?>
var isHidden = function isHidden() {
    var prefix;
    prefix = visibilityPrefix();
    switch (prefix)
    {
      case null:
        return null;
        break;
      case '':
        return document.hidden;
        break;
      default:
        return document[prefix + 'Hidden'];
        break;
    }
  };


<?php
// Thanks to Remy Sharp https://remysharp.com/2010/07/21/throttling-function-calls
?>
function throttle(fn, threshold, scope) {

  var last,
      deferTimer;
      
  threshold || (threshold = 250);
  
  return function () {
    var context = scope || this,
        now = +new Date(),
        args = arguments;
        
    if (last && now < last + threshold)
    {
      // hold on to it
      clearTimeout(deferTimer);
      deferTimer = setTimeout(function () {
          last = now;
          fn.apply(context, args);
        }, threshold);
    }
    else 
    {
      last = now;
      fn.apply(context, args);
    }
  };
}

<?php
// Tries to determine if the network connection is metered and subject to
// charges or throttling
?>
function isMeteredConnection()
{
  var connection = navigator.connection || 
                   navigator.mozConnection || 
                   navigator.webkitConnection ||
                   navigator.msConnection ||
                   null;
  
  if (connection === null)
  {
    return false;
  }
  
  if ('type' in connection)
  {
    <?php 
    // Although not all cellular networks will be metered, they
    // may be subject to throttling once a data threshold has passed.
    // It is probably sensible to assume that most users connected via
    // a cellular network will want to minimise data traffic.
    ?>
    return (connection.type === 'cellular');
  }
  
  <?php // The older version of the interface ?>
  if ('metered' in connection)
  {
    return connection.metered;
  }
  
  return false;
}


function getCSRFToken()
{
  return $('meta[name="csrf_token"]').attr('content');
}



function getParameterByName(name, url)
{
  if (!url)
  {
    url = window.location.href;
  }
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2])
  {
    return '';
  }
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

<?php
//父页面特殊dom，调起iframe
?>
function showDialog(e){
    var target = e.target;
      var $target = $(target);
      if(target.nodeName == "A" && $target.data('jumpurl') != ""){
        var jumpurl = $target.data('jumpurl');
        showIframe(jumpurl,510,550);
      }
}

<?php
//父页面关闭iframe方法，在iframe中调用
?>
function closeIFrame() {
    console.log("关闭子页面");
    $("#iFrameWrap").remove();
    $("#iFrameWrapBg").remove();
}

window.closeIFrame = closeIFrame;

<?php
//动态创建iframe
?>
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



(function($){

    /**
     * Copyright 2012, Digital Fusion
     * Licensed under the MIT license.
     * http://teamdf.com/jquery-plugins/license/
     *
     * @author Sam Sehnert
     * @desc A small plugin that checks whether elements are within
     *       the user visible viewport of a web browser.
     *       only accounts for vertical position, not horizontal.
     */
    var $w=$(window);
    $.fn.visible = function(partial,hidden,direction,container){

        if (this.length < 1)
            return;
	
	// Set direction default to 'both'.
	direction = direction || 'both';
	    
        var $t          = this.length > 1 ? this.eq(0) : this,
						isContained = typeof container !== 'undefined' && container !== null,
						$c				  = isContained ? $(container) : $w,
						wPosition        = isContained ? $c.position() : 0,
            t           = $t.get(0),
            vpWidth     = $c.outerWidth(),
            vpHeight    = $c.outerHeight(),
            clientSize  = hidden === true ? t.offsetWidth * t.offsetHeight : true;

        if (typeof t.getBoundingClientRect === 'function'){

            // Use this native browser method, if available.
            var rec = t.getBoundingClientRect(),
                tViz = isContained ?
												rec.top - wPosition.top >= 0 && rec.top < vpHeight + wPosition.top :
												rec.top >= 0 && rec.top < vpHeight,
                bViz = isContained ?
												rec.bottom - wPosition.top > 0 && rec.bottom <= vpHeight + wPosition.top :
												rec.bottom > 0 && rec.bottom <= vpHeight,
                lViz = isContained ?
												rec.left - wPosition.left >= 0 && rec.left < vpWidth + wPosition.left :
												rec.left >= 0 && rec.left <  vpWidth,
                rViz = isContained ?
												rec.right - wPosition.left > 0  && rec.right < vpWidth + wPosition.left  :
												rec.right > 0 && rec.right <= vpWidth,
                vVisible   = partial ? tViz || bViz : tViz && bViz,
                hVisible   = partial ? lViz || rViz : lViz && rViz,
		vVisible = (rec.top < 0 && rec.bottom > vpHeight) ? true : vVisible,
                hVisible = (rec.left < 0 && rec.right > vpWidth) ? true : hVisible;

            if(direction === 'both')
                return clientSize && vVisible && hVisible;
            else if(direction === 'vertical')
                return clientSize && vVisible;
            else if(direction === 'horizontal')
                return clientSize && hVisible;
        } else {

            var viewTop 				= isContained ? 0 : wPosition,
                viewBottom      = viewTop + vpHeight,
                viewLeft        = $c.scrollLeft(),
                viewRight       = viewLeft + vpWidth,
                position          = $t.position(),
                _top            = position.top,
                _bottom         = _top + $t.height(),
                _left           = position.left,
                _right          = _left + $t.width(),
                compareTop      = partial === true ? _bottom : _top,
                compareBottom   = partial === true ? _top : _bottom,
                compareLeft     = partial === true ? _right : _left,
                compareRight    = partial === true ? _left : _right;

            if(direction === 'both')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop)) && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
            else if(direction === 'vertical')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop));
            else if(direction === 'horizontal')
                return !!clientSize && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
        }
    };

})(jQuery);

