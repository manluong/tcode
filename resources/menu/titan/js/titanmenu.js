/**
  * Name: Titan Menu
  * Date: May 2011
  * Autor: CreativeMilk
  * Version: 1.2
  * Lib: 1.6.1
  * Licence: NOT free
  * This is an CodeCanyon item
  * http://codecanyon.net/item/titan-menu-a-fresh-and-powerfull-dropdown-menu/258696?ref=CreativeMilk
  **/

(function(jQuery){
    jQuery.fn.titanMenu = function(opt) { 
	 
	        // default settings(dont change) 
			var defaults = {
				effect: 'fade',   //effect
				speed: 200,       //speed of effect
				style: 'black',   //color setting
				layout: 'button', //layout type
				distance: 0,      //distance between button and box
				opacity: 1.0,     //opacity menu
				float: 'right',   //position box
				icons: 'yes',     //show icons
				zindex: 100       //z-index
			};
			
			var opt = jQuery.extend(defaults, opt);

			return this.each(function() {  
			
				// set the layout
				if(opt.layout == 'button'){
					var tmLayout = 'tmbutton';	
				}else if(opt.layout == 'bar'){
					var tmLayout = 'tmbar';						
				}else if(opt.layout == 'stack'){
					var tmLayout = 'tmstack';						
				}	
											
				// add layout type
				jQuery(this).parent('appnav').addClass(tmLayout);
									
				// variables
				var obj          = jQuery(this);
				var items        = jQuery("> li", obj);
				var itemsNoSplit = jQuery("> li", obj);
				var itemsSplit   = jQuery("> li.split > div", obj);
				var itemsSub     = jQuery('li li', obj);
				var itemsSubdown = jQuery('li.tmgodown > a', obj);
				var firstItem    = jQuery('> li > div > ul, > li > ul', obj);
				var ulItem       = jQuery('ul', obj);
				var distanceBox  = opt.distance + obj.height();
				var ulBox        = obj.width() + 3;
			    var heightBrigde = distanceBox;
				var spaceBrigde  = distanceBox;
				var zIndex       = opt.zindex - 1;
				
				    // set the effect
					if(opt.effect == 'fade'){
						var effectIn = 'fadeIn';
						var effectOut = 'fadeOut';	
						var speed = opt.speed;
					}else if(opt.effect == 'slide'){
						var effectIn = 'slideDown';
						var effectOut = 'slideUp';
						var speed = opt.speed;						
					}else if(opt.effect == 'none'){
						var effectIn = 'show';
						var effectOut = 'hide';
						var speed = 0;						
					}
					
					// distance form button to box
					if(opt.layout != 'stack'){
						firstItem.css({top: distanceBox+'px'});
					}
										
					// add color scheme
					obj.addClass('tm-'+opt.style);
					
					// add opacity to menu
					obj.find('div').css({opacity: opt.opacity});
					
					// set position left or right
					if((opt.float == 'left') && (opt.layout != 'stack')){
						items.hover(function() {
							var posBox =  jQuery(this).width() - 29;
							$(this).children('div').children('ul').css({left:  '-'+ posBox +'px'});	
						});
					}else if((opt.float == 'right') && (opt.layout != 'stack')){
						$('> li > div > ul',this).css({right: '-133px'});		
					}else if(opt.layout == 'stack'){
						$('> li > div > ul',this).css({right: '-'+ ulBox + 'px', top: '-1px'});	
					}
					
					// add opacity to menu
					obj.css({zIndex: zIndex});
					ulItem.css({zIndex: opt.zindex});
					
					// show/hide icons
					if(opt.icons == 'no'){
						itemsSub.children('a').css({width: '150px', border: 'none'});	
					}
					
						// nonsplit, show box with the complete button
						itemsNoSplit.hover(function() {
							if(!jQuery(this).hasClass('split')){
								jQuery(this).children('div').children('ul:first').stop(true,true)[effectIn](speed);
							}
						},function() {
							jQuery(this).children('div').children('ul:first').stop(true,true)[effectOut](speed);
						});
		
						// split, show box with the arrow only
						itemsSplit.hover(function() {
							jQuery(this).children('ul:first').stop(true,true)[effectIn](speed);		
						},function() {	
							jQuery(this).children('ul:first').stop(true,true)[effectOut](speed);
						});	
		
						// sub items
						itemsSub.hover(function() {
							if(!$(this).hasClass('tmgodown')){
								jQuery(this).children('ul').stop(true,true)[effectIn](speed);
							}
						},function() {
							if(!$(this).hasClass('tmgodown')){	
								jQuery(this).children('ul').stop(true,true)[effectOut](speed).hide(400);
							}
						});
			
						// add and remove bridge, keep the menu alive
						if(opt.layout != 'stack'){
							items.hover(function() {
								if(opt.float == 'left'){
									var widthLi =  jQuery(this).width();
								}else{
									var widthLi =  28;
								}
								jQuery(this).children('div').prepend('<div class="tmbridge" style="width:'+ widthLi +'px;height:'+ heightBrigde +'px;bottom:-'+ spaceBrigde +'px"/>');
							},function() {
								jQuery(this).find('.tmbridge').remove();	
							});
						}

						// sub items down slidedown inside
						itemsSubdown.toggle(function() {
							jQuery(this).parent('li').children('ul:first').stop(true,true).slideDown(speed);
							jQuery(this).children('span').children().remove();
							return false;
						},function(){
							jQuery(this).parent('li').children('ul:first').stop(true,true).slideUp(speed);
							jQuery(this).children('span').prepend('<span/>');
							return false;
						});
	
			});		

		}
})(jQuery);
