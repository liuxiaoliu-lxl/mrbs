



var areaId, roomId;
var month, week, changeDate = "";

const times = [
            "00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30",
            "04:00", "04:30", "05:00", "05:30", "06:00", "06:30", "07:00", "07:30", 
            "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", 
            "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", 
            "16:00", "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", 
            "20:00", "20:30", "21:00", "21:30", "22:00", "22:30", "23:00", "23:30"
];
var roomData = {}; //房间数据
var $roomArea = $("#room_area");
var $roomOffic = $("#room_office");
var $table = $('#day_main');
var $mdCancel = $('.md_cancel');


function getTodayDate(){
    let date = new Date();
    let year = date.getFullYear();
    let month = date.getMonth() + 1;
    let day = date.getDate();
    return year + "-" + month + "-" + day; 
}

    //请求数据
    function getData(url) {
        return new Promise(function(resolve, reject){
            $.ajax({
                url:url,
                dataType:"json",
                success:function(res){
                    if(res.code == 0){
                        resolve(res.data || [])
                    }
                }
            })
        })
    };
    //渲染区域
    function renderAreaSelectDom(ele, data){
        if(data && data.length > 0){
            ele.select2({
                data:data,
                width: 120,
                minimumResultsForSearch: -1
            })
        }
    }
    //渲染房间，并在房间信息里面获取该房间选中日期的会议数据
    async function renderRoomSelectDom(ele, data){
        if(data && data.length > 0){ //有会议室
            ele.select2({
                data:data,
                width: 120,
                minimumResultsForSearch: -1
            })
            //取第一个会议室的会议数据
            roomId = data[0].id;
            let date = changeDate || getTodayDate();
            let url = "http://192.168.1.106:8888/web/entry_api.php?action=entryss&date="+ date + "&room="+roomId
            let eventList = await getData(url);
            renderEventListDom($table, eventList);
        }else{//没有会议室
            ele.next().css({
                display:"none"
            })
            let domStr = "<tbody><tr><td>没有会议室<td></tr></tbody>";
            let dom = $(domStr);
            $table.find("tbody").replaceWith(dom);
        }
    }

    //渲染会议列表
    function renderEventListDom(ele, data){

        let domStr = "<tbody>";
        let index = 0;
        for(let i = 0; i < data.length; i++){
            let tempData = data[i];
            for(let j = 0; j < tempData.count; j++){
                
                if(tempData.type == 1){
                    if(j == 0){
                        domStr += "<tr><th>" + times[index] + "</th><td class='booked' rowspan=" + tempData.count + ">"+ tempData.data.name +"</td></tr>"
                    }else{
                        domStr += "<tr><th>" + times[index] + "</th>1</tr>"
                    }
                }else if(tempData.type == -1){
                    domStr += "<tr><th>" + times[index] + "</th><td></td></tr>"
                }
                index++;
            }
        }
        domStr += "</tbody>";
        let dom = $(domStr);
        ele.find("tbody").replaceWith(dom);
    }
    
    async function initPage(){
        let areaDate = await getData('http://192.168.1.106:8888/web/entry_api.php?action=areas');
        areaId = areaDate[0].id;
        let roomData = await getData('http://192.168.1.106:8888/web/entry_api.php?action=rooms&area='+areaId);
        return [areaDate, roomData]
    }

    function dateChange(){
        let date = changeDate || getTodayDate();
        var url = 'http://192.168.1.106:8888/web/entry_api.php?action=entryss&date='+ date +'&room=' + roomId;
        getData(url).then((data)=>{
            renderEventListDom($table, data);
        })
    }

    //获取区域和房间信息
    initPage().then((data)=>{
        renderAreaSelectDom($roomArea, data[0]);
        renderRoomSelectDom($roomOffic, data[1]);
    })

    //区域change事件
	$roomArea.on("select2:select",function(){
        areaId = $(this).val();
        $roomOffic.empty();
        var url = 'http://192.168.1.106:8888/web/entry_api.php?action=rooms&area=' + areaId;
        getData(url).then((data)=>{
            renderRoomSelectDom($roomOffic, data);
        })
    });
    //房间change事件
    $roomOffic.on("select2:select",function(){
        roomId = $(this).val();
        let date = changeDate || getTodayDate();
        var url = 'http://192.168.1.106:8888/web/entry_api.php?action=entryss&date='+ date +'&room=' + roomId;
        getData(url).then((data)=>{
            renderEventListDom($table, data);
        })
    });

    


     //调用周日历
    week = new jcalendar_week({
        dayclick: function (date, obj) {
            //day点击事件
            changeDate = date;
            dateChange();
            $(obj).addClass("calendar_day_act").siblings().removeClass("calendar_day_act");
        }
    });

    $mdCancel.on('click', function () {
        if ($(this).hasClass("week")) {
            $(".calendar_tr").remove();
            week._removeEvent();
            month = new mdate({
                defaultValue: changeDate,
                dayclick: function (date) {
                    //day点击事件
                    changeDate = date;
                    dateChange();
                }
            });
            $(".md_datearea").show();
            $(this).addClass("month").removeClass("week");
        } else {
            $(".md_datearea").remove();
            month.mdater._removeEvent();
            week = new jcalendar_week({
                defaultValue: changeDate,
                dayclick: function (date, obj) {
                    //day点击事件
                    changeDate = date;
                    dateChange();
                    $(obj).addClass("calendar_day_act").siblings().removeClass("calendar_day_act");
                }
            });
            $(".calendar_tr").show(400);
            $(".md_body").css({ height: 86 });
            $(this).addClass("week").removeClass("month");
        }
    })
 