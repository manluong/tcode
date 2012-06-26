
<div id="boxes" class="timeline">
	<!-- Start Timeline box -->
	<div class="box left">
		<div class="title"><img src="/resources/template/<?=get_template()?>/img/icon-chevron-right.png" /> Recent activity</div>

		<div class="boxContent timeline">
			<div id="posts"></div>

			<a href="/activity" class="viewAll">View all &rarr;</a>
		</div>
	</div>
	<!-- End Timeline box -->

	<!-- Start Contacts box -->
	<div class="box">
		<div class="title"><img src="/resources/template/<?=get_template()?>/img/icon-chevron-right.png" /> Contacts</div>

		<div class="boxContent upcoming events">

			{graphs js}

		</div>
	</div>
	<!-- End Contacts box -->

	<!-- Start Upcoming Events box -->
	<div class="box left">
		<div class="title"><img src="/resources/template/<?=get_template()?>/img/icon-chevron-right.png" /> Upcoming events</div>

		<div class="boxContent upcomingEvents">

			<div class="entry clearfix">
				<div class="calendarNumber">25</div>
				<div class="info">
					<a href="#">Networking event</a> <span class="">at Hilton Hotel</span>
					<div class="time">On thursday 29th at 18:00</div>
				</div>
			</div>

			<div class="entry clearfix">
				<div class="calendarNumber">10</div>
				<div class="info">
					<a href="#">FOWA 2012</a> <span class="">at Hilton Hotel</span>
					<div class="time">On thursday 29th at 18:00</div>
				</div>
			</div>

			<div class="entry clearfix">
				<div class="calendarNumber">12</div>
				<div class="info">
					<a href="#">SXSW 2012</a> <span class="">at Hilton Hotel</span>
					<div class="time">On thursday 29th at 18:00</div>
				</div>
			</div>

			<a href="#" class="viewAll">View all &rarr;</a>

		</div>
	</div>
	<!-- End Upcoming Events box -->

	<!-- Start Recently Viewed box -->
	<div class="box">
		<div class="title"><img src="/resources/template/<?=get_template()?>/img/icon-chevron-right.png" /> Recently viewed</div>

		<div class="boxContent recentlyViewed">

			<div class="entry left">
				<div class="icon activityIcon"></div>
				<div class="info">
					<a href="#">Activity</a>
					<div class="time">23 minutes ago</div>
				</div>
			</div>

			<div class="entry">
				<div class="icon calendarIcon"></div>
				<div class="info">
					<a href="#">FOWA 2012</a> <span class="">at Hilton Hotel</span>
					<div class="time">About 1 hour ago</div>
				</div>
			</div>

			<div class="entry left">
				<div class="icon contactsIcon"></div>
				<div class="info">
					<a href="#">Mark Zuckerberg's profile</a>
					<div class="time">About 30 minutes ago</div>
				</div>
			</div>

			<div class="clearfix"></div>

			<a href="#" class="viewAll">View all &rarr;</a>

		</div>
	</div>
	<!-- End Recently Viewed box -->

</div>

<script>
	$(document).ready(function() {
		$.get(
			'/activity/ajax_wall',
			function(resp) {
				var posts = '';
				var last_id = '';

				$.each(resp.details, function(k, v) {
					v.app_id = 18;
					v.comments_html = '';
					v.app_data_id = v.id;
					v.parent_id = 0;
					comments_html = '';
					show_more_html = '';

					if ($(v.comments).size()>0) {
						$.each(v.comments, function(sk, sv) {
							if (sk == 1) v.last_id = sv.id;
							sv.reply = 'reply';
							comments_html += Mustache.to_html(tpl_comments.post, sv);
						});
					}

					v.comments_html = comments_html;
					posts += Mustache.to_html(tpl_dashboard.post, v);
					last_id = v.id;
				});
				$('#posts').html(posts);
				$('div.time').timeago();
			},
			'json'
		);

	});

</script>