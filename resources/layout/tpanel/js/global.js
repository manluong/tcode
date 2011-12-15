// HTML5 placeholder plugin version 0.3
// Enables cross-browser* html5 placeholder for inputs, by first testing
// for a native implementation before building one.
//
// USAGE: 
//$('input[placeholder]').placeholder();

(function ($) {
    $.fn.placeholder = function (options) {
        return this.each(function () {
            if (!("placeholder" in document.createElement(this.tagName.toLowerCase()))) {
                var $this = $(this), 
                placeholder = $this.attr('placeholder');
                $this.val(placeholder).data('color', $this.css('color')).css('color', '#aaa');
                $this
                .focus(function () {
                        if ($.trim($this.val()) === placeholder) {
                            $this.val('').css('color', $this.data('color'));
                        }
                    }) 
                .blur(function () {
                        if (!$.trim($this.val())) {
                            $this.val(placeholder).data('color', $this.css('color')).css('color', '#aaa');
                        }
                    });
                }
            });
        };
    }
    (jQuery)
);

var menuYloc = null;
var adminCookieExpiry = 365;
var sortableCookie = "sortable-order";
var sortableName = ".sortable";
var appmenuCollapsed1 = "140px";
var appmenuCollapsed2 = "50px";

// perform JavaScript after the document is scriptable.
$(document).ready(function () {

    /**
     * Setup superfish menu
     */
    if (typeof $("ul.sf-menu").superfish === 'function') {
        $("ul.sf-menu").supersubs({
                minWidth : 15, // minimum width of sub-menus in em units
                maxWidth : 27, // maximum width of sub-menus in em units 
                extraWidth : 1// extra width can ensure lines don't sometimes turn over 
                // due to slight rounding differences and font-family 
            }).superfish(); // call supersubs first, then superfish, so that subs are 
        // not display:none when measuring. Call before initialising 
        // containing tabs for same reason.
    }
    
    if (typeof prettyPrint === 'function') {
        prettyPrint();
        $('.prettyprint').after('<div class="clear"></div>').hover(function () {
            if ($(this).css('width', 'auto').width() < $(this).parent().width()) {
                $(this).css('width', '100%');
            }
        }, function () {
        });
    }
    
    /**
     * Buttons
     */
    $('.button').each(function () {
            $(this).button({
                    icons : {
                        primary : $(this).attr('data-icon-primary') ? $(this).attr('data-icon-primary') : null, 
                        secondary : $(this).attr('data-icon-primary') ? $(this).attr('data-icon-secondary') : null
                    }, 
                    text : $(this).attr('data-icon-only') === 'true' ? false : true
                });
        });
    
    /**
     * Table Sorting, Row Selection and Pagination
     */
    if ($('.display').dataTable) {
        $('.display').dataTable({
                "bJQueryUI" : true,
                "sPaginationType" : "full_numbers"
            });
    }

    /**
     * Toolbar Buttons
     */
    $(".buttonset input").addClass('no-uniform');
    $(".buttonset").addClass('ui-corner-all').buttonset();

    /**
     * Skin select, file, checkbox and radio input elements
     */
    $(":checkbox:not(.no-uniform), :radio:not(.no-uniform), select:not(.no-uniform), :file:not(.no-uniform)").uniform();
    //$("select").uniform();
    //$.uniform.update();
    ///alert("xxx");


    /**
     * Skin Sliders

    $(":range").each(function () {
        var range = this,
        opts = {
            animate : true,
            value : $(this).val(),
            min : $(this).attr('min') ? parseInt($(this).attr('min'), 10) : 0,
            max : $(this).attr('max') ? parseInt($(this).attr('max'), 10) : 100,
            step : $(this).attr('step') ? parseInt($(this).attr('step'), 10) : 1,
            disabled : $(this).is(':disabled'),
            orientation : $(this).attr('data-orientation') === 'vertical' ? 'vertical' : 'horizontal',
            range : $(this).attr('data-range') === "true" ? true : ($(this).attr('data-range')in{
                    min : 1,
                    max : 1
                }
                 ? $(this).attr('data-range') : false),
            values : $(this).attr('data-values') ? $(this).attr('data-values').split(/,/) : null,
            slide : function (event, ui) {
                $(range).val(ui.value);
                if ($(range).attr('data-values')) {
                    $(range).attr('data-values', ui.values[0] + ',' + ui.values[1]);
                }
                $(range).change();
            }
        },
        slider = $('<div class="slider"></div>').slider(opts);
        $(this).before(slider).hide();
    });
    */
    /**
     * attach calendar to date inputs
     */
    //$("input[type=date]").datepicker({
    //    dateFormat : 'y-mm-dd'
    //});

    /**
     * Setup Nav tooltips

    $('a[title]').tooltip({
        effect: 'slide', offset: [0, 0], position: 'top center', layout: '<div><em/></div>',
        onBeforeShow: function() {
            this.getTip().each(function(){
                if ($.browser.msie) {
                    PIE.attach(this);
                }
            });
        },
        onHide: function() {
            this.getTip().each(function(){
                if ($.browser.msie) {
                    PIE.detach(this);
                }
            });
        }
    }).dynamic({
            bottom: { direction: 'down', bounce: true },
            right: { offset: [51, 0] }
    });
    */
    /**
     * Setup the Accordions
     */
    $(".accordion").accordion();
    
    /**
     * Setup the Tabs
     */
    $(".tabs").tabs().find('> .ui-tabs-nav').removeClass('ui-corner-all').addClass('ui-corner-top');
    
    $(".sidebar-tabs").tabs() 
        .addClass('ui-tabs-vertical ui-helper-clearfix') 
        .find('li').removeClass('ui-corner-top').addClass('ui-corner-left') 
        .parents('.sidebar-tabs') 
        .find('.ui-tabs-nav').removeClass('ui-corner-all').addClass('ui-corner-left')
        .parents('.sidebar-tabs') 
        .find('.ui-tabs-panel').removeClass('ui-corner-bottom').addClass('ui-corner-right');
    
    /**
     * Retrieve portlet state
     */
    var collapsed = [], 
    state = $.cookie && $.cookie('collapsedPortlets') ? $.cookie('collapsedPortlets') : '';
    collapsed = state.split(',');
    
    /**
     * Setup Portlets
     */
    $('.portlet').each(function () {
        var portlet = $(this), 
        id = portlet.attr('id'), 
        isCollapsed = $(this).hasClass('collapsible') && collapsed.includes(id);
        portlet.addClass('ui-widget ui-widget-content ui-corner-all' + (isCollapsed ? ' collapsed' : '')) 
        .find('> header:first') 
        .addClass('ui-widget-header ui-corner-top') 
        .parent() 
        .find('> section:last') 
        .addClass('ui-widget-content ui-corner-bottom') 
        .parent() 
        .filter('.collapsible') 
        .find('> header:first') 
        .append('<a href="#" class="portlet-collapse ui-corner-all" role="button"><span class="' + (isCollapsed ? 'ui-icon ui-icon-circle-plus' : 'ui-icon ui-icon-circle-minus') + '">Expand/Collapse</span></a>') 
        .find('.portlet-collapse').hover(function () {
                $(this).addClass('ui-state-hover');
            }, function () {
                $(this).removeClass('ui-state-hover');
            }) 
        .click(function () {
            if (portlet.hasClass('collapsed')) {
                $('span', this).removeClass('ui-icon-circle-plus').addClass('ui-icon-circle-minus');
                portlet.removeClass('collapsed').find('> section').css('display', 'none').slideDown(500, 'easeOutExpo');
                if (collapsed.includes(id)) {
                    collapsed.remove(id);
                    saveCollapsed(collapsed);
                }
            } else {
                $('span', this).removeClass('ui-icon-circle-minus').addClass('ui-icon-circle-plus');
                $(this).parent().next().slideUp(500, 'easeOutExpo', function () {
                        portlet.addClass('collapsed');
                        if (!collapsed.includes(id)) {
                            collapsed.push(id);
                            saveCollapsed(collapsed);
                        }
                    });
            }
            return false;
        });
    });

    /**
     * Textbox Placeholder
     */
    $('input[placeholder]').placeholder();

    /**
     * setup progress bars
     */
    $(".progress").each(function () {
        var val = parseInt($(this).attr("data-value"), 10);
        $(this).progressbar({
                value : val
            }).filter('[data-show-value=true]').find('div').append('<b>' + val + '%</b>');
    });

    /**
     * add close buttons to closeable message boxes
     */
    $(".message.closeable").prepend('<span class="message-close ui-icon ui-icon-circle-close"></span>')
    .find('.message-close')
    .click(function () {
        $(this).parent().fadeOut(function () {
                $(this).remove();
            });
    });

    /**
     * html element for the help popup
     */
    $('body').append('<div class="apple_overlay black" id="overlay"><iframe class="contentWrap" style="width: 100%; height: 500px"></iframe></div>');

    /**
     * this is the help popup
     */
    $("a.help[rel]").overlay({

        effect : 'apple',

        onBeforeLoad : function () {

            // grab wrapper element inside content
            var wrap = this.getOverlay().find(".contentWrap");

            // load the page specified in the trigger
            wrap.attr('src', this.getTrigger().attr("href"));
        }

    });

    /**
     * Form Validators
     */
    // Regular Expression to test whether the value is valid
    $.tools.validator.fn("[type=time]", "Please supply a valid time", function (input, value) {
        return(/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/).test(value);
    });

    //$.tools.validator.fn("[type=date]", "Please enter a valid date", function (input, value) {
        //re = /^[12][90][0-9][0-9]\-/[01]?[0-9]\-[0-3]?[0-9]$/;
    //    return(/(?:19|20\d{2})\/(?:0[1-9]|1[0-2])\/(?:0[1-9]|[12][0-9]|3[01])/).test(value);
    //});

    ///^[0-3]?[0-9]\/[01]?[0-9]\/[12][90][0-9][0-9]$/
    // /^\$/
    //
    $.tools.validator.fn("[data-equals]", "Value not equal with the $1 field", function (input) {
        var name = input.attr("data-equals"),
        field = this.getInputs().filter("[name=" + name + "]");
        return input.val() === field.val() ? true : [name];
    });

    $.tools.validator.fn("[minlength]", function (input, value) {
        var min = input.attr("minlength");

        return value.length >= min ? true : {
            en : "Please provide at least " + min + " character" + (min > 1 ? "s" : "")
        };
    });

    $.tools.validator.localizeFn("[type=time]", {
        en : 'Please supply a valid time'
    });

    /**
     * setup the validators
     */
    $(".has-validation").validator({
        position : 'bottom left',
        offset : [5, 0],
        messageClass : 'form-error',
        message : '<div><em/></div>'// em element is the arrow
    }).attr('novalidate', 'novalidate');

    /**
     * Animate search form in top-menu
     */
    $("#quick-search").focus(function () {
        $(this).parent().animate({
            width : '250px'
        }, 500, 'easeOutExpo');
    }).blur(function () {
        var base = this;
        setTimeout(function () {
            if ($(base)[0] !== document.activeElement) {
                $(base).parent().animate({
					width : '140px'
				}, 500, 'easeOutExpo');
            }
        }, 500);
    });

    /**
     * Fieldsets
     */
    $('fieldset.fieldset-buttons > legend').each(function () {
        $(this).css({
            marginLeft : (Math.round($(this).parent().width() / 2) - Math.round($(this).width() / 2)) + "px"
        });
    });

    /**
     * Main Content Resized
     */
    $('.main-content').resize(function () {
        (typeof $('.isotope').isotope === 'function') && $('.isotope').isotope();
        $('fieldset.fieldset-buttons > legend').each(function () {
            $(this).css({
                    marginLeft : (Math.round($(this).parent().width() / 2) - Math.round($(this).width() / 2)) + "px"
                });
            });  
    });



    /**
     * Navigation
    */
    if ($.cookie) {
        if ($.cookie('menuCollapsed') === '1') {
            $('.main-section > nav').addClass('collapsed').find('a.chevron').append('<span class="ui-icon-appmenu-right"></span>');
            $('.main-content').resize();
            $('#mainmenu ul.dropdown-vertical').css({"width":appmenuCollapsed2});
            $('#mainmenu ul.dropdown li a').css({"overflow":"hidden"});
        } else if ($.cookie('menuCollapsed') === '0') {
            $('.main-section > nav').removeClass('collapsed').find('a.chevron').append('<span class="ui-icon-appmenu-left"></span>');
            $('.main-content').resize();
            $('#mainmenu ul.dropdown-vertical').animate({width:appmenuCollapsed1}, 500);
            $('#mainmenu ul.dropdown li a').css({"overflow":"visible"});
        } else {
			if ($('.main-section > nav').hasClass('collapsed')) {
	            $('.main-section > nav').find('a.chevron').append('<span class="ui-icon-appmenu-right"></span>');
            $('#mainmenu ul.dropdown-vertical').css({"width":appmenuCollapsed2});
            $('#mainmenu ul.dropdown li a').css({"overflow":"hidden"});
			} else {
	            $('.main-section > nav').find('a.chevron').append('<span class="ui-icon-appmenu-right"></span>');
            $('#mainmenu ul.dropdown-vertical').css({"width":appmenuCollapsed2});
            $('#mainmenu ul.dropdown li a').css({"overflow":"hidden"});
			}
		}
    }

    $('.main-section > nav a.chevron').click(function () {
        if ($(this).parent().toggleClass('collapsed').hasClass('collapsed')) {
            $('span', this).removeClass('ui-icon-appmenu-left').addClass('ui-icon-appmenu-right');
            $.cookie && $.cookie('menuCollapsed', '1', {
                expires : adminCookieExpiry,
                path : "/"
            });
            $('#mainmenu ul.dropdown-vertical').css({"width":appmenuCollapsed2});
            $('#mainmenu ul.dropdown li a').css({"overflow":"hidden"});

        } else {
            $('span', this).removeClass('ui-icon-appmenu-right').addClass('ui-icon-appmenu-left');
            $.cookie && $.cookie('menuCollapsed', '0', {
                expires : adminCookieExpiry,
                path : "/"
            });
            $('#mainmenu ul.dropdown-vertical').animate({width:appmenuCollapsed1}, 500);
            //$('#mainmenu ul.dropdown-vertical').css({"width":appmenuCollapsed1});
            $('#mainmenu ul.dropdown li a').css({"overflow":"visible"});

        }
        $('.main-content').resize();

        return false;
    });

    if ($(sortableName).sortable) {
        $(sortableName).sortable({
            cursor : 'move',
            revert : 500,
            opacity : 0.7,
            appendTo : 'body',
            handle : 'header',
            items : '[draggable=true]',
            placeholder : 'portlet-placeholder grid_2',
            forcePlaceholderSize : true,
            update : function (event, ui) {
                $.cookie && $.cookie(sortableCookie, $(this).sortable("toArray"), {
                        expires : adminCookieExpiry,
                        path : "/"
                    });
            }
        }).disableSelection();
    }

    /**
     * restore the order of sortable widgets
     */
    $.cookie && restoreOrder(sortableName, sortableCookie);

    /**
     * restore saved background
     */
    $.cookie && $.cookie('background') && changeBackground($.cookie('background'));

    /**
     * restore saved css
     */
    $.cookie && $.cookie('css') && changeUicolor($.cookie("css"));
});

