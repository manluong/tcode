; <?php exit; ?> DO NOT REMOVE THIS LINE
[general]
 allowcache = false
 path.pdf = "/Applications/XAMPP/xamppfiles/htdocs/tcode/tmp/"
 path.swf = "/Applications/XAMPP/xamppfiles/htdocs/tcode/tmp/"

[external commands]
 cmd.conversion.singledoc 	= "/opt/local/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}.swf -f -T 9 -t -s storeallcharacters"
 cmd.conversion.splitpages 	= "/opt/local/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}%.swf -f -T 9 -t -s storeallcharacters -s linknameurl"
 cmd.searching.extracttext = "/opt/local/bin/swfstrings {path.swf}{swffile}"
