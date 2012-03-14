<style>
	div.dataTables_length label {
		width: 460px;
		float: left;
		text-align: left;
	}

	div.dataTables_length select {
		width: 75px;
	}

	div.dataTables_filter label {
		float: right;
		width: 460px;
	}

	div.dataTables_info {
		padding-top: 8px;
	}

	div.dataTables_paginate {
		float: right;
		margin: 0;
	}

	table {
		margin: 1em 0;
		clear: both;
	}
</style>
<form>
	<div id="uploader">
		<p>Your browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
	</div>
</form>
<div class="form-inline">
	<input type="text" name="name" id="name" class="input-small"><button id="create-folder" class="btn">Create Folder</button>
</div>
<table id="directory_contents" class="table table-striped table-bordered table-condensed">
	<thead>
	<tr>
		<th>Name</th>
		<th>Size</th>
		<th>Last Modified</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	</tbody>
</table>