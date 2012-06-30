; <?php exit; ?> DO NOT REMOVE THIS LINE
[general]
 allowcache = true
 splitmode	= false
 path.pdf = "C:\inetpub\wwwroot\adaptiveui\pdf\"
 path.swf = "C:\inetpub\wwwroot\adaptiveui\docs\"
 
[external commands]
 cmd.conversion.singledoc 		= "\"C:\Program Files\SWFTools\pdf2swf.exe\" {path.pdf}{pdffile} -o {path.swf}{pdffile}.swf -f -T 9 -t -s storeallcharacters"
 cmd.conversion.splitpages 		= "\"C:\Program Files\SWFTools\pdf2swf.exe\" {path.pdf}{pdffile} -o {path.swf}{pdffile}%.swf -f -T 9 -t -s storeallcharacters -s linknameurl"
 cmd.conversion.renderpage 		= "\"C:\Program Files\SWFTools\swfrender.exe\" {path.swf}{swffile} -p {page} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio"
 cmd.conversion.rendersplitpage	= "\"C:\Program Files\SWFTools\swfrender.exe\" {path.swf}{swffile} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio"
 cmd.conversion.jsonfile		= "\"C:\Program Files\SWFTools\pdf2json.exe\" {path.pdf}{pdffile} -enc UTF-8 -compress {path.swf}{jsonfile}" 
 cmd.searching.extracttext 		= "\"C:\Program Files\SWFTools\swfstrings.exe\" {swffile}"
