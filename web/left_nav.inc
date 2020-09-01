<?php

use function MRBS\get_calendar_nav;
use function MRBS\get_location_nav;

$area_left_html = get_location_nav($view, $view_all, $year, $month, $day, $area, $room);

echo <<<EOF
<div class="leftNav--large">
    <div class="leftNav--topbar">
        <button type='button' class="leftNav--topbar-iconbtn" data-expanded="true">
            <span>
                <svg width="20" height="20" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </span>
        </button>
        <div class="leftNav--topbar-newBtn">
            <a class="new_booking" data-jumpurl="">新建会议</a>
        </div>
    </div>
    <div class="leftNav--DatepickerWrap">
        <div id="leftNavDatepicker"></div>
    </div>
     $area_left_html
</div>
<div class="leftNav--small">
    <div class="leftNav--small-topbar">
        <button type='button' class="leftNav--topbar-iconbtn" data-expanded="false">
            <span>
                <svg width="20" height="20" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </span>
        </button>
        <div class="leftNav--topbar-newBtn">
                <span>新建会议</span>
        </div>
    </div>
</div>
EOF;



