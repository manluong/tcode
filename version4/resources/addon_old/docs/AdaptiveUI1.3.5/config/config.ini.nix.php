; <?php exit; ?> DO NOT REMOVE THIS LINE
[general]
 allowcache = true
 splitmode	= false
 path.pdf = "/tmp/"
 path.swf = "/tmp/"

[external commands]
 cmd.conversion.singledoc 			= "/opt/local/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}.swf -f -T 9 -t -s storeallcharacters"
 cmd.conversion.splitpages 			= "/opt/local/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}_%.swf -f -T 9 -t -s storeallcharacters -s linknameurl"
 cmd.conversion.renderpage 			= "/opt/local/bin/swfrender {path.swf}{swffile} -p {page} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio"
 cmd.conversion.rendersplitpage 	= "/opt/local/bin/swfrender {path.swf}{swffile} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio"
 cmd.conversion.jsonfile			= "/usr/local/bin/pdf2json {path.pdf}{pdffile} -enc UTF-8 -compress {path.swf}{jsonfile}"
 cmd.searching.extracttext 			= "/opt/local/bin/swfstrings {swffile}"
