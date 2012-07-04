<!--<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/nanoscroller.css" />-->
<script type="text/javascript" src="/resources/addon/overthrow.min.js"></script>
<script type="text/javascript" src="/resources/addon/contacts.js"></script>
<script>
$(document).ready(function(){
	//LOAD CONTACT INFO FIRST
	hide_empty_contact();
	load_contact_info(1);
	jQuery("#breadcrump").prepend('<div class="rightBar"><button class="btn btn-inverse">NEW</button></div>');
});
</script>
<!-- Start Contacts' list -->
		<div id="contactsPanel">
			<input style="color: rgb(192, 192, 192);" id="searchContacts" onclick="value =''" onblur="(value =='' ? value='Search contacts':value=value)" value="Search contacts" type="text">
			<nav>
				<a onclick="contact_fillter(0)" href="javascript:void(0)" class="active">all</a>
				<a class="" onclick="contact_fillter(1)" href="javascript:void(0)">staff</a>
				<a class="" onclick="contact_fillter([2,3])" href="javascript:void(0)">customers</a>
				<a class="" onclick="contact_fillter(4)" href="javascript:void(0)">vendors</a>
				<a class="" onclick="contact_fillter(-1)" href="javascript:void(0)">rest</a>
			</nav>

			<div class="leftPanel">
					<ul style="right: -15px;" id="contact_list" class="addressBook content">
						<li class="letter staff customers vendors a-title">a</li>
						<li onclick="load_contact_info(228)" class="contact client a-contact">

	                    <div class="contactType">Client</div>
	                    <img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" width="32">
	                    <div class="info hasCompany"><a href="http://apple.8force.net/card/view/228">aaa cccc</a></div>
	                    <div class="company"></div></li><li onclick="load_contact_info(237)" class="contact client (additional) a-contact">

	                    <div class="contactType">Client (additional)</div>
	                    <img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" width="32">
	                    <div class="info hasCompany"><a href="http://apple.8force.net/card/view/237">aaa </a></div>
	                    <div class="company"></div></li><li onclick="load_contact_info(243)" class="contact client a-contact">

	                    <div class="contactType">Client</div>
	                    <img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" width="32">
	                    <div class="info hasCompany"><a href="http://apple.8force.net/card/view/243">aaa aaa</a></div>
	                    <div class="company">aaa</div></li><li onclick="load_contact_info(244)" class="contact client (additional) a-contact">

	                    <div class="contactType">Client (additional)</div>
	                    <img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" width="32">
	                    <div class="info hasCompany"><a href="http://apple.8force.net/card/view/244">aaa aaa</a></div>
	                    <div class="company">aaa</div></li><li onclick="load_contact_info(256)" class="contact client a-contact">

	                    <div class="contactType">Client</div><img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32">
	                    <div class="info hasCompany"><a href="http://apple.8force.net/card/view/256">Andy Lau</a></div>
	                    <div class="company">Andy Co.</div></li><li onclick="load_contact_info(254)" class="contact  a-contact active">
	                    <div class="contactType"></div>

	                    <img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32"><div class="info hasCompany"><a href="http://apple.8force.net/card/view/254">Andy Lau</a></div><div class="company">Andy Co.</div></li><li onclick="load_contact_info(255)" class="contact  a-contact"><div class="contactType"></div><img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32"><div class="info hasCompany"><a href="http://apple.8force.net/card/view/255">Andy Lau</a></div><div class="company">Andy Co.</div></li><li onclick="load_contact_info(149)" class="contact  a-contact"><div class="contactType"></div><img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32"><div class="info hasCompany"><a href="http://apple.8force.net/card/view/149">Andyyyy Anthonyyyyy</a></div><div class="company">ABC Co.</div></li><li style="display: none;" class="letter staff customers vendors b-title">b</li><li class="letter staff customers vendors c-title">c</li><li onclick="load_contact_info(222)" class="contact client c-contact"><div class="contactType">Client</div><img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32"><div class="info hasCompany"><a href="http://apple.8force.net/card/view/222">Customer One</a></div><div class="company"></div></li><li onclick="load_contact_info(224)" class="contact client c-contact"><div class="contactType">Client</div></li>				</ul>

				<div class="pane"><div style="height: 50px;" class="slider"></div></div>


			</div>
			<div class="rightPanel" id="rightPanel">

				<div id="quickjump">
					<div class="quickjump_select">
						<div class="btn-group">
						  <button class="btn btn-inverse">CUSOTMER</button>
						  <button class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
						    <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu">
							<li><a href="#"><i class="icon-pencil"></i> New Invoice</a></li>
							<li><a href="#"><i class="icon-trash"></i> New Quotation</a></li>
							<li><a href="#"><i class="icon-ban-circle"></i> View Invoices</a></li>
						  </ul>
						</div>
					</div>
					<div class="quickjump_card">
					<div class="quickjump_avatar avatar rounded20 fl mr5"><img width="40" height="40" class="rounded20" alt="" src="/resources/template/default_web/img/avatar.jpg"></div>
						<div class="quickjump_info">
							<ul>
								<li class="quickjump_title">Mr.</li>
								<li class="quickjump_name">Albert Z</li>
								<li class="quickjump_co">Facebook Inc. <span class="quickjump_position">CEO</span></li>
							</ul>
						</div>
					</div>
				</div>

	        	<div class="dataV">
	        		<div class="dataVL"><div class="dataVT">Phone</div><div class="dataVD">987654</div></div>
	       			<div class="dataVL"><div class="dataVT">Office</div><div class="dataVD">987654321</div></div>
	       			<div class="dataVL"><div class="dataVT">Email</div><div class="dataVD">info@albert.com</div></div>
	       			<div class="dataVL"><div class="dataVT">&nbsp;</div><div class="dataVD"><button class="btn btn-inverse" href="#">View</button></div></div>
	        	</div>

    		</div>
		</div>
		<!-- End Contacts' list -->