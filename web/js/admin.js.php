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

  var fixedColumnsOptions = {leftColumns: 1};

    $('select#area_select').wrap('<div></div>').select2({ //会议室
    width:150,
    minimumResultsForSearch: -1
  })

  <?php
  // Turn the list of rooms into a dataTable
  // If we're an admin, then fix the right hand column
  ?>
  if (args.isAdmin)
  {
    fixedColumnsOptions.rightColumns = 1;
  }

  makeDataTable('#rooms_table', {}, fixedColumnsOptions);

  $('.del_room_btn').click(function(){
    if(confirm('该操作会删除会议室内所有的会议，确定删除？')){
      $(this).parent('form').submit();
    }
  })
});

