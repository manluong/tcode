<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/nanoscroller.css" />
<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.nanoscroller.min.js"></script>
<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/jquery.smooth-scroll.min.js"></script>
<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/overthrow.min"></script>
<script type="text/javascript" src="/resources/template/<?=get_template()?>/js/ga.js"></script>


<script>
$(document).ready(function(){
	$(".nano").nanoScroller();
});
</script>
<div id="boxes" style="margin-bottom:60px;">
	<!-- Start Contacts Panels -->
	<div id="contactsPanel">
		<div class="fix"></div>
		<!-- Start Contacts' list -->
		<div class="leftPanel">
			<input type="text" id="searchContacts" onclick="value =''" onblur="(value =='' ? value='Search contacts':value=value)" value="Search contacts" />
			<nav>
				<a href="#" class="active">all</a>
				<a href="#">staff</a>
				<a href="#">customers</a>
				<a href="#">vendors</a>
				<a href="#">rest</a>
			</nav>
			<div class="test" style="display: none;">
			
			</div>
			
			<div class="test">
			
			</div>
			<div style="height:437px;" class="nano">
				<ul class="addressBook content">
					<li class="letter staff customers vendors a-title">a</li>
					<li class="contact staff a-contact">
						<div class="contactType">Staff</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Albert A.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact customers a-contact">
						<div class="contactType">Customer</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info">Albert A.</div>
					</li>
					<li class="contact staff a-contact">
						<div class="contactType">staff</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Albert B.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact vendors noBottom a-contact">
						<div class="contactType">Vendor</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Albert C.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="letter staff customes rest vendors b-title">b</li>
					<li class="contact staff b-contact">
						<div class="contactType">Staff</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben Smith.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact rest b-contact">
						<div class="contactType">rest</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben X.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact vendors b-contact">
						<div class="contactType">Vendor</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info">Ben A.</div>
					</li>
					<li class="contact customers b-contact">
						<div class="contactType">Customer</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben Z.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact staff b-contact">
						<div class="contactType">Staff</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben ZZ.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact vendors b-contact">
						<div class="contactType">Vendor</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben A.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact rest b-contact">
						<div class="contactType">Rest</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben A.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
					<li class="contact staff noBottom b-contact">
						<div class="contactType">Staff</div>
						<image width="32"  alt="avatar" src="<?=site_url('resources/template/default_web/img')?>/avatar.jpg"/>
						<div class="info hasCompany">Ben A.</div>
						<div class="company">McDonalds, Co.</div>
					</li>
				</ul>
			</div>
		</div>
		<!-- End Contacts' list -->
		
		<div class="rightPanel">
			<div id="user_profile">
				<div id="user_avatar"><image alt="avatar" src="<?=site_url('resources/template/default_web/img/invoice')?>/invoice-avatar.jpg"/></div>
				<div id="user_info">
					<ul>
						<li class="user_sex">Mr.</li>
						<li class="user_name">Albert Z</li>
						<li class="user_position">Facebook Inc. CEO</li>
					</ul>
				</div>
			</div>
			<div id="contact_info">
				<ul>
					<li>
						<span class="input_data_label">Phone</span>
						<span class="fillter_input">555-23332</span>
					</li>
					<li>
						<span class="input_data_label">Office</span>
						<span class="fillter_input">555-23332</span>
					</li>
					<li>
						<span class="input_data_label">Email</span>
						<span class="fillter_input">555-23332</span>
					</li>
					<li style="margin:10px 0 0 121px;">
						<button style="width:50px; height:20px;line-height:10px;" class="btn btn-inverse">View</button>
					</li>	
				</ul>
			</div>
		</div>
		
	</div>
	<!-- End Contacts Panels -->
</div>
