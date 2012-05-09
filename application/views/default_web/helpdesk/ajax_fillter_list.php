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