/**
 * Restores the sortable order from a cookie
 */
function restoreOrder(sortable, cookieName) {
    var list = $(sortable);
    if (!list) {
        return;
    }

    // fetch the saved cookie
    var cookie = $.cookie(cookieName);
    if (!cookie) {
        return;
    }

    // create array from cookie
    var IDs = cookie.split(","),

    // fetch current order
    items = list.sortable("toArray"),

    // create associative array from current order
    current = [];
    for (var v = 0; v < items.length; v++) {
        current[items[v]] = items[v];
    }

    for (var i = 0, n = IDs.length; i < n; i++) {
        // item id from saved order
        var itemID = IDs[i];

        if (itemID in current) {
            // select the item according to the saved order and reappend it to the list
            $(sortable).append($(sortable).children("#" + itemID));
        }
    }
}

function saveCollapsed(collapsed) {
    $.cookie && $.cookie('collapsedPortlets', collapsed, {
        expires : adminCookieExpiry,
        path : "/"
    });
}

function changeBackground(newBackground) {
    $('body').removeClass($.cookie('background')).addClass(newBackground);
    $.cookie && $.cookie('background', newBackground, {
        expires : adminCookieExpiry,
        path : "/"
    });
}

function changeUicolor(newcss) {
    $('link.uicolor').attr('href', newcss);
    $.cookie && $.cookie('css', newcss, {
        expires : adminCookieExpiry,
        path : "/"
    });
}

