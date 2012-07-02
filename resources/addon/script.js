/* Author: roywalker.me

*/

// Search input functions (header)
$(".optionsDropdownOpener").click(function(){
	if($(".optionsDropdownPadder").is(":visible")){
		$(".optionsDropdownPadder").fadeOut(500);
	}else{
		$(".optionsDropdownPadder").fadeIn(500);
	}

	return false;
});

$(".optionsDropdownPadder").mouseleave(function(){
	if($(".optionsDropdownPadder").is(":visible")){
		$(".optionsDropdownPadder").fadeOut(500);
	}
});

// User links functions (header)
$(".dropdownAvatarOpener").click(function(){
	if($(".userDropdownPadding").is(":visible")){
		$(".userDropdownPadding").fadeOut(500);
	}else{
		$(".userDropdownPadding").fadeIn(500);
	}

	return false;
});

$(".userDropdownPadding").mouseleave(function(){
	if($(".userDropdownPadding").is(":visible")){
		$(".userDropdownPadding").fadeOut(500);
	}
});

$(".userDropdownPadding .settings").click(function(){
	$("#settings, #overlay").fadeIn("fast");
	$(".userDropdownPadding").hide();

	return false;
});

// Breadcrumb favorite star effect
$("#breadcrumb #favoriteIcon").click(function(){
	if ($(this).hasClass("on")){
		$(this).removeClass("on").addClass("off");
		$(this).attr('title','Add to favorites');
	} else {
		$(this).removeClass("off").addClass("on");
		$(this).attr('title','Remove from favorites');
	}

	return false;
});

// Tabs navigation
$(".tabsNavigation li a").click(function(){
	$(".tabsNavigation li").removeClass("active");
	$(this).parents("li").addClass("active");

	// Ajax call to load new sections goes here
	var url = $(this).attr('href');
	console.log('loading: '+url);
	$.pjax({
		url: url,
		container: '#main',
		timeout: 5000
	});

	return false;
});

$(".tabsNavigation, .tabsNavigationPagers, #bottomMenus").mouseenter(
	function(){
		$(".tabsNavigationPagers").fadeIn();
	}
);

$("body.fixedwidth, #main").mouseenter(
	function(){
		$(".tabsNavigationPagers").fadeOut();
	}
);

// Bottom menu (history & favorites)
$("div#main #bottomMenus a").click(function(){
	$("div#main #bottomMenus a").removeClass("active");
	$(this).addClass("active");

	// Ajax call to load new sections goes here

	return false;
});

// Scroll


$('.appsIconBottom, .appsIconTop').click(function(){
	return false;
});

$('.appsIconBottom').hover(function() {
    moveABitBottom();
    interval = setInterval(moveABitBottom, 1000);
},
function() {
    clearInterval(interval);
});

$('.appsIconTop').hover(function() {
    moveABitTop();
    interval = setInterval(moveABitTop, 1000);
},
function() {
    clearInterval(interval);
});

function moveABitBottom(){
	var scrolltop = $(".tabsNavigation").scrollTop();
	var newTop = scrolltop + 65;

	$(".tabsNavigation").animate({scrollTop:newTop}, 800);
}

function moveABitTop(){
	var scrolltop = $(".tabsNavigation").scrollTop();
	var newTop = scrolltop - 65;

	$(".tabsNavigation").animate({scrollTop:newTop}, 800);
}

// Activity app
$(".timeline .entry").hover(function(){
	$(this).find(".notes a.regular").show();
}).mouseleave(function(){
	$(this).find(".notes a.regular").hide();
});

$(".openNotes").click(function(){

	$("#commentsBar").parents(".entry").find(".notes a").removeClass("alwaysVisible");
	$("#commentsBar").parents(".entry").find(".notes a.new").removeClass("new").addClass("regular");
	// Change the link's label for no new notes and add quantity of notes

	$(this).parents(".notes").find("a.openNotes").addClass("alwaysVisible");

	$("#commentsBar").appendTo($(this).parents(".entry"));
	$("#commentsBar").show();

	// Ajax call to load comments

	return false;
});

$(".notes .remove").click(function(){
	$(this).parents(".entry").slideToggle();

	// Ajax call to remember the hidden item
});

$("a.previousMessages").click(function(){
	// Ajax call to load previous comments

	return false;
});

$("#commentsBar .writeANote").focus(function(){
	$(this).animate({height:'+50'}, 500);
});

$("#breadcrumb select").change(function () {
	value = $("#breadcrumb select option:selected").val();

	if (value == "staffstatusupdates") {
		$(".allUpdates").hide();
		$(".staffStatusUpdates").show();
	} else {
		$(".staffStatusUpdates").hide();
		$(".allUpdates").show();
	}

	})
.change();

$(".ShareYourThoughts select").change(function () {
	$('#markstatus').attr('checked', true);
});

$(".ShareYourThoughts textarea").focus(function(){
	$(this).parent().addClass("active");
	$(".ShareYourThoughts .options").show();
});

var observe;
if (window.attachEvent) {
    observe = function (element, event, handler) {
        element.attachEvent('on'+event, handler);
    };
}
else {
    observe = function (element, event, handler) {
        element.addEventListener(event, handler, false);
    };
}
function initResize () {
    var text = document.getElementById('shareTextarea');
    function resize () {
        text.style.height = 'auto';
        text.style.height = text.scrollHeight+'px';
        $(".ShareYourThoughts .options").animate({top: text.scrollHeight + 24}, 0);
        $(".ShareYourThoughts.active").animate({height: text.scrollHeight + 51}, 0);
    }
    /* 0-timeout to get the already changed text */
    function delayedResize () {
        window.setTimeout(resize, 0);
    }
    observe(text, 'change',  resize);
    observe(text, 'cut',     delayedResize);
    observe(text, 'paste',   delayedResize);
    observe(text, 'drop',    delayedResize);
    observe(text, 'keydown', delayedResize);


    resize();
}

$(".ShareYourThoughts input[type=text]").click(function(){
	$(this).select();
	$(this).addClass("focus");
});

$(".ShareYourThoughts input[type=text]").focus(function(){
	if (!$(this).hasClass("focus")) {
		$(this).animate({width:'+=53'}, 500 );
		$(this).addClass("focus");
	}
}).blur(function(){
	$(this).animate({width:'-=53'}, 500 );
	$(this).removeClass("focus");
});

$(".ShareYourThoughts input[type=text]").keydown(function(){
	$('#markstatus').attr('checked', true);
});

// Contacts app

$("#menuTabCard a").click(function(){
	$("#menuTabCard li").removeClass("active");
	$(this).parent().addClass("active");

	if ($(this).parent().hasClass("details")) {
		$("#contactsPanel .fix").show();
	} else {
		$("#contactsPanel .fix").hide();
	}

	// Change label of the breadcrumb
	var label = $(this).html();
	$("#breadcrumb .tabsContent").html(label);

	// Create function to load the new content for the tab

	return false;
});

// Settings

$("#settings .icons a").click(function(){
	$("#settings .step1").hide();
	$("#settings .step2").fadeIn("fast");

	return false;
});

$("#settings .cancel").click(function(){
	$("#settings .step2").hide();
	$("#settings .step1").fadeIn("fast");

	return false;
});

$("#overlay, .closeModal").click(function(){
	$("#settings, #overlay").fadeOut("fast");

	return false;
});

$('.nano').nanoScroller();