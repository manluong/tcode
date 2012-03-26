<div class="hide" id="process">
	We're setting up your account now. Please hold.
</div>

<div class="hide" id="success">
	<h3>All Done!</h3>
	<p>Your domain is now all setup and ready to go!</p>
	<p>You may click on the link below to begin using.</p>
	<p style="font-size:16px;"><a href="http://<?=$domain?>.8force.net">http://<?=$domain?>.8force.net</a></p>
	<!--<p>We will also send you an email on your account details.</p>-->
	<p>Thank you for choosing 8Force.</p>
</div>

<div class="hide" id="error">
</div>

<script>
	$(document).ready(function() {
		$('#process').slideDown();
		$.get(
			'/signup/ajax_begin_setup',
			function(resp) {
				if (resp.success) {
					$('#process').slideUp();
					$('#success').slideDown();
				} else {
					$('#process').slideUp();
					$('#error').html(resp.details).slideDown();
				}
			}
		);
	});
</script>