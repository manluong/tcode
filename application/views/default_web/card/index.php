<!--
<div class="container-fluid">
	<table class="table table-bordered table-condensed table-striped">
		<thead>
			<tr>
				<td>ID</td>
				<td>Name</td>
				<td>Action</td>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($list AS $l) {
				echo '<tr>';
				echo '<td>',$l['id'],'</td>';
				echo '<td><a href="/card/view/',$l['id'],'">',$l['first_name'],' ',$l['last_name'],'</a></td>';
				echo '<td><a href="/card/edit/',$l['id'],'">Edit</a></td>';
				echo '</tr>';
			}
		?>
		</tbody>
	</table>
</div>
-->

<!-- Start Contacts Panels -->
<div id="contactsPanel">

	<div class="fix"></div>
	<!-- Start Contacts' list -->
	<div class="leftPanel">
		<input type="text" id="searchContacts" value="Search contacts" />
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
		<ul class="addressBook">
			<li class="letter staff customers vendors a-title">a</li>
			<?php
				$current_alphabet = 0;
				$alphabets = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

				$list_count = count($list);
				for($start=0; $start<$list_count; $start++) {
					$l = $list[$start];

					if (strtolower(substr($l['first_name'], 0, 1)) != $alphabets[$current_alphabet]) {
						$current_alphabet++;
						echo '<li class="letter staff customers vendors ',$alphabets[$current_alphabet],'-title">',$alphabets[$current_alphabet],'</li>';
						$start--;
						continue;
					}

					if (!isset($l['role']['name'])) {
						$role = '';
					} else {
						$role = strtolower($l['role']['name']);
					}

					echo '<li class="contact ',$role,' ',$alphabets[$current_alphabet],'-contact">';
						echo '<div class="contactType">',ucfirst($role),'</div>';
						echo '<img src="/resources/template/default_web/img/avatar.png" alt="" width="32" />';
						echo '<div class="info hasCompany"><a href="/card/view/',$l['id'],'">',$l['first_name'],' ',$l['last_name'],'</a></div>';
						echo '<div class="company">',$l['organization_name'],'</div>';
					echo '</li>';
				}
			?>
				<!--
				<li class="contact staff a-contact">
					<div class="contactType">Staff</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Albert A.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact customers a-contact">
					<div class="contactType">Customer</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info">Albert A.</div>
				</li>
				<li class="contact staff a-contact">
					<div class="contactType">staff</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Albert B.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact vendors noBottom a-contact">
					<div class="contactType">Vendor</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Albert C.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="letter staff customes rest vendors b-title">b</li>
				<li class="contact staff b-contact">
					<div class="contactType">Staff</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben Smith.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact rest b-contact">
					<div class="contactType">rest</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben X.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact vendors b-contact">
					<div class="contactType">Vendor</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info">Ben A.</div>
				</li>
				<li class="contact customers b-contact">
					<div class="contactType">Customer</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben Z.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact staff b-contact">
					<div class="contactType">Staff</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben ZZ.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact vendors b-contact">
					<div class="contactType">Vendor</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben A.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact rest b-contact">
					<div class="contactType">Rest</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben A.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				<li class="contact staff noBottom b-contact">
					<div class="contactType">Staff</div>
					<img src="img/examples/avatar.jpg" alt="" width="32" />
					<div class="info hasCompany">Ben A.</div>
					<div class="company">McDonalds, Co.</div>
				</li>
				-->
			</ul>
	</div>
	<!-- End Contacts' list -->

	<div class="rightPanel">
		<div class="contactName">
			<img src="img/examples/avatar.jpg" alt="" />
			Albert Z <span>Customer</span>
		</div>

		<!-- Start Tab Menu for contact's details -->
		<div id="menuTabCard">
			<ul>
				<li class="details active"><a href="#">Details</a></li>
				<li class="notes"><a href="#">Notes</a></li>
			</ul>
		</div>
		<!-- End Tab Menu for contact's details -->

		<div id="contentTabCard">
			<div class="top"></div>
			<div class="center">
				<div class="basicInformation">
					<div class="title">Basic information</div>
					<div class="info"><span>Phone</span> 555-3223</div>
					<div class="info"><span>Office</span> 555-3223</div>
					<div class="info"><span>Website</span> <a href="#">http://www.albert.com</a></div>
				</div>

				<div class="staff clearfix">
					<div class="title">Staff</div>
					<ul>
						<li>
							<div class="profileCard">
								<div class="zipDeco"></div>
								<a href="#"><img src="img/examples/avatar.jpg" alt="" width="50" /></a>
								<div class="info">
									<div class="name"><a href="#">Mark Zuckerberg</a></div>
									<div class="location">San Francisco</div>
								</div>
							</div>
						</li>
						<li>
							<div class="profileCard">
								<div class="zipDeco"></div>
								<a href="#"><img src="img/examples/avatar.jpg" alt="" width="50" /></a>
								<div class="info">
									<div class="name"><a href="#">Mark Zuckerberg</a></div>
									<div class="location">San Francisco</div>
								</div>
							</div>
						</li>
						<li>
							<div class="profileCard">
								<div class="zipDeco"></div>
								<a href="#"><img src="img/examples/avatar.jpg" alt="" width="50" /></a>
								<div class="info">
									<div class="name"><a href="#">Mark Zuckerberg</a></div>
									<div class="location">San Francisco</div>
								</div>
							</div>
						</li>
					</ul>
				</div>

				<button class="btn primary">Edit details</button>
			</div>
			<div class="bottom"></div>
		</div>
	</div>

</div>
<!-- End Contacts Panels -->