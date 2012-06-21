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
$(".appsIconBottom").click(function(){
	var scrolltop = $(".tabsNavigation").scrollTop();
	var newTop = scrolltop + 65;

	$(".tabsNavigation").animate({scrollTop:newTop}, 500);


	return false;
});

$(".appsIconTop").click(function(){
	var scrolltop = $(".tabsNavigation").scrollTop();
	var newTop = scrolltop - 65;

	$(".tabsNavigation").animate({scrollTop:newTop}, 500);


	return false;
});

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

	$(this).parents(".notes").find("a").addClass("alwaysVisible");

	$("#commentsBar").appendTo($(this).parents(".entry"));
	$("#commentsBar").show();

	// Ajax call to load comments

	return false;
});

$("a.previousMessages").click(function(){
	// Ajax call to load previous comments

	return false;
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

$(".ShareYourThoughts input[type=text]").click(function(){
	$(this).select();
	$(this).addClase("focus");
	$(this).animate({width:'+=53'}, 500 );
});

$(".ShareYourThoughts input[type=text]").focus(function(){
	if (!$(this).hasClass("focus")) {
		$(this).animate({width:'+=53'}, 500 );
		$(this).addClase("focus");
	}
}).blur(function(){
	$(this).animate({width:'-=53'}, 500 );
	$(this).removeClase("focus");
});

$(".ShareYourThoughts input[type=text]").keydown(function(){
	$('#markstatus').attr('checked', true);
});




