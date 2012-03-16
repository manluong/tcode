<div class="container-fluid">
	<div class="row-fluid">
		<div class="span7">
			<div class="widget">
				<div class="widget-body">
					<div id="wall">
						<div>
							<?=form_open('/dashboard/new_post')?>
							<input type="text" id="new_post" name="" value="" style="width:90%;" placeholder="Something to share?" />
							</form>
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

					if (v.comments_more) {
						show_more_html += Mustache.to_html(tpl_comments.show_more, v);
					}

					v.comments_html = show_more_html + comments_html;
					posts += Mustache.to_html(tpl_dashboard.post, v);
					last_id = v.id;
				});
				$('#posts').html(posts);
				$('span.displaydate').timeago();

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

					if ($(resp.details).size() < 10) $('#show_more').remove();

					$('#show_more').attr('data-last_id', last_id);
				},
				'json'
			);
		});


		$('#wall').on('keypress', '#new_post', function(e) {
			if (e.which != 13) return true;

			var textbox = $(this);
			var textboxform = textbox.closest('form');

			var text = textbox.val();

			if (text.length == 0) {
				e.preventDefault();
				return false;
			}

			textbox.attr('disabled', 'disabled');
			$(textboxform).attr('onSubmit', 'return false;')

			$.post(
				'/dashboard/new_post',
				{ text: text },
				function(resp) {
					if (resp.success) {
						textbox.val('');
						resp.details.app_id = 18;
						resp.details.app_data_id = resp.details.id;

						$('#posts').prepend(
							Mustache.to_html(tpl_dashboard.post, resp.details)
						);

						$('span.displaydate').timeago();
					} else {
						alert('Unable to save. Please try again.'+resp.message);
					}
					textbox.removeAttr('disabled');
					$(textboxform).removeAttr('onSubmit');
				},
				'json'
			);

			//prevent enter key from submitting the form
			e.preventDefault();
			return false;
		});

	});

</script>