<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
        <title>FlexPaper</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css" media="screen">
			html, body	{ height:100%; }
			body { margin:0; padding:0; overflow:auto; }
			#flashContent { display:none; }
        </style>

		<script type="text/javascript" src="js/flexpaper_flash.js"></script>
    </head>
    <body>
    	<div style="position:absolute;left:10px;top:10px;">
	        <a id="viewerPlaceHolder" style="width:660px;height:480px;display:block"></a>

	        <script type="text/javascript">
				var fp = new FlexPaperViewer(
						 'FlexPaperViewer',
						 'viewerPlaceHolder', { config : {
						 SwfFile : escape('<?php echo $s3_object; ?>'),
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

		<!-- THE FOLLOWING CODE BLOCK CAN SAFELY BE REMOVED, IT IS ONLY PLACED HERE TO HELP YOU GET STARTED. -->
        <div style="position:absolute;left:680px;height:540px;top:10px;font-family:Verdana;font-size:9pt;background-color:#CACACA;width:300px">
			<div style="padding: 5px 5px 5px 5px;font-size:15px;font-weight:bold;text-align:center;margin-top:10px;">FlexPaper Sample Document</div>
			<div style="padding: 5px 5px 5px 5px;font-size:11px;text-align:left;margin-bottom:10px;">Can't see the document and running FlexPaper from your local directory?<br/><br/> Upload the contents of the zip file to a web server or make FlexPaper trusted to run locally. You can manage your trusted flash applications at <a href="http://www.macromedia.com/support/documentation/en/flashplayer/help/settings_manager04a.html#119065">Adobe's website</a>.</div>

			<div style="background-color:#EFEFEF">
				<div style="padding: 5px 5px 5px 5px;font-size:15px;font-weight:bold;text-align:center;margin-top:50px;">More Examples</div>
				<div style="padding: 5px 5px 5px 5px;font-size:11px;text-align:left;margin-bottom:10px;line-height:150%">
						» <a href="examples/two_page.html">FlexPaper starting up in Two-Page mode</a><br/>
						» <a href="examples/singlepage.html">FlexPaper using SinglePage mode</a><br/>
						» <a href="examples/interactive_API.html">Interactive API html page</a><br/>
						» <a href="examples/searchmatchall.html">Search and highlight all matches</a><br/>
						</div>

				<div style="padding: 5px 5px 5px 5px;font-size:15px;font-weight:bold;text-align:center;margin-top:50px;">Publishing with PHP</div>
				<div style="padding: 5px 5px 5px 5px;font-size:11px;text-align:left;margin-bottom:10px;line-height:150%">
						» <a href="php/simple_document.php">On the fly conversion (requires PHP5)</a><br/>
						» <a href="php/split_document.php">Split file conversion (requires PHP5)</a>
				</div>

				<div style="padding: 5px 5px 5px 5px;font-size:15px;font-weight:bold;text-align:center;margin-top:50px;">Documentation</div>
				<div style="padding: 5px 5px 5px 5px;font-size:11px;text-align:left;margin-bottom:10px;line-height:150%">
						» <a href="http://flexpaper.devaldi.com/docs_php.jsp">Configuring and Publishing with PHP</a><br/>
						» <a href="http://flexpaper.devaldi.com/docs_converting.jsp">Converting Documents</a><br/>
						» <a href="http://flexpaper.devaldi.com/docs_parameters.jsp">Parameters</a><br/>
						» <a href="http://flexpaper.devaldi.com/docs_api.jsp">API</a><br/>
				</div>
			</div>
		</div>
   </body>
</html>