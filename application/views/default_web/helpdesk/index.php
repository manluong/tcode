<div id="helpdesk_list">
	<div class="widget">
		<div class="widget-header">
			<h4>
				<div style="float:left;width:1010px;height:10px;">HelpDesk List</div>
				<div><span onclick="load_insert_helpdesk_form()">New</span></div>
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
					<div style="float:left;" class="input-prepend">
						<span class="add-on"><i class="icon-search"></i></span>
						<input type="text">
					</div>
					<li class="controls" style="float:left;margin-left:20px;">
						<label for="select01" class="control-label" style="padding-right:5px;">Fillter</label>
						<select name="helpdesk_fillter" id="helpdesk_fillter">
							<option value="">All</option>
							<option value="">Group</option>
							<option value="">Status</option>
							<option value="">Type</option>
							<option value="">Priority</option>
						</select>
					</li>
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
				
				<tbody>
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
