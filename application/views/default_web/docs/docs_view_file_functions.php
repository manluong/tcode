<?php function print_tree($tree, &$docs_detail, $html='') {
	$html .= '<ul>';
	foreach ($tree as $folder) {
		$html .= '<li class="tree-folders" folder_id='.$folder['a_docs_id'].'><a href="#" >'.$folder['a_docs_displayname'].'</a>';
		if (isset($folder['child'])) {
			$html .= print_tree($folder['child'], $docs_detail);
		} else {
			$html .= "</li>";
		}
	}
	$html .= '</ul>';
	return $html;
}

?>
<div id="filetree">
<?php if ( ! empty($tree)) : ?>
<ul>
<li class="tree-folders" folder_id="1"><a href="#">All files</a>
<?php echo print_tree($tree, $docs_detail); ?>
</li>
</ul>
<?php endif; ?>
</div>
<div id="confirm">
	<p>All files and versions will be remove.<br> Are you sure?</p>
</div>
<ul>
	<li class="permission" >Permission</li>
	<li class="move">Move</li>
	<li class="delete">Delete</li>
	<li class="download"><a href="/docs/download_file?id=<?php echo $docs_detail['a_docs_ver_id']; ?>">Download</a></li>
	<li class="upload">Upload</li>
</ul>
<input type="hidden" id="ver_id" value="<?php echo $docs_detail['a_docs_ver_id'];?>">
<input type="hidden" id="docs_id" value="<?php echo $docs_detail['a_docs_id'];?>">