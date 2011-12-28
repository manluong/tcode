<script src="/resources/addon/jquery.tinysort.min.js" type="text/javascript"></script>

<style>
	.tags ul{
		margin:1em 0;
		padding:.5em 10px;
		text-align:center;
		background-color:#71b5e9;
		}
	.tags li{
		margin:0;
		padding:0;
		list-style:none;
		display:inline-block;
		}
	.tags li a{
		text-decoration:none;
		color:#fff;
		padding:0 2px;
		}
	.tags li a:hover{
		color:#cff400;
		}

	.tag1{font-size:100%;}
	.tag2{font-size:120%;}
	.tag3{font-size:140%;}
	.tag4{font-size:160%;}
	.tag5{font-size:180%;}

	/* alternative layout */

	.tags .alt{
		text-align:left;
		padding:0;
		background:none;
		}
	.tags .alt li{
		padding:2px 10px;
		background:#efefef;
		display:block;
		}
	.tags .alt .tag1,
	.tags .alt .tag2,
	.tags .alt .tag3,
	.tags .alt .tag4,
	.tags .alt .tag5{font-size:100%;}
	.tags .alt .tag1{background:#7cc0f4;}
	.tags .alt .tag2{background:#67abe0;}
	.tags .alt .tag3{background:#4d92c7;}
	.tags .alt .tag4{background:#3277ad;}
	.tags .alt .tag5{background:#266ca2;}

	#tag_controls {
		padding:5px;
	}
	#tag_controls a {
		display:block;
		margin:3px;
	}
</style>

<?php

//get distinct count values
$count = array_reverse(extract_distinct_values($tags, 'tag_count'));

//for each distinct count value, assign them to a tag class. starting from the smallest count value
$levels = array();
$x = 1;
foreach($count AS $c) {
	$levels[$c] = 'tag'.$x;
	if ($x<5) $x++;
}


echo '<div class="tags" id="tags"><ul>';
foreach($tags AS $t) {
	echo '<li class="'.$levels[$t['tag_count']].'"><a href="/tags/?search='.$t['tag'].'">'.$t['tag'].'</a></li>';
}
echo '</ul></div>';
echo '<div id="tag_controls"></div>';
?>



<script>
$(document).ready(function(){

	// create a style switch button
	var switcher = $('<a href="javascript:void(0)" class="btn">Change appearance</a>').toggle(
		function(){
			$("#tags ul").hide().addClass("alt").fadeIn("fast");
		},
		function(){
			$("#tags ul").hide().removeClass("alt").fadeIn("fast");
		}
	);
 	$('#tag_controls').append(switcher);

	// create a sort by alphabet button
	var sortabc = $('<a href="javascript:void(0)" class="btn">Sort alphabetically</a> | ').toggle(
		function(){
			$("#tags ul li").tsort({order:"asc"});
		},
		function(){
			$("#tags ul li").tsort({order:"desc"});
		}
		);
 	$('#tag_controls').append(sortabc);

	// create a sort by alphabet button
	var sortstrength = $('<a href="javascript:void(0)" class="btn">Sort by strength</a><br />').toggle(
		function(){
			$("#tags ul li").tsort({order:"desc",attr:"class"});
		},
		function(){
			$("#tags ul li").tsort({order:"asc",attr:"class"});
		}
		);
 	$('#tag_controls').append(sortstrength);

	sortabc.click();

});
</script>