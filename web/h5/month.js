function mdate(config) {
      var defaults = {
        maxDate: null,
        defaultValue: new Date(),
        minDate: new Date(1970, 0, 1),
        ele: document.body,
        display: false,
        dayclick:function(date){
          console.log(date);
        }
      };
      var option = $.extend(defaults, config);
      var $ele = this;
  
      //通用函数
      var F = {
        //计算某年某月有多少天
        getDaysInMonth: function(year, month) {
          return new Date(year, month + 1, 0).getDate();
        },
        //计算某月1号是星期几
        getWeekInMonth: function(year, month) {
          return new Date(year, month, 1).getDay();
        },
        getMonth: function(m) {
          return ['一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二'][m];
        },
        //计算年某月的最后一天日期
        getLastDayInMonth: function(year, month) {
          return new Date(year, month, this.getDaysInMonth(year, month));
        },
        GetSlideAngle:function(dx,dy) {
            return Math.atan2(dy,dx) * 180 / Math.PI;
        },

        //根据起点和终点返回方向 1：向上，2：向下，3：向左，4：向右,0：未滑动

        GetSlideDirection: function(startX,startY, endX, endY) {
            var dy = startY - endY;
            var dx = endX - startX;
            var result = 0;
            //如果滑动距离太短
            if (Math.abs(dx) < 10 && Math.abs(dy) < 10) {
               return result;
            }
            var angle = this.GetSlideAngle(dx, dy);
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
  
  
      }
  
      //为$扩展一个方法，以配置的方式代理事件
      $.fn.delegates = function(configs) {
        el = $(this[0]);
        for (var name in configs) {
          var value = configs[name];
          if (typeof value == 'function') {
            var obj = {};
            obj.click = value;
            value = obj;
          };
          for (var type in value) {
            el.delegate(name, type, value[type]);
          }
        }
        return this;
      }
  
      this.mdater = {
        value: {
          year: '',
          month: '',
          date: ''
        },
        lastCheckedDate: '',
        startX: 0, 
        startY: 0,
        event1:null,
        event2:null,
        event3:null,
        init: function() {
          this.initListeners();
        },
        renderHTML: function() {
            var $html = $(
                
                      '<ul class="md_datearea in"></ul>'
                    
             
              );
              $("#md_datearea").append($html);
  
            this._funMoveEvent();
  
  
            return $html;
          },

        _funClick: function(ele) {
            var _this = this;
            var $this = ele.parent();
            if ($this.hasClass('disabled')) {
              return;
            }
            _this.value.date = $this.data('day');
            //判断是否点击的是前一月或后一月的日期
            var add = 0;
            if ($this.hasClass('nextdate')) {
              add = 1;
            } else if ($this.hasClass('prevdate')) {
              add = -1;
            }
            if (add !== 0) {
              _this._changeMonth(add, _this.value.date);
            } else {
              $this.addClass('current').siblings('.current').removeClass('current');
              _this.onChange();
            }
          },
        _funMoveEvent: function(){
            var _this = this;

            document.getElementById("md_datearea").addEventListener('touchstart', _this.event1=function (ev){
                ev.preventDefault();
                this.startX = ev.touches[0].pageX;
                this.startY = ev.touches[0].pageY; 
            }, false);
            document.getElementById("md_datearea").addEventListener('touchend', _this.event2=function (ev){
                var endX, endY;
                ev.preventDefault();
                endX = ev.changedTouches[0].pageX;
                endY = ev.changedTouches[0].pageY;
                var direction = F.GetSlideDirection(this.startX, this.startY, endX, endY);
        
                switch (direction){
                    case 0:
                    console.log("没滑动");
                    _this._funClick($(ev.target));
                    break;
                    case 1:
                    console.log("向上");
                    break;
                    case 2:
                    console.log("向下");
                    break;
                    case 3:
                    console.log("向左");
                    _this._changeMonth(1);
                    break;
                    case 4:
                    console.log("向右");
                    _this._changeMonth(-1);
                    break;
                    default:          
                } 
        

            }, false);
    
            document.getElementById("md_datearea").addEventListener('touchmove', _this.event3=function (ev){
                var endX, endY;
                // ev.preventDefault();
                endX = ev.changedTouches[0].pageX;
                endY = ev.changedTouches[0].pageY;
    
                var direction = F.GetSlideDirection(this.startX, this.startY, endX, endY);
        
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

        },
        _removeEvent: function(){
            var _this = this;

            document.getElementById("md_datearea").removeEventListener('touchstart',_this.event1);
            document.getElementById("md_datearea").removeEventListener('touchend',_this.event2);
            document.getElementById("md_datearea").removeEventListener('touchmove',_this.event3);
        },
        _showPanel: function(container) {
          this.refreshView();
          $('.md_panel').addClass('show');
        },
        _hidePanel: function() {
          $('.md_panel').remove();
        },
        _changeMonth: function(add, checkDate) {
  
          //先把已选择的日期保存下来
          this.saveCheckedDate();
  
          var monthTag = $('.md_selectarea').find('.monthtag'),
            num = ~~monthTag.data('month') + add;
          //月份变动发生了跨年
          if (num > 11) {
            num = 0;
            this.value.year++;
            $('.yeartag').text(this.value.year).data('year', this.value.year);
          } else if (num < 0) {
            num = 11;
            this.value.year--;
            $('.yeartag').text(this.value.year).data('year', this.value.year);
          }
  
          var nextMonth = F.getMonth(num) + '月';
          monthTag.text(nextMonth).data('month', num);
          this.value.month = num;
          if (checkDate) {
            this.value.date = checkDate;

            //点击了上一月或者下一月的日期，日历会切换，此时也将数据同步出去
            var date = this.value.year + '-' + (this.value.month+1) + '-' + this.value.date;
            option.dayclick(date);
            
          } else {
            //如果有上次选择的数据，则进行赋值
            this.setCheckedDate();
          }
          this.updateDate(add);
        },
        _changeYear: function(add) {
          //先把已选择的日期保存下来
          this.saveCheckedDate();
  
          var yearTag = $('.md_selectarea').find('.yeartag'),
            num = ~~yearTag.data('year') + add;
          yearTag.text(num + '年').data('year', num);
          this.value.year = num;
  
          this.setCheckedDate();
  
          this.updateDate(add);
        },
        //保存上一次选择的数据
        saveCheckedDate: function() {
          if (this.value.date) {
            this.lastCheckedDate = {
              year: this.value.year,
              month: this.value.month,
              date: this.value.date
            }
          }
        },
        //将上一次保存的数据恢复到界面
        setCheckedDate: function() {
          if (this.lastCheckedDate && this.lastCheckedDate.year == this.value.year && this.lastCheckedDate.month == this.value.month) {
            this.value.date = this.lastCheckedDate.date;
          } else {
            this.value.date = '';
          }
        },
        //根据日期得到渲染天数的显示的HTML字符串
        getDateStr: function(y, m, d) {
          var dayStr = '';
          //计算1号是星期几，并补上上个月的末尾几天
          var week = F.getWeekInMonth(y, m);
          var lastMonthDays = F.getDaysInMonth(y, m - 1);
          for (var j = week - 1; j >= 0; j--) {
            dayStr += '<li class="prevdate" data-day="' + (lastMonthDays - j) + '"><span>' + (lastMonthDays - j) + '</span></li>';
          }
          //再补上本月的所有天;
          var currentMonthDays = F.getDaysInMonth(y, m);
          //判断是否超出允许的日期范围
          var startDay = 1,
            endDay = currentMonthDays,
            thisDate = new Date(y, m, d),
            firstDate = new Date(y, m, 1);
          lastDate = new Date(y, m, currentMonthDays),
            minDateDay = option.minDate.getDate();
  
  
          if (option.minDate > lastDate) {
            startDay = currentMonthDays + 1;
          } else if (option.minDate >= firstDate && option.minDate <= lastDate) {
            startDay = minDateDay;
          }
  
          if (option.maxDate) {
            var maxDateDay = option.maxDate.getDate();
            if (option.maxDate < firstDate) {
              endDay = startDay - 1;
            } else if (option.maxDate >= firstDate && option.maxDate <= lastDate) {
              endDay = maxDateDay;
            }
          }
  
  
          //将日期按允许的范围分三段拼接
          for (var i = 1; i < startDay; i++) {
            dayStr += '<li class="disabled" data-day="' + i + '"><span>' + i + '</span></li>';
          }
          for (var j = startDay; j <= endDay; j++) {
            var current = '';
            if (y == this.value.year && m == this.value.month && d == j) {
              current = 'current';
            }
            dayStr += '<li class="' + current + '" data-day="' + j + '"><span>' + j + '</span></li>';
          }
          for (var k = endDay + 1; k <= currentMonthDays; k++) {
            dayStr += '<li class="disabled" data-day="' + k + '"><span>' + k + '</span></li>';
          }
  
          //再补上下个月的开始几天
          var nextMonthStartWeek = (currentMonthDays + week) % 7;
          if (nextMonthStartWeek !== 0) {
            for (var i = 1; i <= 7 - nextMonthStartWeek; i++) {
              dayStr += '<li class="nextdate" data-day="' + i + '"><span>' + i + '</span></li>';
            }
          }

          //动态计算md_body的高度
        var weekLine = Math.ceil((currentMonthDays + week) / 7) + 1;
          $('#md_body').css({
              height: weekLine*43
          })

          return dayStr;
        },
        updateDate: function(add) {
          var dateArea = $('.md_datearea.in');
          if (add == 1) {
            var c1 = 'out_left';
            var c2 = 'out_right';
          } else {
            var c1 = 'out_right';
            var c2 = 'out_left';
          }
          var newDateArea = $('<ul  class="md_datearea ' + c2 + '"></ul>');
          newDateArea.html(this.getDateStr(this.value.year, this.value.month, this.value.date));
          $('#md_datearea').append(newDateArea);

          setTimeout(function() {
            newDateArea.removeClass(c2).addClass('in');
            dateArea.removeClass('in').addClass(c1);
          }, 0);
          setTimeout(function(){
              $('.out_right').remove();
              $('.out_left').remove();
          },500)
  
        },
        //每次调出panel前，对界面进行重置
        refreshView: function() {
          var date = option.defaultValue ?  new Date(option.defaultValue) : new Date();
          var y = this.value.year = date.getFullYear(),
            m = this.value.month = date.getMonth(),
            d = this.value.date = date.getDate();
          $('.yeartag').text(y).data('year', y);
          $('.monthtag').text(F.getMonth(m) + '月').data('month', m);
          var dayStr = this.getDateStr(y, m, d);
          $('.md_datearea').html(dayStr);
        },
        $ele: null, //暂存当前指向input
        initListeners: function() {
          var _this = this;
          _this.renderHTML();
          var panel = $('.md_panel');
          _this.afterShowPanel(panel);
          setTimeout(function() {
            _this._showPanel();
          }, 50);
        },
        onChange: function() {
            var _this = this;
            var monthValue = ~~_this.value.month + 1;
            if (monthValue < 10) {
                monthValue = '0' + monthValue;
            }
            var dateValue = _this.value.date;
            if (dateValue === '') {
                dateValue = _this.value.date = 1;
            }
            if (dateValue < 10) {
                dateValue = '0' + dateValue;
            }

            var date = _this.value.year + '-' + monthValue + '-' + dateValue
          option.dayclick(date);
            // _this._hidePanel();
            console.log(date)
            return date
        },
        afterShowPanel: function(panel) {
          var _this = this;

          panel.delegates({
            // '.change_month': function() {
            //   var add = $(this).hasClass('md_next') ? 1 : -1;
            //   _this._changeMonth(add);
            // },
            // '.change_year': function() {
            //   var add = $(this).hasClass('md_next') ? 1 : -1;
            //   _this._changeYear(add);
            // },
            // '.out_left, .out_right': {
            //   'webkitTransitionEnd': function() {
            //     $(this).remove();
            //   },
            //   'msTransitionEnd':function(){
            //     $(this).remove();
            //   },
            //   'oTransitionEnd':function(){
            //     $(this).remove();
            //   },
            //   'otransitionend':function(){
            //     $(this).remove();
            //   },
            //   'transitionend':function(){
            //     $(this).remove();
            //   }
            // },
            '.md_datearea li': function() {
              var $this = $(this);
              if ($this.hasClass('disabled')) {
                return;
              }
              _this.value.date = $this.data('day');
              //判断是否点击的是前一月或后一月的日期
              var add = 0;
              if ($this.hasClass('nextdate')) {
                add = 1;
              } else if ($this.hasClass('prevdate')) {
                add = -1;
              }
              if (add !== 0) {
                _this._changeMonth(add, _this.value.date);
              } else {
                $this.addClass('current').siblings('.current').removeClass('current');
                _this.onChange();
              }
            },
            // '.md_cancel': function() {
            //     _this._hidePanel();
            //   },
          });
        }
      }
      this.mdater.init();
    }
