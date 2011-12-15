$.fn.slidernav = function(options) {
	var defaults = { items: ["a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"]};
	var opts = $.extend(defaults, options);
    var o = $.meta ? $.extend({}, opts, $$.data()) : opts;
    var slider = $(this); $(slider).addClass('slidernav').disableSelection();
	$('.items li:first .title', slider).addClass('ui-state-active');
	$(slider).addClass('ui-widget-content ui-corner-all').append('<div class="slider-nav"><ul></ul></div>').find('.title').addClass('ui-state-default');
    $(slider).find('.prev').addClass('ui-widget-header');
    $(slider).find('.next').addClass('ui-widget-header');
    var i = 0;
	for(i=0;i<o.items.length;i++) {
        $('.slider-nav ul', slider).append("<li><a rel='#"+o.items[i]+"'>"+o.items[i]+"</a></li>");
    }
	$('.scrollable', slider).css('height',$('.slider-nav', slider).height());
    $(slider).find('.scrollable').scrollable({
        vertical: true,
        mousewheel: true,
        easing: 'easeOutExpo',
        next: '',
        prev: '',
        onBeforeSeek: function(event, index){
            $('.scrollable li', slider).find('.title').removeClass('ui-state-active');
            $($('.item:eq('+index+')', slider)).find('.title').addClass('ui-state-active');
        }
    });
	$('.slider-nav a', slider).hoverIntent(function(event){
        $('.scrollable', slider).data("scrollable").seekTo($('.scrollable '+$(this).attr('rel'), slider).index());
	}, function(){});
    
    $("a.prev", slider).mousedown(function(){scrollUp(slider);})
    .bind('mouseup mouseleave', function(){clearTimeout(scrolling);});

    $("a.next", slider).mousedown(function(){scrollDown(slider);})
    .bind('mouseup mouseout', function(){clearTimeout(scrolling);});
    
    var scrolling;
    function scrollUp(slider) {
        var topVal = parseInt($(".items", slider).css("top").replace("px", ""));
        topVal = topVal? topVal : 0;
        if (topVal < 0) {
            $(".items", slider).css("top", topVal+10);
            scrolling = setTimeout(function(){scrollUp(slider);}, 50);
        }
    }
    
    function scrollDown(slider) {
        var topVal = parseInt($(".items", slider).css("top").replace("px", ""));
        topVal = topVal? topVal : 0;
        if ($(".items", slider).height()+topVal+$(slider).height()-$(slider).height()*2+40 > 0) {
            $(".items", slider).css("top", topVal-10);
            scrolling = setTimeout(function(){scrollDown(slider);}, 50);
        }
    }

};