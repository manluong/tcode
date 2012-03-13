<style>
	#posts .post {
		padding:5px;
		border-bottom: solid 1px #EEE;
		margin:5px 0;
	}
</style>

<div class="container-fluid">
	<div class="row-fluid">
		<div class="span5">
			<div class="widget">
				<div class="widget-body">
					<div id="wall">
						<div id="post_new">
							<input type="text" name="" value="" placeholder="Something to share?" />
						</div>
						<div id="posts">
						</div>
						<button id="show_more" class="btn" data-last_id="" style="width:100%;">Show More</button>
					</div>
				</div>
			</div>
		</div>
		<div class="span7">

		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$.get(
			'/dashboard/ajax_wall',
			function(resp) {
				var posts = '';
				var last_id = '';

				$.each(resp.details, function(k, v) {
					posts += Mustache.to_html(tpl_dashboard.post, v);
					last_id = v.id;
				});
				$('#posts').html(posts);

				$('#show_more').attr('data-last_id', last_id);
			},
			'json'
		);


		$('#show_more').on('click', function() {
			$.get(
				'/dashboard/ajax_wall/'+$('#show_more').attr('data-last_id'),
				function(resp) {
					var posts = '';
					var last_id = '';

					$.each(resp.details, function(k, v) {
						posts += Mustache.to_html(tpl_dashboard.post, v);
						last_id = v.id;
					});
					$('#posts').append(posts);

					$('#show_more').attr('data-last_id', last_id);
				},
				'json'
			);
		});
	});

</script>