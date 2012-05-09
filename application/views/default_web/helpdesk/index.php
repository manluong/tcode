<script type="text/javascript" >
	$(document).ready(function(){

	});

	function ajax_search(){
		var value = $('#helpdesk_search').val();
		
		var url = 'helpdesk/ajax_search/';
		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}
	
	function group_fillter(){
		var value = $('#helpdesk_group').val();
		var url = 'helpdesk/group_fillter/';
		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}
	
	function status_fillter(){
		var value = $('#helpdesk_status').val();
		var url = 'helpdesk/status_fillter/';
		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}
	
	function type_fillter(){
		var value = $('#helpdesk_type').val();
		var url = 'helpdesk/type_fillter/';
		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}
	
	function priority_fillter(){
		var value = $('#helpdesk_prioruty').val();
		var url = 'helpdesk/priority_fillter/';
		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}
</script>
<div id="helpdesk_list">
	<div class="widget">
		<div class="widget-header">
			<h4>
				<div style="float:left;width:1010px;height:10px;">HelpDesk List</div>
				<div><a href="#" onclick="load_insert_helpdesk_form()">New</a></div>
			</h4>
		</div>
		
		<div class="widget-body">
			<div class="dataTables_wrapper form-inline" id="helpdesk_list_table_wrapper">
				<div class="pull-right">
					<div class="dataTables_paginate paging_bootstrap pagination">
						<ul>
							<li class="prev disabled"><a href="#">Previous</a></li>
							<li class="active"><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li class="next"><a href="#">Next</a></li>
						</ul>
					</div>
				</div>
				<div class="dataTables_filter" id="helpdesk_list_table_filter">
					<div style="width:700px;" class="input-prepend">
						<span class="add-on" onclick="return ajax_search();"><i style="cursor:pointer;" class="icon-search"></i></span>
						<input onchange="return ajax_search();" type="text" id="helpdesk_search" name="helpdesk_search">
					</div>
					<!--FILLTER-->
					<ul style="margin:15px 0 7px 0px;">
						<!--GROUP FILLTER-->
						<li class="fillter">
							<label class="control-label" for="select01">Group</label>
							<select  onchange="group_fillter()" name="helpdesk_group" id="helpdesk_group">
								<option value="">Fillter</option>
								<?php if(!empty($group)){
										foreach($group as $k){
								?>
								<option value="<?=$k->access_gpsub_id?>"><?=$k->access_gpsub_name?></option>
								<?php }}?>
							</select>
						</li>
						<!--STATUS FILLTER-->
						<li class="fillter">
							<label class="control-label" for="select01">Status</label>
							<select  onchange="status_fillter()" id="helpdesk_status" name="helpdesk_status">
								<option value="">Fillter</option>
								<?php if(!empty($status)){
										foreach($status as $k){
								?>
								<option value="<?=$k->id?>"><?=$k->name?></option>
								<?php }}?>
							</select>
						</li>
						<!--TYPE FILLTER-->
						<li class="fillter">
							<label class="control-label" for="select01">Type</label>
							<select  onchange="type_fillter()" id="helpdesk_type" name="helpdesk_type">
								<option value="">Fillter</option>
								<?php if(!empty($type)){
										foreach($type as $k){
								?>
								<option value="<?=$k->id?>"><?=$k->name?></option>
								<?php }}?>
							</select>
						</li>
						<!--PRIORITY FILLTER-->
						<li class="controls">
							<label class="control-label" for="select01">Priority</label>
							<select onchange="priority_fillter()" id="helpdesk_priority" name="helpdesk_priority">
								<option value="">Fillter</option>
								<?php if(!empty($priority)){
										foreach($priority as $k){
								?>
								<option value="<?=$k->id?>"><?=$k->name?></option>
								<?php }}?>
							</select>
						</li>
					</ul>
					<!--END FILLTER-->
				</div>
			</div>
			
			<table cellspacing="0" cellpadding="0" border="0" id="helpdesk_list_table" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th class="sorting_asc" rowspan="1" colspan="1" style="width: 80px;">Id</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 200px;">Subject</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 207px;">Created</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 291px;">Last Update</th>
						<th class="sorting" rowspan="1" colspan="1" style="width: 231px;">Assigned</th>
						<th style="width: 45px;" colspan="1" rowspan="1" class="sorting">Edit</th>
					</tr>
				</thead>
				
				<tbody id="helpdesk_datalist">
					<?php if(!empty($result)){
							for($i = 0 ; $i < count($result) ; $i++){
					?>
					<tr class="odd">
						<td class=" sorting_1"><?=$result[$i][id]?></td>
						<td><?=$result[$i][subject]?></td>
						<td><?=$result[$i][created_stamp]?></td>
						<td><?=$result[$i][modified_stamp]?></td>
						<td><?=$this->Helpdesk_CommentM->get_assigname($result[$i][assign_id])?></td>
						<td><span class="btn btn-default" onclick="load_comment_form(<?=$result[$i][id]?>)">Edit</span>
						</td>
					</tr>
					<?php }}?>
				</tbody>
			</table>
		
		
		<div class="pull-right">
			<div class="dataTables_paginate paging_bootstrap pagination">
				<ul>
					<li class="prev disabled"><a href="#">Previous</a></li>
					<li class="active"><a href="#">1</a></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
					<li class="next"><a href="#">Next</a></li>
				</ul>
			</div>
		</div>
		
		<div id="helpdesk_list_table_length" class="dataTables_length">
			<label>
				<select>
					<option value="10" selected="selected">10</option>
					<option value="20">20</option><option value="30">30</option>
					<option value="40">40</option><option value="50">50</option>
					<option value="-1">All</option>
				</select>Rows
			</label>
		</div>
		
		<div class="dataTables_info" id="helpdesk_list_table_info">Showing 1 to 10 of 56</div>
		</div>
	</div>
</div>