/**
 * Custom jQuery Tools Overlay Effect, thanks to the great guys at flowplayer.org :)
 */
// create custom animation algorithm for jQuery called "drop"
$.easing.drop = function (x, t, b, c, d) {
    return - c * (Math.sqrt(1 - (t /= d) * t) - 1) + b;
};

// loading animation
$.tools.overlay.addEffect("drop", function (css, done) {

    // use Overlay API to gain access to crucial elements
    var conf = this.getConf(),
    overlay = this.getOverlay();

    // determine initial position for the overlay
    if (conf.fixed) {
        css.position = 'fixed';
    } else {
        css.top += $(window).scrollTop();
        css.left += $(window).scrollLeft();
        css.position = 'absolute';
    }

    // position the overlay and show it
    overlay.css(css).show();

    // begin animating with our custom easing
    overlay.animate({
            top : '+=55',
            opacity : 1,
            width : '+=20'
        }, 400, 'drop', done);

    /* closing animation */
}, function (done) {
    this.getOverlay().animate({
            top : '-=55',
            opacity : 0,
            width : '-=20'
        }, 300, 'drop', function () {
            $(this).hide();
            done.call();
        });
});

/**
 * Utility functions
 */
if (!Array.indexOf) {
    Array.prototype.indexOf = function (obj) {
        for (var i = 0; i < this.length; i++) {
            if (this[i] === obj) {
                return i;
            }
        }
        return - 1;
    };
}
Array.prototype.includes = function (value) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] === value) {
            return true;
        }
    }
    return false;
};
Array.prototype.remove = function (value) {
    var i = this.indexOf(value);
    if (i !== -1) {
        this.splice(i, 1);
    }
};





	(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input type=\"text\">" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button type='button'></button>" )
					.attr( "tabIndex", -1 )
					//.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-carat-1-s"
						},
						text: false
					})
					.removeClass( "" )
					.addClass( "ui-button-autocomplete" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );
