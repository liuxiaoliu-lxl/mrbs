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

  $('select#new_area').wrap('<div></div>').select2({// 开始时间段
    width:130,
    minimumResultsForSearch: -1
  })

});

