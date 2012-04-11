<style>
	#permissions_table th, #permissions_table td {
		font-size:12px;
		padding:5px;
	}

	#permissions_table .row_selected {
		background-color:#DDD;
	}
</style>




<div style="padding:10px;">
	<a href="#" id="view_permissions">Manage Permissions</a>
</div>


<div id="acl_summary">
	<table border="1" id="permissions_table" class="tpaneltable" width="100%" cellspacing="5" cellpadding="5">
		<thead>
			<tr>
				<th>ID</th>
				<th>Role/Group</th>
				<th>Admin</th>
				<th>Read</th>
				<th>Write</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>

	<div class="ui-widget bu-div" style="margin-top:10px;">
		<button type="button" id="add_acl_button" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" data-icon-primary="ui-icon-plus" role="button"><span class="ui-button-icon-primary ui-icon ui-icon-plus"></span><span class="ui-button-text">Add New Permission</span></button>
		<button type="button" id="delete_acl_button" class="button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" data-icon-primary="ui-icon-trash" role="button"><span class="ui-button-icon-primary ui-icon ui-icon-trash"></span><span class="ui-button-text">Delete Selected Row</span></button>
	</div>
</div>


<div id="acl_add">
	<div id="acl_groups">
		<ul>
			<li><a href="#tab-groups">Groups</a></li>
			<li><a href="#tab-roles">Roles</a></li>
			<li><a href="#tab-users">Users</a></li>
		</ul>

		<div id="tab-groups" data-tab="groups">
			<form>
				<input type="hidden" name="acl_type" value="groups" />
				<input type="hidden" name="app" value="<?=$app?>" />
				<input type="hidden" name="actiongp" value="<?=$actiongp?>" />
				<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
				<select name="groups" class="groups" id="g-groups">
					<option value="">Select Group</option>
				</select>
				<p><input type="checkbox" name="admin" id="g-admin" value="1" /> <label for="g-admin">Admin</label></p>
				<p><input type="checkbox" name="write" id="g-write" value="1" /> <label for="g-write">Write</label></p>
				<p><input type="checkbox" name="read" id="g-read" value="1" /> <label for="g-read">Read</label></p>
				<p><input type="button" name="save" value="Save" class="save-button" /></p>
				<div class="ui-widget message" style="display:none;">
					<div class="ui-corner-all">
						<p>
							<div class="message-content"></div>
						</p>
					</div>
				</div>
			</form>
		</div>

		<div id="tab-roles" data-tab="roles">
			<form>
				<input type="hidden" name="acl_type" value="roles" />
				<input type="hidden" name="app" value="<?=$app?>" />
				<input type="hidden" name="actiongp" value="<?=$actiongp?>" />
				<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
				<select name="groups" class="groups" id="r-groups">
					<option value="">Select Group</option>
				</select>
				<p><select name="roles" class="roles" id="r-roles">
					<option>Select Role</option>
				</select></p>
				<p><input type="checkbox" name="admin" id="r-admin" value="1" /> <label for="r-admin">Admin</label></p>
				<p><input type="checkbox" name="write" id="r-write" value="1" /> <label for="r-write">Write</label></p>
				<p><input type="checkbox" name="read" id="r-read" value="1" /> <label for="r-read">Read</label></p>
				<p><input type="button" name="save" value="Save" class="save-button" /></p>
				<div class="ui-widget message" style="display:none;">
					<div class="ui-corner-all">
						<p>
							<div class="message-content"></div>
						</p>
					</div>
				</div>
			</form>
		</div>

		<div id="tab-users" data-tab="users">
			<form>
				<input type="hidden" name="acl_type" value="users" />
				<input type="hidden" name="app" value="<?=$app?>" />
				<input type="hidden" name="actiongp" value="<?=$actiongp?>" />
				<input type="hidden" name="app_data_id" value="<?=$app_data_id?>" />
				<select name="groups" class="groups" id="u-groups">
					<option value="">Select Group</option>
				</select>
				<p><select name="users" class="users" id="u-users">
					<option>Select User</option>
				</select></p>
				<p><input type="checkbox" name="admin" id="u-admin" value="1" /> <label for="u-admin">Admin</label></p>
				<p><input type="checkbox" name="write" id="u-write" value="1" /> <label for="u-write">Write</label></p>
				<p><input type="checkbox" name="read" id="u-read" value="1" /> <label for="u-read">Read</label></p>
				<p><input type="button" name="save" value="Save" class="save-button" /></p>
				<div class="ui-widget message" style="display:none;">
					<div class="ui-corner-all">
						<p>
							<div class="message-content"></div>
						</p>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
	var permissions_table = '';

	$('document').ready(function() {


		$('#view_permissions').click(function() {
			$('#acl_summary').dialog('open');
		});


		$('#r-groups').change(function() {
			var sel = $('#r-groups :selected').val();
			$.post(
				'/acl/ajax_get_roles/',
				{ gp_id: sel },
				function(resp) {
					update_select('#r-roles', resp.data, 'access_gpsub_name', 'access_gpsub_id');
				},
				'json'
			);
		});

		$('#u-groups').change(function() {
			var sel = $('#u-groups :selected').val();
			$.post(
				'/acl/ajax_get_users/',
				{ gp_id: sel },
				function(resp) {
					update_select('#u-users', resp.data, 'name', 'access_usergp_cardid');
				},
				'json'
			);
		});

		$('.save-button').click(function() {
			var form = $(this).parent().parent();
			$.post(
				'/acl/ajax_save_acl/',
				form.serializeArray(),
				function(resp) {
					if (resp.success) {
						form.find('.message').addClass('ui-state-success').find('.message-content').text(resp.message);
						form.find('.message').slideToggle().delay(1000).slideToggle(function() { $(this).removeClass('ui-state-success') });
					} else {
						form.find('.message').addClass('ui-state-error').find('.message-content').text(resp.message);
						form.find('.message').slideToggle().delay(1000).slideToggle(function() { $(this).removeClass('ui-state-error') });
					}
				},
				'json'
			);
		});


		$('#acl_summary').dialog({
			title: 'Manage Permissions',
			autoOpen:false,
			modal:true,
			width:800,
			height:480
		}).bind('dialogopen', function(event, ui) {
			if (permissions_table == '') {
				permissions_table = $('#permissions_table').dataTable({
					bJQueryUI: true,
					bPaginate: false,
					bFilter: false,
					sAjaxDataProp: 'acl',
					sAjaxSource: '/acl/ajax_get_acl/<?=$app?>/<?=$actiongp?>/<?=$app_data_id?>',
					aoColumns: [
						{ sTitle: 'ID', mDataProp: 'id' },
						{ sTitle: 'Role / Group', mDataProp: 'name' },
						{ sTitle: 'Admin', mDataProp: 'admin_display' },
						{ sTitle: 'Read', mDataProp: 'read_display' },
						{ sTitle: 'Write', mDataProp: 'write_display' }
					]
				});
			}
		});

		$('#add_acl_button').click(function() {
			$('#acl_add').dialog('open');
		});


		$('#permissions_table').on('click', 'tbody', function(e) {
			$(permissions_table.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('row_selected');
			});

			$(e.target.parentNode).addClass('row_selected');
		});

		$('#delete_acl_button').click(function() {
			var anSelected = fnGetSelected( permissions_table );
			var acl_id = $(anSelected).children('td :first').text();

			if (acl_id == undefined || acl_id == '') return false;

			$.post(
				'/acl/ajax_delete_acl',
				{ acl_id: acl_id },
				function(resp) {
					if (resp.success) {
						permissions_table.fnDeleteRow( anSelected[0] );
					}
				},
				'json'
			);
		});



		$('#acl_add').dialog({
			title: 'Add Permissions',
			autoOpen:false,
			modal:true,
			width:640
		}).bind('dialogopen', function(event, ui) {
			$('#acl_groups').tabs();

			var groups = new Array();

			$.post(
				'/acl/ajax_get_groups',
				{},
				function(resp) {
					update_select('.groups', resp.data, 'access_gpmaster_name', 'access_gpmaster_code');
				},
				'json'
			);

		});


		function update_select(form, data, name, value) {
			var target = $(form);
			target.empty();
			$(data).each(function(i, v) {
				target.append('<option value="'+v[value]+'">'+v[name]+'</option>');
			});
			if (data.length == 0) {
				target.append('<option value="">&nbsp;</option>');
			}
		}

		function fnGetSelected( oTableLocal ) {
			var aReturn = new Array();
			var aTrs = oTableLocal.fnGetNodes();

			for ( var i=0 ; i<aTrs.length ; i++ ) {
				if ( $(aTrs[i]).hasClass('row_selected') ) {
					aReturn.push( aTrs[i] );
				}
			}
			return aReturn;
		}


	});


	//datatable reloadAjax plugin, bug fix: json.aaData changed to aData
	$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw ) {

		if ( typeof sNewSource != 'undefined' && sNewSource != null )
		{
			oSettings.sAjaxSource = sNewSource;
		}
		this.oApi._fnProcessingDisplay( oSettings, true );
		var that = this;
		var iStart = oSettings._iDisplayStart;

		oSettings.fnServerData( oSettings.sAjaxSource, [], function(json) {
			/* Clear the old information from the table */
			that.oApi._fnClearTable( oSettings );

			/* Got the data - add it to the table */
			var aData =  (oSettings.sAjaxDataProp !== "") ?
				that.oApi._fnGetObjectDataFn( oSettings.sAjaxDataProp )( json ) : json;

			for ( var i=0 ; i<aData.length ; i++ )
			{
				that.oApi._fnAddData( oSettings, aData[i] );
			}

			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			that.fnDraw();

			if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true )
			{
				oSettings._iDisplayStart = iStart;
				that.fnDraw( false );
			}

			that.oApi._fnProcessingDisplay( oSettings, false );

			/* Callback user function - for event handlers etc */
			if ( typeof fnCallback == 'function' && fnCallback != null )
			{
				fnCallback( oSettings );
			}
		}, oSettings );
	}
</script>