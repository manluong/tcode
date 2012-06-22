/*------- Upload avatar -------*/
$(document).ready(function(){
	$('#contact_close').click(function(){
		$("#upload_avatar").overlay().close();
	});
});

function load_upload_form(){
	$("#upload_avatar").overlay({
	  mask: {
			color: '#000',
			loadSpeed: 200,
			opacity: 0.3
	  },
	  top: '10%',
	});
	$("#upload_avatar").overlay().load();
}

/*------- End Upload avatar -------*/

//AJAX LOAD CONTACT INFO
function load_contact_info(id){
	var url = '/card/ajax_contact_info';
	$.post(url,{
			id : id,
		},function(data){
			$('#rightPanel').html(data);
		}
	);
}

//CONTACT LIST FILLTER
function contact_fillter(role_id){
	var url = '/card/contact_fillter';
	$.post(url,{
			role_id : role_id,
		},function(data){
			$('#contact_list').html(data);
			hide_empty_contact();
		}
	);
}

function hide_empty_contact() {
	$(".addressBook li.letter").each(function(index) {
		var letter = $(this).html();

		shouldhideit = 0;
		$(".addressBook ."+letter+"-contact:visible").each(function(indexInterior){
			shouldhideit++;
		});

		if (shouldhideit > 0) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
}

//SELECT CONTACT
$(document).ready(function(){

	$('nav a').click(function(){
		$('nav a').removeClass('active');
		$(this).addClass('active');
	});

	//ADD ACTICE FOR CONTACT SELECTED
	$(".addressBook li").click(function(){
		if (!$(this).hasClass("letter")) {
			$(".addressBook li").removeClass("active");
			$(this).addClass("active");
		}
		return false;
	});

	// Search input
	$("#searchContacts").focus(function(){
		if ($(this).val() == "Search contacts") {
			$(this).val("");
		}
	}).blur(function(){
		if ($(this).val() == "") {
			$(this).val("Search contacts");
		}
	});

	$(".input-file").change(function (){
	   $(".wrapperFile").addClass("changed");
	});

	// Various
	$(".addNewField").click(function(){
		var newField = $('.sampleToDuplicate').clone();

		// Change labels and IDs here
		newField.removeClass("sampleToDuplicate").find("label").html("Other Field")

		$('.sampleToDuplicate').after(newField);

		return false;
	});

	// Search contacts input
	$.expr[':'].containsIgnoreCase = function(n,i,m){
		return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase())>=0;
	};

	$("#searchContacts").keyup(function(){

		$(".addressBook").find("li.contact ").hide();
		var data = this.value.split(" ");
		var jo = $(".addressBook").find("li.contact");
		$.each(data, function(i, v){

			 //Use the new containsIgnoreCase function instead
			 jo = jo.filter("*:containsIgnoreCase('"+v+"')");
		});

		jo.show();

		$(".addressBook li.letter").each(function(index) {
				var letter = $(this).html();

				shouldhideit = 0;
				$(".addressBook ."+letter+"-contact:visible").each(function(indexInterior){
					shouldhideit++;
				});

				if (shouldhideit > 0) {
					$(this).show();
				} else {
					$(this).hide();
				}

			});

	}).focus(function(){
		this.value="";
		$(this).css({"color":"black"});
		$(this).unbind('focus');
	}).css({"color":"#C0C0C0"});

});

/*------- CONTACT VIEW -------*/
	$(document).ready(function(){
		$('#btn_view_active').click(function(){
			$('#view_active').hide();
			$('#edit_active').show();
		});
		$('#btn_view_pass').click(function(){
			$('#view_pass').hide();
			$('#edit_pass').show();
		});
		
	});

	function ajax_change_status(id){
		var active = $('#select_active').val();
		var url = '/card/ajax_change_status';
		
		$.post(url,{
				id : id,
				active : active,
			},function(data){
				$('#customer_detail').html(data);
			}
		);
	}
	
	function ajax_change_pass(id){
		var pass = $('#access_pass').val();
		var expiry_date = $('#expiry_date').val();
		var url = '/card/ajax_change_pass';
		var card_id = $('#access_card_id').val();
		
		$.post(url,{
				id : id,
				pass : pass,
				expiry_date : expiry_date,
				card_id : card_id,
			},function(data){
				$('#edit_pass').hide();
				$('#view_pass').show();
				$('#view_pass').html(data);
			}
		);
	}
	
	function save_role(id){
		var role = $('#addon_role').val();
		var url = '/card/save_role';
		$.post(url,{
				id : id,
				role : role,
			},function(data){
				if(data != ''){
					window.location = '/card/view/'+id;
				}
			}
		);
		
	}
/*------- END CONTACT VIEW -------*/
