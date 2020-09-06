
/*--周日历--*/
function jcalendar_week(options){
    var _this=this;
    var startX = 0;
    var startY = 0;
    var value =  {
        year: '',
        month: '',
        date: '',
		weeknum:'',
		weekday:""
      };
      _this.event1 = null,
      _this.event2 =null,
      _this.event3 =null;
	var initFlag = true; 
	var defaults={
		element: "#calendar_tr",
		dayclick:function(date,obj){
			//day点击事件
            obj &&	$(obj).addClass("calendar_day_act").siblings().removeClass("calendar_day_act");
		},
		dayaddclass:function(date){
			return null;
		},
        showbtn:true,
        defaultValue: new Date()
	};
	var opts = $.extend(defaults,options);
	var calendarid = $(opts.element);
	function addDOM(){
        calendarid.html("");    
		var $html = '<ul class="calendar_tr calendar_day_bar" id="calendar_body"></ul>';
		calendarid.append($html);
	}
	addDOM();

	var getMonthCustom = function(m) {
		return ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'][m];
	}
	//获取今天
	var todaydate = function(date){
		var nstr = date ? new Date(date) : new Date();
		var ynow = nstr.getFullYear();
		var mnow = nstr.getMonth();
		var dnow = nstr.getDate();
		var wnow = nstr.getDay();
		return [ynow,mnow,dnow,wnow];
	}
	//判断是否为闰年
	var is_leap = function(year){
		return (year%100 == 0 ? res=(year%400 == 0 ? 1 : 0) : res=(year%4 == 0 ? 1 : 0));
	}
	//获取周第一天日期方法
	_this.weekfirstdate = function(year,weeknum){
		//获取当年月份天数数组
		var m_days=[31,28+is_leap(year),31,30,31,30,31,31,30,31,30,31];
		//获取当年第一天是周几
		var newyear_week=(new Date(year,0,1)).getDay();
		//新年到周第一天的总天数
		var week_day;
		if(newyear_week < 5){
			//新年第一天算年内第一周[周四在本年]
			week_day = 7*(weeknum-2)+(7-newyear_week+1);
		}else{
			//新年第一天是上年最后一周
			week_day = 7*(weeknum-1)+(7-newyear_week+1);
		}
		var startmouch;
		for(var i=0;i<m_days.length;i++){
			startmouch=i;
			if(week_day>m_days[i]){
				week_day-=m_days[i];
				if(i==m_days.length-1){
					year++;	
					startmouch=0;
				}
			}else{
				break;
			}
		}
		return [year,startmouch,week_day];
	}
	
	//设置周日历
	var setdaydata = function(year,weeknum){
		
		//获取月份天数数组
		var m_days=[31,28+is_leap(year),31,30,31,30,31,31,30,31,30,31];
		//获取周第一天日期
		var datastart=_this.weekfirstdate(year,weeknum);
		var flagDate = "";
		if(initFlag){
			flagDate = opts.defaultValue;
		}else{
			flagDate = datastart[0] + "-" +(datastart[1]+1) + "-" +datastart[2]; 
        }
        
        opts.dayclick(flagDate);

		var selectarr = todaydate(flagDate); 
		var todayarr = todaydate();
		console.log(datastart)
		//根据日期判断显示正确的数据（比如传入的值比总周数大）
		var trueweeknum = _this.getweeknum(datastart[0],datastart[1],datastart[2]);
		// calendarid.attr({
		// 	"year":trueweeknum[0],
		// 	"weeknum":trueweeknum[1]
		// })
	

		var monthTag = $('.md_selectarea').find('.monthtag');
		monthTag.text(getMonthCustom(selectarr[1])+ '月')

		// calendarid.find(".calendar_info").html(trueweeknum[1]+'周/'+ (selectarr[1]+1)+ '/月'+trueweeknum[0]);
		
		var week_day = "";
		var isdayaddclass = false;
		if(opts.dayaddclass() !== null){
			isdayaddclass = true;
		}
		var dayaddclass="";
		var newdate;
		for(var i = 0; i < 7; i++){
			newdate = new Date(datastart[0],datastart[1],datastart[2]+i);
			if(isdayaddclass){
				dayaddclass=" "+opts.dayaddclass(newdate.getFullYear()+'-'+(newdate.getMonth()+1)+'-'+newdate.getDate());
			}
            var istoday = '';
			var istodayClass = "";
			
			//给选中天active
			// var selectarr = todaydate(flagDate); 
			if(newdate.getFullYear()==selectarr[0] && newdate.getMonth()==selectarr[1] && newdate.getDate()==selectarr[2]){
                istodayClass = "calendar_day_act";
			}
			//标示今天
			// var todayarr = todaydate();
			if(newdate.getFullYear()==todayarr[0] && newdate.getMonth()==todayarr[1] && newdate.getDate()==todayarr[2]){
                istoday = '<span class="today_i">今天</span>';
			}
			week_day += '<li class="calendar_day ' + dayaddclass + istodayClass + '" '+
					'date="' + newdate.getFullYear() + '-' + (newdate.getMonth()+1) + '-' + newdate.getDate() + '">' +
				'<span class="calendar_day_i">'+newdate.getDate()+'</span>'+istoday+
			'</li>';
		}
		newdate = null;
		$(".calendar_day_bar").html(week_day);
	}

	//传入日期为当年第几周
	_this.getweeknum = function(year,month,day){
		//获取月份天数数组
		var m_days = [31,28+is_leap(year),31,30,31,30,31,31,30,31,30,31];

		var newtonowday = 0;
		for(var i = 0; i < month; i++){
			newtonowday += m_days[i];
		}
		newtonowday += day;
		//获取当年第一天是周几
		var newyear_week = (new Date(year,0,1)).getDay();
		var fdaynothisy = false;
		//新年到周第一天的总天数
		if(newyear_week < 5){
			//新年第一天算年内第一周[周四在本年]
			newtonowday += newyear_week;
			if(newyear_week == 0 && m_days[2] == 29){
				fdaynothisy = true;
			}
		}else{
			//新年第一天是上年最后一周
			fdaynothisy = true;
			newtonowday -= (7-newyear_week);
		}
		var weeknum_result = Math.ceil(newtonowday/7);
		var weekyear = year;
		if(weeknum_result == 0){
			var beforeyear_fweek = (new Date(weekyear-1,0,1)).getDay();
			if(beforeyear_fweek < 5 && beforeyear_fweek > 1 && fdaynothisy){
				weeknum_result = 53;
			}else{
				weeknum_result = 52;
			}
			weekyear--;
		}else if(weeknum_result > 52){
			var year_lweek=(new Date(year,11,31)).getDay();
			if(year_lweek > 3 && newyear_week < 5){
				weeknum_result=53;
			}else{
				weekyear++;
				weeknum_result=1;
			}
		}
		return [weekyear,weeknum_result];
	}
	
	//DOM添加
	_this.confirmweek = function(year,weeknum){

		if(weeknum < 1) weeknum = 1;
		
		setdaydata(year,weeknum);
		
		// //上一周
		// calendarid.find(".beforem_btn").off("click").on("click",function(){
		// 	initFlag = false;
		// 	var beforew=weeknum-1;
		// 	var beforeweekfirst=_this.weekfirstdate(year,beforew);
		// 	var beforeweekdata=_this.getweeknum(beforeweekfirst[0],beforeweekfirst[1],beforeweekfirst[2]);
        //     _this.confirmweek(beforeweekdata[0],beforeweekdata[1]);
        //     value.year = beforeweekdata[0];
        //     value.weeknum = beforeweekdata[1];
		// })
		// //下一周
		// calendarid.find(".afterm_btn").off("click").on("click",function(){
		// 	initFlag = false;
		// 	var afterw=weeknum+1;
		// 	var afterweekfirst=_this.weekfirstdate(year,afterw);
		// 	var afterweekdata=_this.getweeknum(afterweekfirst[0],afterweekfirst[1],afterweekfirst[2]);
        //     _this.confirmweek(afterweekdata[0],afterweekdata[1]);
        //     value.year = afterweekdata[0];
        //     value.weeknum = afterweekdata[1];
		// })
		// //day点击事件
		// calendarid.find(".calendar_day").on("click",function(){
		// 	var obj=$(this);
		// 	opts.dayclick(obj.attr("date"),this);
		// })
	}
	//本周
	_this.nowweek = function(){
		var todayarr = todaydate(opts.defaultValue);
		var weekdata = _this.getweeknum(todayarr[0], todayarr[1], todayarr[2]);
        value.year = weekdata[0];
		value.weeknum = weekdata[1];
		value.month = todayarr[1];
		value.weekday = todayarr[3];
		value.date = todayarr[0]+"-"+(todayarr[1]+1)+"-"+todayarr[2]
		console.log(value);
		_this.confirmweek(weekdata[0],weekdata[1]);
	}
    _this.nowweek();

    _this.GetSlideAngle = function(dx,dy) {
        return Math.atan2(dy,dx) * 180 / Math.PI;
    },

    //根据起点和终点返回方向 1：向上，2：向下，3：向左，4：向右,0：未滑动

    _this.GetSlideDirection = function(startX,startY, endX, endY) {
        var dy = startY - endY;
        var dx = endX - startX;
        var result = 0;
        //如果滑动距离太短
        if (Math.abs(dx) < 10 && Math.abs(dy) < 10) {
           return result;
        }
        var angle = _this.GetSlideAngle(dx, dy);
        if (angle >= -45 && angle < 45) {
           result = 4;
        }else if (angle >= 45 && angle < 135) {
           result = 1;
        }else if (angle >= -135 && angle < -45) {
           result = 2;
        }else if ((angle >= 135 && angle <= 180) || (angle >= -180 && angle < -135)) {
           result = 3;
        }
        return result;
    }
    _this._removeEvent = function(){
        var _this = this;

        document.getElementById("md_datearea").removeEventListener('touchstart',_this.event1);
        document.getElementById("md_datearea").removeEventListener('touchend',_this.event2);
        document.getElementById("md_datearea").removeEventListener('touchmove',_this.event3);
    }
    _this.funMoveEvent = function(){
        var _this = this;
        document.getElementById("calendar_body").addEventListener('touchstart', _this.event1 = function (ev){
            ev.preventDefault();
            startX = ev.touches[0].pageX;
            startY = ev.touches[0].pageY; 
        }, false);
        document.getElementById("calendar_body").addEventListener('touchend',  _this.event2 = function (ev){
            var endX, endY;
            ev.preventDefault();
            endX = ev.changedTouches[0].pageX;
            endY = ev.changedTouches[0].pageY;
            var direction = _this.GetSlideDirection(startX, startY, endX, endY);
    
            switch (direction){
                case 0:
				console.log("没滑动");
				if(ev.target.nodeName == "SPAN"){
					var parent = $(ev.target).parent();
					var date = parent.attr('date');
                    value.date = date;
					console.log(date);
					var monthTag = $('.md_selectarea').find('.monthtag');
					var selectarr = todaydate(date); 
					monthTag.text(getMonthCustom(selectarr[1])+ '月')
					opts.dayclick(date,parent);
				}
           
                break;
                case 1:
                console.log("向上");
                break;
                case 2:
                console.log("向下");
                break;
                case 3:
                console.log("向左");
				initFlag = false;
                var afterw = value.weeknum + 1;
                var afterweekfirst=_this.weekfirstdate(value.year, afterw);
                var afterweekdata=_this.getweeknum(afterweekfirst[0],afterweekfirst[1],afterweekfirst[2]);
                _this.confirmweek(afterweekdata[0],afterweekdata[1]);
                value.year = afterweekdata[0];
                value.weeknum = afterweekdata[1];
                break;
                case 4:
                console.log("向右");
				initFlag = false;
                var beforew = value.weeknum - 1;
                var beforeweekfirst = _this.weekfirstdate(value.year, beforew);
                var beforeweekdata = _this.getweeknum(beforeweekfirst[0],beforeweekfirst[1],beforeweekfirst[2]);
                _this.confirmweek(beforeweekdata[0],beforeweekdata[1]);
                value.year = beforeweekdata[0];
                value.weeknum = beforeweekdata[1];
                break;
                default:          
            } 
    

        }, false);

        document.getElementById("calendar_body").addEventListener('touchmove', _this.event3 = function (ev){
            var endX, endY;
            // ev.preventDefault();
            endX = ev.changedTouches[0].pageX;
            endY = ev.changedTouches[0].pageY;

            var direction = _this.GetSlideDirection(startX, startY, endX, endY);
    
            switch (direction){
                case 0:
            
                console.log("没滑动");
                break;
                case 1:
                console.log("向上");
                break;
                case 2:
                console.log("向下");
                break;
                case 3:
                console.log("向左");
                break;
                case 4:
                console.log("向右");
                break;
                default:          
                } 
            }, false);
    }
    _this.funMoveEvent();
}