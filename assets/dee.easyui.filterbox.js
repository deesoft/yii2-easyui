/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


(function($){
    function createBox(target){
        var state = $.data(target, 'filterbox');
		var opts = state.options;
		
		$(target).addClass('filterbox-f').combo($.extend({}, opts, {
			onShowPanel:function(){
				bindEvents(this);
				setButtons(this);
				setCalendar(this);
				setValue(this, $(this).filterbox('getText'), true);
				opts.onShowPanel.call(this);
			}
		}));
		
		/**
		 * if the calendar isn't created, create it.
		 */
		if (!state.filterform){
			var panel = $(target).combo('panel').css('overflow','hidden');
			var cc = $('<div class="filterbox-form-inner"></div>').prependTo(panel);
			var $filterform = $('<form>');
			$.each(opts.filterOptions,function(){
                var $div = $('<div>').addClass('row').appendTo($filterform);
                $div.append($('<label>'))
            });

			$.extend(state.calendar.calendar('options'), {
				fit:true,
				border:false,
				onSelect:function(date){
					var target = this.target;
					var opts = $(target).filterbox('options');
					setValue(target, opts.formatter.call(target, date));
					$(target).combo('hidePanel');
					opts.onSelect.call(target, date);
				}
			});
		}

		$(target).combo('textbox').parent().addClass('filterbox');
		$(target).filterbox('initValue', opts.value);
		
		function bindEvents(target){
			var opts = $(target).filterbox('options');
			var panel = $(target).combo('panel');
			panel.unbind('.filterbox').bind('click.filterbox', function(e){
				if ($(e.target).hasClass('filterbox-button-a')){
					var index = parseInt($(e.target).attr('filterbox-button-index'));
					opts.buttons[index].handler.call(e.target, target);
				}
			});
		}
		function setButtons(target){
			var panel = $(target).combo('panel');
			if (panel.children('div.filterbox-button').length){return}
			var button = $('<div class="filterbox-button"><table cellspacing="0" cellpadding="0" style="width:100%"><tr></tr></table></div>').appendTo(panel);
			var tr = button.find('tr');
			for(var i=0; i<opts.buttons.length; i++){
				var td = $('<td></td>').appendTo(tr);
				var btn = opts.buttons[i];
				var t = $('<a class="filterbox-button-a" href="javascript:void(0)"></a>').html($.isFunction(btn.text) ? btn.text(target) : btn.text).appendTo(td);
				t.attr('filterbox-button-index', i);
			}
			tr.find('td').css('width', (100/opts.buttons.length)+'%');
		}
		function setCalendar(target){
			var panel = $(target).combo('panel');
			var cc = panel.children('div.filterbox-calendar-inner');
			panel.children()._outerWidth(panel.width());
			state.calendar.appendTo(cc);
			state.calendar[0].target = target;
			if (opts.panelHeight != 'auto'){
				var height = panel.height();
				panel.children().not(cc).each(function(){
					height -= $(this).outerHeight();
				});
				cc._outerHeight(height);
			}
			state.calendar.calendar('resize');
		}
    }
    
    $.fn.filterbox = function(options, param){
		if (typeof options == 'string'){
			var method = $.fn.filterbox.methods[options];
			if (method){
				return method(this, param);
			} else {
				return this.combo(options, param);
			}
		}
		
		options = options || {};
		return this.each(function(){
			var state = $.data(this, 'filterbox');
			if (state){
				$.extend(state.options, options);
			} else {
				$.data(this, 'filterbox', {
					options: $.extend({}, $.fn.filterbox.defaults, $.fn.filterbox.parseOptions(this), options)
				});
			}
			createBox(this);
		});
	};
    
    $.fn.filterbox.methods = {
        
    };
    
    
	
	$.fn.filterbox.parseOptions = function(target){
		return $.extend({}, $.fn.combo.parseOptions(target));
	};
	
	$.fn.filterbox.defaults = $.extend({}, $.fn.combo.defaults, {
		panelWidth:180,
		panelHeight:'auto',
		sharedCalendar:null,
		
		keyHandler: {
			up:function(e){},
			down:function(e){},
			left: function(e){},
			right: function(e){},
			enter:function(e){doEnter(this)},
			query:function(q,e){doQuery(this, q)}
		},
		
		currentText:'Today',
		closeText:'Close',
		okText:'Ok',
		
		buttons:[{
			text: function(target){return $(target).filterbox('options').currentText;},
			handler: function(target){
				$(target).filterbox('calendar').calendar({
					year:new Date().getFullYear(),
					month:new Date().getMonth()+1,
					current:new Date()
				});
				doEnter(target);
			}
		},{
			text: function(target){return $(target).filterbox('options').closeText;},
			handler: function(target){
				$(this).closest('div.combo-panel').panel('close');
			}
		}],
		
		formatter:function(date){
			var y = date.getFullYear();
			var m = date.getMonth()+1;
			var d = date.getDate();
			return (m<10?('0'+m):m)+'/'+(d<10?('0'+d):d)+'/'+y;
		},
		parser:function(s){
			if (!s) return new Date();
			var ss = s.split('/');
			var m = parseInt(ss[0],10);
			var d = parseInt(ss[1],10);
			var y = parseInt(ss[2],10);
			if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
				return new Date(y,m-1,d);
			} else {
				return new Date();
			}
		},
		
		onSelect:function(date){}
	});
})(jQuery)