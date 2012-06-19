<script type="text/javascript" src="/resources/addon/contacts.js"></script>
<div style="width:100%;" class="invoice_title">
	<span class="arrow_title"></span>
	<span style="text-transform:uppercase;"><?=$role?> DETAILS</span>
</div>

<ul id="view_active">
	<li>
		<span class="input_data_label">Status</span>
		<span class="fillter_input"><?=($data['active']==1?'Active':'Unactive')?></span>
	</li>
	<li style="margin:10px 0 0 95px;">
		<button id="btn_view_active" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">EDIT DETAILS</button>
	</li>
</ul>

<ul id="edit_active" style="display:none;">
	<li>
		<span class="input_data_label">Status</span>
		<span class="fillter_input">
			<select id="select_active">
				<option value=""> Change status</option>
				<option <?=($data['active']==1?'selected="selected"':'')?> value="1">Active</option>
				<option <?=($data['active']==0?'selected="selected"':'')?> value="0">Unactive</option>
			</select>
		</span>
	</li>
	<li style="margin:10px 0 0 95px;">
		<button id="btn_edit_active" onclick="ajax_change_status(<?=$data['id']?>);" style="height:20px;line-height:12px;" class="btn btn-inverse" href="#">SAVE</button>
	</li>
</ul>

