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


});

