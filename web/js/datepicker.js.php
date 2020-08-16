<?php
namespace MRBS;

require "../defaultincludes.inc";

http_headers(array("Content-type: application/x-javascript"),60*30);

if ($use_strict)
{
  echo "'use strict';\n";
}

?>


$(document).on('page_ready', function() {

  //js date begin
  $('#leftNavDatepicker').datetimepicker({
    format: 'YYYY-MM-DD',
    locale: moment.locale('zh-cn'),
    keepOpen: true
  });

  $(".input-group-addon")[0].click();
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

