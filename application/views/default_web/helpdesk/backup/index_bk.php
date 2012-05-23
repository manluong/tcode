<link href="/resources/template/default_web/helpdesk.css" media="screen" rel="stylesheet" type="text/css" />
<script type="text/javascript" >
	var total = <?php echo $total?>;
	$(document).ready(function(){
		$('.page_active_top:first').addClass('active');
		$('.page_active_bottom:first').addClass('active');
	});

	function ajax_pagination(offset){
		set_active_page(offset);
		var url = '<?=site_url('helpdesk/ajax_pagination');?>';
		$.post(url,{
				offset : offset
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function set_active_page(offset){
		var current_page = parseInt(offset)/10 + 1;
		show_curren_page(current_page);

		if (offset == 0) {
			$('.prev').addClass('disabled');
		} else {
			$('.prev').removeClass('disabled');
		}
		if ((parseInt(offset)+10) > total) {
			$('.next').addClass('disabled');
		} else {
			$('.next').removeClass('disabled');
		}
		var to = parseInt(offset) + 1;
		var from = parseInt(offset) + 10;
		if (from > total) {
			from = total;
		}
		$('#helpdesk_list_table_info').html('Showing '+to+' to '+from+' of '+total);

		$('.page_active_top').removeClass('active');
		$('.page_active_bottom').removeClass('active');
		$('#page_top'+offset).addClass("active");
		$('#page_bottom'+offset).addClass("active");
	}

	function show_curren_page(current_page){
		$('.page_active_top ').removeClass('hide_page');
		$('.page_active_bottom ').removeClass('hide_page');
		if (current_page == 1) {//first page
			for(var i = 50 ; i <= total ; i+=10){
				$('#page_top'+i).addClass('hide_page');
				$('#page_bottom'+i).addClass('hide_page');
			}
		} else {
			if ((current_page*10) >= total){//last page
				for (var i = (current_page*10) - 60; i >= 0 ; i -=10){
					$('#page_top'+i).addClass('hide_page');
					$('#page_bottom'+i).addClass('hide_page');
				}
			} else {
				if (current_page - 1 == 1){//right first page
					for (i = (current_page*10) + 30 ; i <= total ; i+=10) {
						$('#page_top'+i).addClass('hide_page');
						$('#page_bottom'+i).addClass('hide_page');
					}

					for (i = (current_page*10) - 40 ; i >= 0 ; i-=10) {
						$('#page_top'+i).addClass('hide_page');
						$('#page_bottom'+i).addClass('hide_page');
					}
				} else {
					if ((current_page*10)+10 >= total) {//left last page
						for (i = (current_page*10) + 20 ; i <= total ; i+=10) {
							$('#page_top'+i).addClass('hide_page');
							$('#page_bottom'+i).addClass('hide_page');
						}

						for (i = (current_page*10) - 50 ; i >= 0 ; i-=10) {
							$('#page_top'+i).addClass('hide_page');
							$('#page_bottom'+i).addClass('hide_page');
						}
					} else {//between
						for (i = (current_page*10) + 20 ; i <= total ; i+=10) {
							$('#page_top'+i).addClass('hide_page');
							$('#page_bottom'+i).addClass('hide_page');
						}

						for (i = (current_page*10) - 40 ; i >= 0 ; i-=10) {
							$('#page_top'+i).addClass('hide_page');
							$('#page_bottom'+i).addClass('hide_page');
						}
					}
				}
			}
		}
	}

	function ajax_pre() {
		var temp = $('.active').attr('is_active');
		if (temp == 0) {
			var offset = 0;
		} else {
			var offset = parseInt(temp) - 10;
		}

		set_active_page(offset);

		var url = '<?=site_url('helpdesk/ajax_pagination');?>';
		$.post(url,{
				offset : offset
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function ajax_next(){
		var temp = $('.active').attr('is_active');

		var offset = parseInt(temp) + 10;
		if (offset > total) {
			offset = offset - 10;
		}
		set_active_page(offset);

		var url = '<?=site_url('helpdesk/ajax_pagination');?>';

		$.post(url,{
				offset : offset
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function ajax_search(){
		var value = $('#helpdesk_search').val();

		var url = '<?=site_url('helpdesk/ajax_search');?>';

		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function group_fillter(){
		var value = $('#helpdesk_group').val();
		var url = '<?=site_url('helpdesk/group_fillter');?>';

		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function status_fillter(){
		var value = $('#helpdesk_status').val();
		var url = '<?=site_url('helpdesk/status_fillter');?>';

		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function type_fillter(){
		var value = $('#helpdesk_type').val();
		var url = '<?=site_url('helpdesk/type_fillter');?>';

		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function priority_fillter(){
		var value = $('#helpdesk_prioruty').val();
		var url = '<?=site_url('helpdesk/priority_fillter');?>';

		$.post(url,{
				value : value
			},function(data){
				$('#helpdesk_datalist').html(data);
			}
		);
	}

	function fillter_record(){
		var value = $('#fillter_record').val();
		var url = '<?=site_url('helpdesk/fillter_record');?>';
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
				<div><a href="<?=site_url('helpdesk/add');?>">New</a></div>
			</h4>
		</div>

		<div class="widget-body">
			<div class="dataTables_wrapper form-inline" id="helpdesk_list_table_wrapper">
				<div class="pull-right">
					<div class="dataTables_paginate paging_bootstrap pagination">
						<ul class="ul_pagination">
							<li class="prev disabled"><a onclick="ajax_pre()" href="#">Previous</a></li>
							<?php if ($total > 0) {
							for ($i = 0 ; $i < $total ; $i+=10) {
								$j = $i/10 + 1 ;
							?>
							<li id="page_top<?=$i?>" is_active="<?=$i?>" class="page_active_top <?=($j>5?'hide_page':'')?>"><a  onclick="ajax_pagination(<?=$i?>)"><?=$j?></a></li>
							<?php }}?>
							<li class="next"><a onclick="ajax_next()" href="#">Next</a></li>
						</ul>
					</div>
				</div>
				<div class="dataTables_filter" id="helpdesk_list_table_filter">
					<div style="width:600px;" class="input-prepend">
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
								<?php if (!empty($group)) {
										foreach($group as $k) {
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
								<?php if (!empty($status)) {
										foreach($status as $k) {
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
								<?php if (!empty($type)) {
										foreach($type as $k) {
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
								<?php if (!empty($priority)) {
										foreach($priority as $k) {
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
					<?php if (!empty($result)) {
							for ($i = 0 ; $i < count($result) ; $i++) {
					?>
					<tr class="odd">
						<td class=" sorting_1"><?=$result[$i]['id']?></td>
						<td><?=$result[$i]['subject']?></td>
						<td><?=$result[$i]['created_stamp']?></td>
						<td><?=$result[$i]['modified_stamp']?></td>
						<td><?=$this->Helpdesk_CommentM->get_assigname($result[$i]['assign_id'])?></td>
						<td><a href="<?=site_url('helpdesk/edit');?>/<?=$result[$i]['id']?>" class="btn btn-default">Edit</a>

						</td>
					</tr>
					<?php }}?>
				</tbody>
			</table>


		<div class="pull-right">
			<div class="dataTables_paginate paging_bootstrap pagination">
				<ul class="ul_pagination">
					<li class="prev disabled"><a onclick="ajax_pre()" href="#">Previous</a></li>
					<?php if ($total > 0) {
					for ($i = 0 ; $i < $total ; $i+=10) {
						$j = $i/10 + 1 ;
					?>
					<li id="page_bottom<?=$i?>" class="page_active_bottom <?=($j>5?'hide_page':'')?>"><a  onclick="ajax_pagination(<?=$i?>)"><?=$j?></a></li>
					<?php }}?>
					<li class="next"><a onclick="ajax_next()" href="#">Next</a></li>
				</ul>
			</div>
		</div>

		<div id="helpdesk_list_table_length" class="dataTables_length">
			<label>
				<select onchange="fillter_record()" id="fillter_record">
					<option value="10" selected="selected">10</option>
					<option value="20">20</option><option value="30">30</option>
					<option value="40">40</option><option value="50">50</option>
					<option value="-1">All</option>
				</select>
			</label>
		</div>

		<div class="dataTables_info" id="helpdesk_list_table_info">Showing 1 to 10 of <?=$total?></div>
		</div>
	</div>
</div>