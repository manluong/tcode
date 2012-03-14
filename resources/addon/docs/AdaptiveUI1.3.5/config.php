<?php
class Config{
  protected $config;

    public function __construct($domain)
    {
			/*
		if(	PHP_OS == "WIN32" || PHP_OS == "WINNT"	)
      		$this->config = parse_ini_file('config/config.ini.win.php');
		else
			$this->config = parse_ini_file('config/config.ini.nix.php');

			 */
		if (! is_dir('tmp/'.$domain.'/docs/files/')) {
			$oldumask = umask(0);
			mkdir('tmp/'.$domain.'/docs/files/', 0777, true);
			umask($oldumask);
		}
		$this->config['allowcache'] = true;
		$this->config['splitmode'] = false;
		$this->config['path.pdf'] = 'tmp/'.$domain.'/docs/files/';
		$this->config['path.swf'] = 'tmp/'.$domain.'/docs/files/';

		///usr/local/bin/pdf2json
		$this->config['cmd.conversion.singledoc'] = '/usr/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}.swf -f -T 9 -t -s storeallcharacters';
		$this->config['cmd.conversion.splitpages'] = '/usr/bin/pdf2swf {path.pdf}{pdffile} -o {path.swf}{pdffile}_%.swf -f -T 9 -t -s storeallcharacters -s linknameurl';
		$this->config['cmd.conversion.renderpage'] = '/usr/bin/swfrender {path.swf}{swffile} -p {page} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio';
		$this->config['cmd.conversion.rendersplitpage'] = '/usr/bin/swfrender {path.swf}{swffile} -o {path.swf}{pdffile}_{page}.png -X 1024 -s keepaspectratio';
		$this->config['cmd.conversion.jsonfile'] = '/usr/local/bin/pdf2json {path.pdf}{pdffile} -enc UTF-8 -compress {path.swf}{jsonfile}';
		$this->config['cmd.searching.extracttext'] = '/usr/bin/swfstrings {swffile}';
    }

    public function getConfig($key = null)
    {
      if($key !== null)
      {
        if(isset($this->config[$key]))
        {
          return $this->config[$key];
        }
        else
        {
          throw new Exception("Unknown key '$key' in configuration");
        }
      }
      else
      {
        return $this->config;
      }
    }

    public function setConfig($config)
    {
      $this->config = $config;
    }

	public function getDocUrl(){
		return "<br/><br/>Click <a href='http://flexpaper.devaldi.com/docs_php.jsp'>here</a> for more information on configuring FlexPaper with PHP";
	}
}