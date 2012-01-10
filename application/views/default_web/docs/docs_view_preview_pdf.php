<div style="left:10px;top:10px;">
	<p id="viewerPlaceHolder" style="width:660px;height:553px;display:block">Document loading..</p>
	<script type="text/javascript">
		var fp = new FlexPaperViewer(
				 '/resources/template/default_web/lib/flexpaperViewer/FlexPaperViewer',
				 'viewerPlaceHolder', { config : {
				 SwfFile : escape('/docs/pdfPreview?doc=<?php echo $docs_detail['a_docs_ver_filename']; ?>'),
				 Scale : 0.6,
				 ZoomTransition : 'easeOut',
				 ZoomTime : 0.5,
				 ZoomInterval : 0.2,
				 FitPageOnLoad : true,
				 FitWidthOnLoad : false,
				 FullScreenAsMaxWindow : false,
				 ProgressiveLoading : false,
				 MinZoomSize : 0.2,
				 MaxZoomSize : 5,
				 SearchMatchAll : false,
				 InitViewMode : 'Portrait',
				 PrintPaperAsBitmap : false,

				 ViewModeToolsVisible : true,
				 ZoomToolsVisible : true,
				 NavToolsVisible : true,
				 CursorToolsVisible : true,
				 SearchToolsVisible : true,

				 localeChain: 'en_US'
				 }});
	</script>
</div>
