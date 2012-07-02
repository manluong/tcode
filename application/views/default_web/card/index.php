<link rel="stylesheet" href="/resources/template/<?=get_template()?>/css/nanoscroller.css" />
<script type="text/javascript" src="/resources/addon/overthrow.min"></script>
<script type="text/javascript" src="/resources/addon/contacts.js"></script>
<script>
$(document).ready(function(){
	//LOAD CONTACT INFO FIRST
	hide_empty_contact();
	load_contact_info(1);

	$(".nano").nanoScroller();
});
</script>
<?php
//echo '<pre>';
//print_r($list);
//echo '</pre>';
?>
<div id="boxes" style="margin-bottom:60px;">
	<!-- Start Contacts Panels -->
	<div id="contactsPanel">
		<div class="fix"></div>
		<!-- Start Contacts' list -->
		<div class="leftPanel">
			<input type="text" id="searchContacts" onclick="value =''" onblur="(value =='' ? value='Search contacts':value=value)" value="Search contacts" />
			<nav>
				<a onclick="contact_fillter(0)" href="javascript:void(0)" class="active">all</a>
				<a onclick="contact_fillter(1)" href="javascript:void(0)">staff</a>
				<a onclick="contact_fillter([2,3])" href="javascript:void(0)">customers</a>
				<a onclick="contact_fillter(4)" href="javascript:void(0)">vendors</a>
				<a onclick="contact_fillter(-1)" href="javascript:void(0)">rest</a>
			</nav>
			<div class="test" style="display: none;">

			</div>

			<div class="test">

			</div>
			<div style="height:437px;" class="nano">
				<ul id="contact_list" class="addressBook content">
					<li class="letter staff customers vendors a-title">a</li>
					<?php
						//TODO: This list should be implemented in the browser side. e.g. use $.get to pull list data from server.
						$current_alphabet = 0;
						$alphabets = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

						$list_count = count($list);
						for($start=0; $start<$list_count; $start++) {
							$l = $list[$start];

							//if the name does not match the current alphabet, it means it's time to move on to the next alphabet
							if (strtolower(substr($l['first_name'], 0, 1)) != $alphabets[$current_alphabet]) {
								$current_alphabet++;
								if ($current_alphabet == 26) break;
								echo '<li class="letter staff customers vendors ',$alphabets[$current_alphabet],'-title">',$alphabets[$current_alphabet],'</li>';
								$start--;
								continue;
							}

							if (!isset($l['role']['name'])) {
								$role = '';
							} else {
								$role = strtolower($l['role']['name']);
							}

							echo '<li onclick="load_contact_info('.$l['id'].')" class="contact ',$role,' ',$alphabets[$current_alphabet],'-contact">';
								echo '<div class="contactType">',ucfirst($role),'</div>';
								echo '<img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" />';
								echo '<div class="info hasCompany"><a href="/card/view/',$l['id'],'">',$l['first_name'],' ',$l['last_name'],'</a></div>';
								echo '<div class="company">',$l['organization_name'],'</div>';
							echo '</li>';
						}
					?>
				</ul>
			</div>
		</div>
		<!-- End Contacts' list -->

		<div class="rightPanel" id="rightPanel"></div>

		<div id="add_new_contact">
			<a href="<?=site_url('card/add/')?>" style="width:106px; height:10px;line-height:10px;" class="btn btn-inverse">ADD NEW CONTACT</a>
		</div>

	</div>
	<!-- End Contacts Panels -->
</div>
