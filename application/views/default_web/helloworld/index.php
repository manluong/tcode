<div id="helloworld_list">
</div>

<div id="helloworld_view">
</div>

<div id="helloworld_form">
</div>

<script>
	$(document).ready(function() {
		ajax_content('helloworld/sendjson_list', 'helloworld_list');
		ajax_content('helloworld/sendjson_view', 'helloworld_view');
		ajax_content('helloworld/sendjson_form', 'helloworld_form');
	});
</script>