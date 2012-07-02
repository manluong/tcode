<input class="search" />
<div class="optionsDropdownContainer">
	<a href="#" class="optionsDropdownOpener">Options</a>
	<div class="optionsDropdownPadder">
		<div class="optionsDropdownList">
			<div class="arrow"></div>
			<ul>
				<li><input type="checkbox" class="styled" id="contacts" /> <label for="contacts">Contacts</label></li>
				<li><input type="checkbox" class="styled" id="vendors" /> <label for="vendors">Vendors</label></li>
				<li><input type="checkbox" class="styled" id="documents" /> <label for="documents">Documents</label></li>
			</ul>
		</div>
	</div>
</div>

<input type="submit" class="searchInput" value="Go" />

<div class="dropdownAvatar">
	<img src="/resources/template/<?=get_template()?>/img/avatar.png" alt="" width="37" class="dropdownAvatarOpener" />
	<div class="arrow dropdownAvatarOpener"></div>

	<div class="userDropdownPadding">
		<div class="userDropdownList">
			<div class="arrow"></div>
			<ul>
				<li><?=$current_user['first_name'].' '.$current_user['last_name']?></li>
				<li><a class="settings" href="/setting">Settings</a></li>
				<li><a class="support" href="#">Support</a></li>
				<!--<li><a class="logout last" href="/access/logout">Logout</a></li>-->
				<li><a class="logout last" href="javascript:void(0);" onclick="telcoson.logoff(true);">Logout</a></li>
			</ul>
		</div>
	</div>
</div>