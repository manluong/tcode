/**
 * jQuery Image Gallery
 * Copyright (c) 2011 Allan Ma (http://codecanyon.net/user/webtako)
 * Version: 1.53 (07/26/2011)
 */
;(function($) {
	$.fn.wtGallery = function(params) {		
		var INTERVAL_DELAY = 100;
		var DEFAULT_DELAY = 5000;
		var TOOLTIP_DELAY = 800;
		var DURATION = 800;
		var SCROLL_SPEED = 600;
		var ANIMATE_SPEED = 400;
		var LIMIT = 250;
		var STRIPE_SIZE = 50;
		var TOP = "top";
		var BOTTOM = "bottom";
		var PREV = 0;
		var NEXT = 1;
		var BUTTON_OPACITY = 0;
		var UPDATE_IMG_BTNS = "update_imgbtns";
		var UPDATE_IMG_INFO = "update_imginfo";
		var UPDATE_INDEX = 		"update_index";
		var UPDATE_THUMB_INFO = "update_thumbinfo";
		var UPDATE_THUMB_BTNS = "update_thumbbtns";
		var START = "start_timer";
		var RESET = "reset_timer";
		var PAUSE = "pause_timer";
		
		var ei = 0;
		var EFFECTS = {
			"vert.tl":ei++,
			"vert.tr":ei++,
			"vert.bl":ei++,
			"vert.br":ei++,
			"fade.left":ei++,			
			"fade.right":ei++,	
			"alt.left":ei++,
			"alt.right":ei++,
			"blinds.left":ei++,
			"blinds.right":ei++,
			"vert.random.fade":ei++,
			"horz.tl":ei++,
			"horz.tr":ei++,
			"horz.bl":ei++,
			"horz.br":ei++,
			"fade.top":ei++,
			"fade.bottom":ei++,
			"alt.top":ei++,
			"alt.bottom":ei++,
			"blinds.top":ei++,
			"blinds.bottom":ei++,
			"horz.random.fade":ei++,
			"random":ei++,
			"fade":ei++,
			"h.slide":ei++,
			"v.slide":ei++,
			"none":ei++
		}
		
		//Vertical Stripes
		function VertStripes(gallery, areaWidth, areaHeight, stripeSize, bgColor, duration, delay) {
			var $stripes;
			var $arr;
			var total;
			var intervalId = null;
			
			//init stripes
			var init = function() {
				total = Math.ceil(areaWidth/stripeSize);
				if (total > LIMIT) {
					stripeSize = Math.ceil(areaWidth/LIMIT);
					total = Math.ceil(areaWidth/stripeSize);
				}
				var divs = "";
				for (var i = 0; i < total; i++) {
					divs += "<div class='vpiece' id='" + i + "'></div>";
				}					
				gallery.addToScreen(divs);
				
				$stripes = gallery.$el.find("div.vpiece");
				$arr = new Array(total);
				$stripes.each(
					function(n) {
						$(this).css({left:(n * stripeSize), height: areaHeight});
						$arr[n] = $(this);
					}
				);
			}

			//clear animation
			this.clear = function() {
				clearInterval(intervalId);
				$stripes.stop(true).css({"z-index":2, opacity:0});
			}

			//display content
			this.displayContent = function($img, effect) {
				setPieces($img, effect);
				if (effect == EFFECTS["vert.random.fade"]) {
					animateRandom($img);
				}
				else {
					animate($img, effect);
				}
			}			
			
			//set image stripes
			var setPieces = function($img, effect) {
				switch (effect) {
					case EFFECTS["vert.tl"]:
					case EFFECTS["vert.tr"]:
						setVertPieces($img, -areaHeight, 1, stripeSize, false);
						break;
					case EFFECTS["vert.bl"]:
					case EFFECTS["vert.br"]:
						setVertPieces($img, areaHeight, 1, stripeSize, false);
						break;
					case EFFECTS["alt.left"]:
					case EFFECTS["alt.right"]:
						setVertPieces($img, 0, 1, stripeSize, true);
						break;
					case EFFECTS["blinds.left"]:
					case EFFECTS["blinds.right"]:
						setVertPieces($img, 0, 1, 0, false);
						break;
					default:
						setVertPieces($img, 0, 0, stripeSize, false);
				}
			}
			
			//set vertical stripes
			var setVertPieces = function($img, topPos, opacity, width, alt) {
				var imgSrc = $img.attr("src");
				var tOffset = 0;
				var lOffset = 0;
				if (gallery.autoCenter()) {
					tOffset = (areaHeight - $img.height())/2;
					lOffset = (areaWidth - $img.width())/2;
				}
				for (var i = 0; i < total; i++) {		
					var xPos =  ((-i * stripeSize) + lOffset);
					if (alt) {
						topPos = (i % 2) == 0 ? -areaHeight: areaHeight;
					}
					$($stripes.get(i)).css({"background":bgColor + " url('"+ imgSrc +"') " + xPos + "px " + tOffset + "px no-repeat",
											"backgroundPositionX":xPos + "px", "backgroundPositionY":tOffset + "px",
											opacity:opacity, top:topPos, width:width, "z-index":3});
				}
			}
			
			//animate stripes			
			var animate = function($img, effect) {
				var start, end, incr, limit;
				switch (effect) {
					case EFFECTS["vert.tl"]:   case EFFECTS["vert.bl"]: 
					case EFFECTS["fade.left"]: case EFFECTS["blinds.left"]: 
					case EFFECTS["alt.left"]:
						start = 0;
						end = total - 1;
						incr = 1;
						break;
					default:
						start = total - 1;
						end = 0;
						incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						$($stripes.get(start)).animate({top:0, opacity:1, width:stripeSize}, duration, gallery.easing(),
							function() {
								if ($(this).attr("id") == end) {
									gallery.setComplete($img);
								}
							}
						);
						if (start == end) {
							clearInterval(intervalId);
						}
						start += incr;
					}, delay);
			}
			
			//animate random fade 
			var animateRandom = function($img) {		
				shuffleArray($arr);
				var i = 0;
				var count = 0;
				intervalId = setInterval(
					function() {
						$arr[i++].animate({opacity:1}, duration, gallery.easing(),
								function() {
									if (++count == total) {
										gallery.setComplete($img);
									}
								});
						if (i == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			init();
		}
		
		//Horizontal Stripes
		function HorzStripes(gallery, areaWidth, areaHeight, stripeSize, bgColor, duration, delay) {
			var $stripes;
			var $arr;
			var total;
			var intervalId = null;
			
			//init stripes
			var init = function() {			
				total = Math.ceil(areaHeight/stripeSize);
				if (total > LIMIT) {
					stripeSize = Math.ceil(areaHeight/LIMIT);
					total = Math.ceil(areaHeight/stripeSize);
				}
				var divs = "";
				for (var j = 0; j < total; j++) {
					divs += "<div class='hpiece' id='" + j + "'><!-- --></div>";
				}				
				gallery.addToScreen(divs);
				
				$stripes = gallery.$el.find("div.hpiece");
				$arr = new Array(total);
				$stripes.each(
					function(n) {
						$(this).css({top:(n * stripeSize), width: areaWidth});
						$arr[n] = $(this);
					}							 
				);
			}

			//clear animation
			this.clear = function() {
				clearInterval(intervalId);
				$stripes.stop(true).css({"z-index":2, opacity:0});
			}

			//display content
			this.displayContent = function($img, effect) {
				setPieces($img, effect);
				if (effect == EFFECTS["horz.random.fade"]) {
					animateRandom($img);
				}
				else {
					animate($img, effect);
				}
			}			
			
			//set image stripes
			var setPieces = function($img, effect) {
				switch (effect) {
					case EFFECTS["horz.tr"]:
					case EFFECTS["horz.br"]:
						setHorzPieces($img, areaWidth, 1, stripeSize, false);
						break;
					case EFFECTS["horz.tl"]:
					case EFFECTS["horz.bl"]:
						setHorzPieces($img, -areaWidth, 1, stripeSize, false);
						break;
					case EFFECTS["alt.top"]:
					case EFFECTS["alt.bottom"]:
						setHorzPieces($img, 0, 1, stripeSize, true);
						break;
					case EFFECTS["blinds.top"]:
					case EFFECTS["blinds.bottom"]:
						setHorzPieces($img, 0, 1, 0, false);
						break;
					default:
						setHorzPieces($img, 0, 0, stripeSize, false);
				}
			}
			
			//set horizontal stripes
			var setHorzPieces = function($img, leftPos, opacity, height, alt) {
				var imgSrc = $img.attr("src");
				var tOffset = 0;
				var lOffset = 0;
				if (gallery.autoCenter()) {
					tOffset = (areaHeight - $img.height())/2;
					lOffset = (areaWidth - $img.width())/2;
				}
				for (var i = 0; i < total; i++) {			
					var yPos = ((-i * stripeSize) + tOffset);
					if (alt) {
						leftPos = (i % 2) == 0 ? -areaWidth: areaWidth;
					}
					$($stripes.get(i)).css({"background":bgColor + " url('"+ imgSrc +"') " + lOffset + "px " + yPos  + "px no-repeat",
											"backgroundPositionX":lOffset  + "px", "backgroundPositionY":yPos + "px",
											opacity:opacity, left:leftPos, height:height, "z-index":3});  
				}
			}
			
			//animate stripes			
			var animate = function($img, effect) {
				var start, end, incr;
				switch (effect) {
					case EFFECTS["horz.tl"]:  case EFFECTS["horz.tr"]: 
					case EFFECTS["fade.top"]: case EFFECTS["blinds.top"]: 
					case EFFECTS["alt.top"]:
						start = 0;
						end = total - 1;
						incr = 1;
						break;
					default:
						start = total - 1;
						end = 0;
						incr = -1;
				}
				
				intervalId = setInterval(
					function() {
						$($stripes.get(start)).animate({left:0, opacity:1, height:stripeSize}, duration, gallery.easing(),
							function() {
								if ($(this).attr("id") == end) {
									gallery.setComplete($img);
								}
							}
						);
						if (start == end) {
							clearInterval(intervalId);
						}
						start += incr;
					}, delay);
			}
			
			//animate random fade 
			var animateRandom = function($img) {		
				shuffleArray($arr);
				var i = 0;
				var count = 0;
				intervalId = setInterval(
					function() {
						$arr[i++].animate({opacity:1}, duration, gallery.easing(),
								function() {
									if (++count == total) {
										gallery.setComplete($img);
									}
								});
						if (i == total) {
							clearInterval(intervalId);
						}
					}, delay);
			}
			
			init();
		}
		
		//Gallery Class
		function Gallery($obj, opts) {
			//set options
			var numDisplay = 		getPosNumber(opts.num_display,5);
			var screenWidth = 		getPosNumber(opts.screen_width,720);
			var screenHeight = 		getPosNumber(opts.screen_height,360);
			var thumbWidth = 		getPosNumber(opts.thumb_width,125);
			var thumbHeight = 		getPosNumber(opts.thumb_height,70);
			var contImgNav = 		opts.cont_imgnav;
			var contThumbNav = 		opts.cont_thumbnav;
			var displayImgBtns = 	opts.display_imgnav;
			var displayPlayBtn =	opts.display_play;
			var displayImgNum = 	opts.display_imgnum;
			var displayTimer =		opts.display_timer;
			var displayThumbBtns = 	opts.display_thumbnav;
			var displayThumbInfo = 	opts.display_thumbnum;
			var displayArrow = 		opts.display_arrow;
			var displayTooltip = 	opts.display_tooltip;
			var displayIndex = 		opts.display_indexes;
			var mouseoverPause =	opts.mouseover_pause;
			var mouseoverText = 	opts.mouseover_text;
			var mouseoverInfo =		opts.mouseover_info;
			var mouseoverDBtns = 	opts.mouseover_buttons;
			var mouseoverCaption = 	opts.mouseover_caption;
			var textAlign = 		opts.text_align.toLowerCase();
			var captionAlign = 		opts.caption_align.toLowerCase();
			var globalEffect = 		opts.transition.toLowerCase();
			var globalDelay = 		getPosNumber(opts.delay, DEFAULT_DELAY);
			var duration = 			getPosNumber(opts.transition_speed, DURATION);
			var scrollSpeed = 		getPosNumber(opts.scroll_speed, SCROLL_SPEED);
			var moveBy1 = 			opts.move_one;
			var shuffle = 			opts.shuffle;
			var easing = 			opts.easing;
			var autoCenter =		opts.auto_center;
			var playOnce =			opts.rotate_once;
			var numItems;
			var unitSize;
			var prevSlots;
			var nextSlots;
			var maxSlots;
			var currIndex;
			var prevIndex;
			var pos;
			var vStripes;
			var hStripes;
			var rotate;
			var delay;
			var textOffset;
			var selectStyle;
			var timerId;
			var dir;
			var hStripeEffect;
			var vStripeEffect;
			
			var $gallery;   			
			var $screen;
			var $preloader;
			var $mainLink;
			var $strip;
			var $playBtn;
			var $prevBtn;
			var $nextBtn;
			var $textBox;
			var $infoPanel;
			var $thumbPanel;
			var $thumbList;
			var $thumbs;
			var $thumbBoxes;
			var $cpanel;
			var $listBackButton;
			var $listFwdButton;
			var $thumbInfo;
			var $tooltip;
			var $items;
			var $indexes;
			var $timer;
			var $innerText;
			this.$el = $obj;
			
			this.init = function() {
				$gallery = 		$obj.find(".wt-gallery");   			
				$screen = 		$gallery.find(".main-screen");
				$thumbPanel = 	$gallery.find(".thumbnails");
				$thumbList =	$thumbPanel.find(">ul");
				$thumbs	=		$thumbList.find(">li");
				$thumbBoxes = 	$thumbs.find(">div:first");
					
				timerId = null;
				currIndex = 0;
				prevIndex = -1;
				pos = 0;
				numItems = $thumbs.size();
				if (numItems <= numDisplay) {
					displayThumbBtns = displayIndex = false;
					numDisplay = numItems;
				}				
				if (displayIndex) {
					moveBy1 = false;
				}
				hStripeEffect = vStripeEffect = false;
				checkEffect(EFFECTS[globalEffect]);
				
				//init components
				initScreen();
				initCPanel();
				initItems();
				
				//config gallery 
				var areaWidth =  $screen.outerWidth() > $cpanel.outerWidth() ? $screen.outerWidth() : $cpanel.outerWidth();
				var areaHeight = $screen.outerHeight() + $cpanel.outerHeight();
				$gallery.css({width:areaWidth, height:areaHeight, padding:getNonNegNumber(opts.padding,0)});
				$(document).keyup(onKeyPress);
				var autoStart = opts.auto_rotate;
				rotate = false;
				if (autoStart) {
					rotate = true;
					$gallery.bind(START, startTimer).bind(PAUSE, pauseTimer).bind(RESET, resetTimer);
					if (mouseoverPause) {
						$gallery.hover(galleryOver, galleryOut);
					}
				}
				else if (displayPlayBtn) {
					$gallery.bind(START, startTimer).bind(PAUSE, pauseTimer).bind(RESET, resetTimer);
				}
				
				//init effect components
				if (vStripeEffect) {
					vStripes =  new VertStripes(this, screenWidth, screenHeight, getPosNumber(opts.vert_size, STRIPE_SIZE), 
												$screen.css("background-color"), duration, getPosNumber(opts.vstripe_delay, INTERVAL_DELAY));
				}
				if (hStripeEffect) {
					hStripes =  new HorzStripes(this, screenWidth, screenHeight, getPosNumber(opts.horz_size, STRIPE_SIZE), 
												$screen.css("background-color"), duration, getPosNumber(opts.hstripe_delay, INTERVAL_DELAY));
				}
				//init loading
				loadImg(0);
				
				//display image
				loadContent(currIndex);
				updateCPanel();
			}
			
			//set main image
			this.setComplete = function($img) {
				showContent($img);
			}
			
			//add to screen
			this.addToScreen = function(content) {
				$mainLink.append(content);
			}
			
			//get auto center
			this.autoCenter = function() {
				return autoCenter;
			}
			
			//get easing
			this.easing = function() {
				return easing;
			}
			
			//config main screen
			var initScreen = function() {
				var content = "<div id='prev-btn'></div>\
								<div id='play-btn'></div>\
								<div id='next-btn'></div>\
								<div class='desc'></div>\
								<div class='info'></div>\
								<div class='preloader'></div>";
				$screen.append(content);
				$prevBtn = 		$screen.find("#prev-btn");
				$nextBtn = 		$screen.find("#next-btn");
				$playBtn =		$screen.find("#play-btn");
				$textBox = 		$screen.find(".desc");
				$infoPanel = 	$screen.find(".info");
				$preloader = 	$screen.find(".preloader");
				
				$screen.css({width:screenWidth, height:screenHeight});
				textOffset = (msieCheck(6) && screenHeight % 2 != 0) ? -1 : 0;
				
				$strip = $("<div id='strip'></div>");
				if (globalEffect == "h.slide") {
					$screen.append($strip);
					$strip.css({width:2*screenWidth, height:screenHeight});
					$thumbs.removeAttr("effect");
				}
				else if (globalEffect == "v.slide"){
					$screen.append($strip);
					$strip.css({width:screenWidth, height:2*screenHeight});
					$thumbs.removeAttr("effect");
				}
				else {
					$screen.append("<a href='#'></a>");
					$mainLink =	$screen.find(">a:first");
				}
				
				//config components
				initPlayButton();
				initDButtons();
				initTextBox();
				initInfoPanel();
			}
			
			var initPlayButton = function() {
				if (displayPlayBtn) {
					if (opts.auto_rotate) {
						$playBtn.addClass("pause");
					}
					
					$playBtn.click(
							function() {
								rotate = !rotate;
								$(this).toggleClass("pause", rotate);
								if (rotate) {
									$gallery.trigger(START);
								}
								else {
									$gallery.trigger(PAUSE);
								}
							});
					$screen.hover(function() { $playBtn.stop(true,true).fadeIn(ANIMATE_SPEED); }, 
								  function() { $playBtn.stop(true,true).fadeOut(ANIMATE_SPEED); });
				}
				else {
					$playBtn.hide();
				}
			}
			
			//config d-buttons
			var initDButtons = function() {				
				if (displayImgBtns) {
					var prevBtnPos = 0;
					var nextBtnPos = screenWidth - $nextBtn.width();
					if (mouseoverDBtns) {			
						$prevBtn.data({offset:-$prevBtn.width(), pos:prevBtnPos}).css({left:$prevBtn.data("offset")});
						$nextBtn.data({offset:screenWidth, pos:nextBtnPos}).css({left:$nextBtn.data("offset")});
						$screen.hover(displayDButtons, hideDButtons);
					}
					else {
						$prevBtn.css({left:prevBtnPos});
						$nextBtn.css({left:nextBtnPos});
					}
					$prevBtn.css("visibility","visible").mousedown(preventDefault).click(prevImg);
					$nextBtn.css("visibility","visible").mousedown(preventDefault).click(nextImg);
					
					if (!contImgNav) {
						$gallery.bind(UPDATE_IMG_BTNS, updateImgBtns);
					}
				}
				else {
					$prevBtn.hide();
					$nextBtn.hide();
				}
			}
			
			//display d-buttons
			var displayDButtons = function() {
				$prevBtn.stop().animate({left:$prevBtn.data("pos")}, ANIMATE_SPEED);
				$nextBtn.stop().animate({left:$nextBtn.data("pos")}, ANIMATE_SPEED);
			}

			//hide d-buttons
			var hideDButtons = function() {
				$prevBtn.stop().animate({left:$prevBtn.data("offset")}, ANIMATE_SPEED);
				$nextBtn.stop().animate({left:$nextBtn.data("offset")}, ANIMATE_SPEED);
			}
			
			//update d-buttons
			var updateImgBtns = function() {
				$prevBtn.css((currIndex == 0) ? {opacity:0, cursor:"default"} : {opacity:1, cursor:"pointer"});
				$nextBtn.css((currIndex == numItems - 1) ? {opacity:0, cursor:"default"} : {opacity:1, cursor:"pointer"});
			}
			
			//config text box
			var initTextBox = function() {
				var align, offset;
				if (textAlign == BOTTOM) {
					align = BOTTOM;
					offset = textOffset;
				}
				else {
					align = TOP;
					offset = 0;
				}
				
				$textBox.data("align", align).append("<div class='inner-text'></div>");
				$innerText = $textBox.find("div.inner-text");
				
				if (mouseoverText) {
					$textBox.css(align, -$textBox.height() + offset);
					$screen.data("hover", false).hover(displayText, hideText);
				}
				else {
					$textBox.css(align, offset);
				}				
			}
			
			//update text box
			var updateText = function() {
				var text = $items[currIndex].find("div.data>div:first").html();
				var height = $items[currIndex].data("textHeight");
				if (mouseoverText && !$screen.data("hover")) {			
					$textBox.stop(true).css($textBox.data("align"), -height).height(height);
					$innerText.html(text);
				}
				else {
					$innerText.html("");
					$textBox.stop(true).animate({height:height}, ANIMATE_SPEED, 
												function () { 
													$innerText.css("opacity",0).html(text).animate({opacity:1}, ANIMATE_SPEED);
												});  	
				}
			}
			
			//display text
			var displayText = function() {
				$screen.data("hover", true);
				$textBox.stop(true, true).animate($textBox.data("align") == TOP ? {top:0} : {bottom:textOffset}, ANIMATE_SPEED);
			}
			
			//hide text
			var hideText = function() {
				$screen.data("hover", false);
				$textBox.stop(true, true).animate($textBox.data("align") == TOP ? {top:-$textBox.height()} : {bottom:-$textBox.height() + textOffset}, ANIMATE_SPEED);
			}
			
			//init info panel
			var initInfoPanel = function() {
				$infoPanel.append("<div id='timer'></div>");
				$timer = $infoPanel.find("#timer").data("pct", 1);
				if (!displayImgNum && !displayTimer) {
					$infoPanel.hide();
					return;
				}
				
				var align, offset;
				if ($textBox.data("align") == TOP) {
					align = BOTTOM;
					offset = textOffset;
				}
				else {
					align = TOP;
					offset = 0;
				}				
				$infoPanel.data("align", align).css("visibility","visible");
				if (mouseoverInfo) {
					$infoPanel.css(align, -$infoPanel.height() + offset);
					$screen.hover(displayInfo, hideInfo);
				}
				else {
					$infoPanel.css(align, offset);
				}
					
				if (displayImgNum) {
					$infoPanel.append("<div class='inner-info'></div>");
					$gallery.bind(UPDATE_IMG_INFO, updateImgInfo);
				}
					
				if (displayTimer) {
					$timer.css("visibility","visible");
				}
			}
			
			//display info panel
			var displayInfo = function() {
				$infoPanel.stop().animate(($infoPanel.data("align") == BOTTOM) ? {bottom:textOffset} : {top:0}, ANIMATE_SPEED);
			}
			
			//hide info panel
			var hideInfo = function() {
				$infoPanel.stop().animate(($infoPanel.data("align") == BOTTOM) ? {bottom:-$infoPanel.height() + textOffset} : {top:-$infoPanel.height()}, ANIMATE_SPEED);
			}
			
			//update image info
			var updateImgInfo = function() {
				$infoPanel.find("div.inner-info").html((currIndex+1) + " / " + numItems);
			}
			
			//init items
			var initItems = function() {
				var $captions = $thumbs.find(">div:first>p:first");
				if (displayTooltip) {
					$captions.hide();
					$tooltip = $("<div id='gallery-tooltip'></div>").append("<div class='tt-txt'></div>");
					$("body").append($tooltip);
					if (captionAlign == TOP) {
						$tooltip.data("bottom",false).addClass("txt-up");
					}
					else {
						$tooltip.data("bottom",true).addClass("txt-down");
					}
					
					if (msieCheck(6)) {
						$tooltip.css("background-image", "none").find(":only-child").css("margin",0);
					}
				}
				else {
					var pad = $captions.outerWidth() - $captions.width();
					$captions.width(thumbWidth - pad);
				}
				
				$items = new Array(numItems);
				$thumbs.each(
					function(n) {
						var $box = $(this).find(">div:first");
						var $imgLink = $box.find(">a:first");
						var $img = $imgLink.find("img");
						var $caption = $box.find(">p:first");
						var $p = $(this).find(">div.data>div:first");
						var textHeight = ($p.length > 0 && $p.html() != "") ? $innerText.html($p.html()).outerHeight() : 0;
						var itemEffect = EFFECTS[$(this).attr("effect")];
						if (itemEffect == undefined || itemEffect ==  EFFECTS["h.slide"] || itemEffect ==  EFFECTS["v.slide"]) {
							itemEffect = EFFECTS[globalEffect];
						}
						else {
							checkEffect(itemEffect);
						}
						$(this).data({imgurl:$imgLink.attr("href"), caption:$caption.html(),
									  effect:itemEffect, delay:getPosNumber($(this).attr("delay"), globalDelay), textHeight:textHeight});
						
						$img[0].complete || $img[0].readyState == "complete" ? processImg($img) : $img.load(processLoadedImg);
						if ($caption.length > 0 && $caption.html() != "") {
							if (displayTooltip) {
								$box.hover(showTooltip, hideTooltip).mousemove(moveTooltip);
							}
							else {															  
								if (mouseoverCaption) {
									$box.hover(displayCaption, hideCaption);
									$caption.css("top", captionAlign == BOTTOM ? thumbHeight : -$caption.outerHeight());
								}
								else {
									$caption.css("top", captionAlign == BOTTOM ? thumbHeight - $caption.outerHeight() : 0);
								}
							}
						}
						$items[n] = $(this);
					}).hover(itemMouseover, itemMouseout);
				$innerText.html("");
				$textBox.css("visibility", "visible");
				
				if (shuffle) {
					shuffleItems();
				}
			}
			
			//select list item
			var selectItem = function(e) {
				var $item = $(e.target).parents("li").eq(0);
				var i = $item.index();
				if (i >= 0 && i != currIndex) {
					dir = i < currIndex ? PREV : NEXT;
					$gallery.trigger(RESET);
					prevIndex = currIndex;
					currIndex = i;
					loadContent(currIndex);
				}
				return false;
			}
			
			//on item mouseover
			var itemMouseover = function() {
				if ($(this).index() != currIndex) {
					$(this).find(">div:first").addClass("thumb-over");
				}
			}
			
			//on item mouseout
			var itemMouseout = function() {
				$(this).find(">div:first").removeClass("thumb-over");
			}
			
			//display thumb caption
			var displayCaption = function() {
				var $caption = $(this).find(">p:first");
				$caption.stop().animate({top:(captionAlign == BOTTOM) ? thumbHeight - $caption.outerHeight() : 0}, 300);
			}
			
			//hide thumb caption
			var hideCaption = function() {
				var $caption = $(this).find(">p:first");
				$caption.stop().animate({top:(captionAlign == BOTTOM) ? thumbHeight : -$caption.outerHeight()}, 300);
			}
			
			//show tooltip
			var showTooltip = function(e) {
				var caption = $(this).parent().data("caption");
				var yOffset = $tooltip.data("bottom") ? 0 : -$tooltip.outerHeight(true);
				$tooltip.find(">div.tt-txt").html(caption);
				$tooltip.css({top:e.pageY + yOffset, left:e.pageX}).stop(true, true).delay(TOOLTIP_DELAY).fadeIn(300);
			}
			
			//tooltip move
			var moveTooltip = function(e) {
				var yOffset = $tooltip.data("bottom") ? 0 : -$tooltip.outerHeight(true);
				$tooltip.css({top:e.pageY + yOffset, left:e.pageX});
			}
			
			//hide tooltip
			var hideTooltip = function() {
				$tooltip.stop(true, true).fadeOut(0);
			}
			
			//init control panel
			var initCPanel = function() {
				$cpanel = $gallery.find(".cpanel");
				
				//config thumbnails
				var thumbMargin = getNonNegNumber(opts.thumb_margin,0);
				$thumbBoxes.css({width:thumbWidth, height:thumbHeight});
				$thumbs.css({"margin-right":thumbMargin});
				unitSize = $thumbs.outerWidth(true);
				$thumbPanel.width((numDisplay * $thumbBoxes.outerWidth()) + ((numDisplay - 1) * thumbMargin)).click(selectItem);
				
				var num;
				if (displayIndex) {
					num = numDisplay * Math.ceil(numItems/numDisplay);
				}
				else {
					num = numItems;
				}
				$thumbList.width(num * unitSize);
				maxSlots = num - numDisplay;
				prevSlots = 0;
				nextSlots = maxSlots;
				
				initThumbButtons();
				initIndexBar();
				if (!displayIndex && !displayThumbBtns) {
					moveBy1 = true;
					contThumbNav = false;
					$thumbBoxes.click(itemMove);
				}
								
				selectStyle = displayArrow ? "curr-arrow" : "curr";
				$cpanel.width($thumbPanel.outerWidth() + $listBackButton.outerWidth() + $listFwdButton.outerWidth());
				$cpanel.height($thumbPanel.outerHeight() + $cpanel.find("#cbar").outerHeight());
			}
			
			//config thumb buttons
			var initThumbButtons = function() {
				$listBackButton = $cpanel.find("#thumbs-back");
				$listFwdButton =  $cpanel.find("#thumbs-fwd");
				if (displayThumbBtns) {
					var height = $thumbBoxes.outerHeight();
					var margin = $thumbBoxes.css("margin-top");
					$listBackButton.css({height:height, "margin-top":margin}).mousedown(preventDefault).click(prevThumbs);
					$listFwdButton.css({height:height,  "margin-top":margin}).mousedown(preventDefault).click(nextThumbs);
					
					if (!contThumbNav) {
						$gallery.bind(UPDATE_THUMB_BTNS, updateThumbBtns);
					}
				}
				else {
					$listBackButton.remove();
					$listFwdButton.remove();
				}
			}
			
			//config index bar
			var initIndexBar = function() {
				if (displayIndex || displayThumbInfo) {
					$cpanel.append("<div id='cbar'><div class='thumb-info'></div></div>");
				}
				else {
					return;
				}
				var $lowerPanel = $cpanel.find("#cbar").css({width:$thumbPanel.outerWidth(), "margin-left":$listBackButton.outerWidth(), "margin-right":$listFwdButton.outerWidth()});
				if (displayThumbInfo) {
					$thumbInfo = $cpanel.find(".thumb-info");
					$gallery.bind(UPDATE_THUMB_INFO, updateThumbInfo);
					if (!displayIndex) {
						$thumbInfo.css("width", "100%");
					}
				}
				else {
					$cpanel.find(".thumb-info").hide();
				}
				
				if (displayIndex) {
					var n = Math.ceil(numItems/numDisplay);
					var str = "<div class='index-panel'>";
					for (var i = 0; i < n; i++) {
						str += "<div class='index'></div>";
					}
					str += "</div>";
					$lowerPanel.append(str);
					$indexes = $lowerPanel.find(".index").mousedown(preventDefault).bind("click", goToIndex);
					$gallery.bind(UPDATE_INDEX, updateIndexes);
					var $indexPanel = $lowerPanel.find(".index-panel");
					$indexPanel.css({"padding-left":Math.floor(($lowerPanel.width() - $indexPanel.width())/2)});
				}
			}
				
			//update control panel
			var updateCPanel = function() {
				$gallery.trigger(UPDATE_INDEX).trigger(UPDATE_THUMB_INFO).trigger(UPDATE_THUMB_BTNS);
			}
			
			//update indexes
			var updateIndexes = function() {
				var i = Math.ceil(prevSlots/numDisplay);
				$indexes.filter(".index-hl").removeClass("index-hl");
				$($indexes.get(i)).addClass("index-hl");
			}
			
			//update thumb info
			var updateThumbInfo = function() {
				var start = -(pos/unitSize);
				var end = Math.min(start + numDisplay, numItems);
				$thumbInfo.html((start + 1) + " - " + end + " of " + numItems);
			}
			
			//update thumb buttons
			var updateThumbBtns = function() {
				var start = Math.abs(pos/unitSize);
				var end = start + numDisplay;
				$listBackButton.css((start > 0) ? {opacity:1, cursor:"pointer"} : {opacity:BUTTON_OPACITY, cursor:"default"});
				$listFwdButton.css((end < numItems) ? {opacity:1, cursor:"pointer"} : {opacity:BUTTON_OPACITY, cursor:"default"});
			}
			
			//previous image
			var prevImg = function() {
				if (currIndex > 0) {
					prevIndex = currIndex;
					currIndex--;
				}
				else if (contImgNav) {
					prevIndex = currIndex;
					currIndex = numItems - 1;
				}
				else {
					return;
				}
				dir = PREV;
				$gallery.trigger(RESET);
				loadContent(currIndex);
				moveThumbs(currIndex);
				return false;
			}
			
			//next image
			var nextImg = function() {
				if (currIndex < numItems - 1) {
					prevIndex = currIndex;
					currIndex++;
				}
				else if (contImgNav) {
					prevIndex = currIndex;
					currIndex = 0;
				}
				else {
					return;
				}
				dir = NEXT;
				$gallery.trigger(RESET);
				loadContent(currIndex);
				moveThumbs(currIndex);
				return false;
			}
			
			//rotate image
			var rotateImage = function() {
				dir = NEXT;
				prevIndex = currIndex;
				currIndex = (currIndex < numItems - 1) ? currIndex + 1 : 0;
				$gallery.trigger(RESET);
				loadContent(currIndex);
				moveThumbs(currIndex);
			}
			
			//get previous thumbs
			var prevThumbs = function() {
				if (nextSlots < maxSlots) {
					var slots = moveBy1 ? 1 : Math.min(maxSlots - nextSlots, numDisplay);
					nextSlots += slots;
					prevSlots -= slots;
				}
				else if (contThumbNav) {
					nextSlots = 0;
					prevSlots = maxSlots;
				}
				else {
					return;
				}
				moveList();
				return false;
			}
				
			//get next thumbs
			var nextThumbs = function() {
				if (prevSlots < maxSlots) {
					var slots = moveBy1 ? 1 : Math.min(maxSlots - prevSlots, numDisplay);
					prevSlots += slots;
					nextSlots -= slots;
				}
				else if (contThumbNav) {
					prevSlots = 0;
					nextSlots = maxSlots;
				}
				else {
					return;
				}
				moveList();
				return false;
			}
			
			//move list
			var moveList = function() {
				pos = -prevSlots * unitSize;
				$thumbList.stop(true, true).animate({left:pos}, scrollSpeed);
				updateCPanel();
			}
			
			//item click move
			var itemMove = function() {
				var index = ($(this).parents("li").eq(0).index() - prevSlots)%numDisplay
				if (index+1 == numDisplay) {
					nextThumbs();
				}
				else if (index == 0) {
					prevThumbs();
				}
			}
			
			//move thumbs
			var moveThumbs = function(i) {
				prevSlots = Math.floor(i/numDisplay) * numDisplay;
				if (!displayIndex && prevSlots > maxSlots) {
					prevSlots = maxSlots;
				}
				nextSlots = maxSlots - prevSlots;
				moveList();
			}
			
			//go to index			
			var goToIndex = function() {
				prevSlots = $(this).index() * numDisplay;
				nextSlots = maxSlots - prevSlots;
				moveList();
				return false;
			}
			
			//gallery mouseover
			var galleryOver = function() {
				rotate = false;
				$playBtn.removeClass("pause");
				$gallery.trigger(PAUSE);
			}
			
			//gallery mouseout
			var galleryOut = function() {
				rotate = true;
				$playBtn.addClass("pause");
				$gallery.trigger(START);
			}
			
			//pause on last
			var pauseLast = function(i) {
				if (i == numItems - 1) {
					rotate = false;
					$playBtn.removeClass("pause");
					$gallery.trigger(PAUSE);
				}
			}
			
			//load content
			var loadContent = function(i) {
				if (playOnce) {
					pauseLast(i);
				}
				
				//select current thumb
				var $item = $items[i];
				$thumbList.find(">li."+selectStyle).removeClass(selectStyle);
				$item.addClass(selectStyle);
				
				//set delay
				delay = $item.data("delay");
				
				//update link
				if ($mainLink) {
					var $currLink = $item.find("div.data>a");
					var href = $currLink.attr("href");
					if (href) {
						$mainLink.unbind("click", preventDefault).css({cursor:"pointer"}).attr({href:href, target:$currLink.attr("target")});
					}
					else {
						$mainLink.click(preventDefault).css({cursor:"default"});
					}
				}
				
				$gallery.trigger(UPDATE_IMG_INFO).trigger(UPDATE_IMG_BTNS);
				updateText();
				
				//load image
				if ($item.data("img")) {
					$preloader.hide();
					displayContent($item.data("img"));
				}	
				else {	
					//load new image
					var $img = $("<img class='main-img'/>");
					$img.load(
						function() {
							$preloader.hide();
							storeImg($item, $(this));
							displayContent($(this));
						}
					).error(
						function() {
							alert("Error loading image");
						}
					);
					$preloader.show();
					$img.attr("src", $item.data("imgurl"));
				}	    
			}
				
			//display content
			var displayContent = function($img) {
				if (vStripeEffect) {
					vStripes.clear();
					setPrevious();
				}
				if (hStripeEffect) {
					hStripes.clear();
					setPrevious();
				}				
				
				var effect = $items[currIndex].data("effect");
				if (effect == EFFECTS["none"] || effect == undefined) {
					showContent($img);
					return;
				}		
				else if (effect == EFFECTS["fade"]) {
					fadeInContent($img);
					return;
				}
				else if (effect == EFFECTS["h.slide"]) {
					slideContent($img, "left", screenWidth);
					return;
				}
				else if (effect == EFFECTS["v.slide"]) {
					slideContent($img, "top", screenHeight);
					return;
				}
				
				if (effect == EFFECTS["random"]) {
					effect = Math.floor(Math.random() * (ei - 5));
				}				
				
				if (effect <= EFFECTS["vert.random.fade"]){
					vStripes.displayContent($img, effect);
				}
				else if (effect <= EFFECTS["horz.random.fade"]){
					hStripes.displayContent($img, effect);
				}
			}
			
			//set previous
			var setPrevious = function() {
				if (prevIndex >= 0) {
					var currSrc = $mainLink.find("img#curr-img").attr("src");
					var prevSrc = $items[prevIndex].data("imgurl");
					if (currSrc != prevSrc) {
						$mainLink.find("img.main-img").attr("id","").hide();
						var $img = $mainLink.find("img.main-img").filter(function() { return $(this).attr("src") == prevSrc; });
						$($img.get(0)).show();
					}
				}
			}
			
			//display image (no effect)
			var showContent = function($img) {
				$mainLink.find("img.main-img").attr("id","").hide();
				$img.attr("id", "curr-img").show();
				$gallery.trigger(START);
			}
			
			//display content (fade effect)
			var fadeInContent = function($img) {
				$mainLink.find("img#curr-img").stop(true, true);
				$mainLink.find("img.main-img").attr("id","").css("z-index", 0);
				$img.attr("id", "curr-img").css("z-index", 1).stop(true, true).fadeIn(duration, 
					function() {
						$mainLink.find("img.main-img:not('#curr-img')").hide();
						$gallery.trigger(START);
					}
				);
			}
			
			//slide content
			var slideContent = function($currImg, pos, moveby) {
				$strip.stop(true,true);
				var $prevImg = $strip.find("#curr-img");
				if ($prevImg.size() > 0) {
					$strip.find(".main-img").attr("id","").parents(".content-box").css({top:0,left:0});
					$currImg.attr("id", "curr-img").parents(".content-box").show();
					var $img, dest;
					if (dir == PREV) {
						$strip.css(pos, -moveby);
						$img = $prevImg;
						dest = 0;
					}
					else {
						$img = $currImg;
						dest = -moveby;
					}
					$img.parents(".content-box").css(pos,moveby);
					var prop = (pos == "top") ? {top:dest} : {left:dest};
					$strip.stop(true,true).animate(prop, duration, easing,
										function() {
											$strip.find(".main-img:not('#curr-img')").parents(".content-box").hide();
											$img.parents(".content-box").css({top:0,left:0});
											$strip.css({top:0,left:0});
											$gallery.trigger(START);
										});
				}
				else {
					$strip.css({top:0,left:0});
					$strip.find(".main-img").parents(".content-box").hide().css({top:0,left:0});
					$currImg.attr("id", "curr-img").parents(".content-box").show();
					$gallery.trigger(START);
				}
			}
			
			//process loaded thumb image
			var processLoadedImg = function() {
				processImg($(this));
			}
			
			//process thumb image
			var processImg = function($img) {
				var ratio;
				if ($img.outerWidth() > thumbWidth) {
					ratio = $img.outerHeight()/$img.outerWidth();
					$img.width(thumbWidth);
					$img.height(ratio * thumbWidth);
				}
				
				if ($img.outerHeight() > thumbHeight) {
					ratio = $img.outerWidth()/$img.outerHeight();
					$img.width(ratio * thumbHeight);
					$img.height(thumbHeight);
				}
				$img.css({left:Math.round((thumbWidth - $img.outerWidth())/2), top:Math.round((thumbHeight - $img.outerHeight())/2)});
			}
			
			//load image
			var loadImg = function(loadIndex) {
				try {
					var $item = $items[loadIndex];
					var $img = $("<img class='main-img'/>");					
					$img.load(function() {
								if (!$item.data("img")) {
									storeImg($item, $(this));
								}
								loadIndex++
								if (loadIndex < $items.length) {
									loadImg(loadIndex);
								}
							})
						.error(function() {
								//error loading image, continue next
								loadIndex++
								if (loadIndex < $items.length) {
									loadImg(loadIndex);
								}
							});
					$img.attr("src", $item.data("imgurl"));	
				}
				catch(ex) {}
			}
			
			//process & store image
			var storeImg = function($item, $img) {
				if (globalEffect == "h.slide" || globalEffect == "v.slide") {
					$strip.append($img);
					centerImg($img);
					var $div = $("<div class='content-box'></div>").css({width:screenWidth, height:screenHeight});
					$img.wrap($div);
					$img.css("display","block");
					var $link = $item.find("div.data>a");
					if ($link) {
						$img.wrap($link);
					}
				}
				else {
					$mainLink.append($img);
					centerImg($img);
				}
				$item.data("img", $img);
			}
			
			var centerImg = function($img) {
				if (autoCenter && $img.width() > 0 && $img.height() > 0) {
					var tDiff = (screenHeight - $img.height())/2;
					var lDiff = (screenWidth  - $img.width())/2
					var top = 0, left = 0, vPad = 0, hPad = 0;
					if (tDiff > 0) {
						vPad = tDiff;
					}
					else if (tDiff < 0) {
						top = tDiff;
					}				
					if (lDiff > 0) {
						hPad = lDiff;
					}
					else if (lDiff < 0) {
						left = lDiff;
					}
					$img.css({top:top, left:left, "padding-top":vPad, "padding-bottom":vPad, "padding-left":hPad, "padding-right":hPad});
				}
			}
			
			//shuffle items
			var shuffleItems = function() {		
				for (var i = 0; i < $items.length; i++) {
					var ri = Math.floor(Math.random() * $items.length);
					var temp = $items[i];
					$items[i] = $items[ri];
					$items[ri] = temp;
				}
				
				for (var i = 0; i < $items.length; i++) {
					$items[i] = $items[i].clone(true);
				}
				
				for (var i = 0; i < $items.length; i++) {
					$($thumbs.get(i)).replaceWith($items[i]);
				}
			}
			
			//start timer
			var startTimer = function() {
				if (rotate && timerId == null) {
					var duration = Math.round($timer.data("pct") * delay);
					$timer.animate({width:(screenWidth+1)}, duration, "linear");
					timerId = setTimeout(rotateImage, duration);
				}
			}
			
			//reset timer
			var resetTimer = function() {
				clearTimeout(timerId);
				timerId = null;
				$timer.stop(true).width(0).data("pct", 1);
			}
			
			//pause timer
			var pauseTimer = function() {
				clearTimeout(timerId);
				timerId = null;
				var pct = 1 - ($timer.width()/(screenWidth+1));
				$timer.stop(true).data("pct", pct);
			}
			
			//check effect
			var checkEffect = function(num) {
				if (num == EFFECTS["random"]) {
					hStripeEffect = vStripeEffect = true;
				}
				else if (num <= EFFECTS["vert.random.fade"]) {
					vStripeEffect = true;
				}
				else if (num <= EFFECTS["horz.random.fade"]) {
					hStripeEffect = true;
				}
			}
			
			//prevent default behavior
			var preventDefault = function() {
				return false;
			}
			
			var onKeyPress = function(e) {
				switch(e.keyCode) {
					case 37:
						prevImg();
						break;
					case 39:
						nextImg();
						break;
				}
			}
		}
		
		//msie ver. check
		var msieCheck = function(ver) {
			if (jQuery.browser.msie && parseInt(jQuery.browser.version) <= ver) {
				return true;
			}
			return false;
		}
		
		//shuffle array
		var shuffleArray = function(arr) {
			var total =  arr.length;
			for (var i = 0; i < total; i++) {
				var ri = Math.floor(Math.random() * total);
				var temp = arr[i];
				arr[i] = arr[ri];
				arr[ri] = temp;
			}	
		}
		
		//get positive number
		var getPosNumber = function(val, defaultVal) {
			if (!isNaN(val) && val > 0) {
				return val;
			}
			return defaultVal;
		}
		
		//get nonnegative number
		var getNonNegNumber = function(val, defaultVal) {
			if (!isNaN(val) && val >= 0) {
				return val;
			}
			return defaultVal;
		}
		
		var defaults = { 
			num_display:5,
			screen_width:720,
			screen_height:360,
			padding:10,
			thumb_width:125,
			thumb_height:70,
			thumb_margin:5,
			text_align:TOP,
			caption_align:BOTTOM,
			auto_rotate:true,
			delay:DEFAULT_DELAY,			
			rotate_once:false,
			auto_center:true,
			cont_imgnav:true,
			cont_thumbnav:true,
			display_play:true,
			display_imgnav:true,		
			display_imgnum:true,
			display_thumbnav:true,
			display_thumbnum:false,
			display_arrow:true,	
			display_tooltip:false,
			display_timer:true,
			display_indexes:true,
			mouseover_pause:false,
			mouseover_text:false,
			mouseover_info:false,
			mouseover_caption:true,
			mouseover_buttons:true,
			transition:"h.slide",
			transition_speed:DURATION,
			scroll_speed:SCROLL_SPEED,
			vert_size:STRIPE_SIZE,
			horz_size:STRIPE_SIZE,
			vstripe_delay:INTERVAL_DELAY,
			hstripe_delay:INTERVAL_DELAY,
			move_one:false,
			shuffle:false,
			easing:""
		};
		
		var opts = $.extend({}, defaults, params);
		return this.each(
			function() {
				var gallery = new Gallery($(this), opts);
				gallery.init();
			}
		);
	}
})(jQuery);