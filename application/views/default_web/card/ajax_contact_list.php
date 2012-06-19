<?php if ($list): ?>
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

		echo '<li onclick="load_contact_info('.$l['id'].')" class="contact ',$role,' ',$alphabets[$current_alphabet],'-contact">';
			echo '<div class="contactType">',ucfirst($role),'</div>';
			echo '<img src="/resources/template/default_web/img/avatar.jpg" alt="" width="32" />';
			echo '<div class="info hasCompany"><a href="/card/view/',$l['id'],'">',$l['first_name'],' ',$l['last_name'],'</a></div>';
			echo '<div class="company">',$l['organization_name'],'</div>';
		echo '</li>';
	}
?>
<?php endif ?>