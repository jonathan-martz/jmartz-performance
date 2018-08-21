<?php

class RoboFile extends \Robo\Tasks
{
	public function execute($name)
	{
		$filename = 'page.json';
		$file = file_get_contents($filename);

		$folder = "reports";

		$this->_exec('mkdir '.$folder);

		if(strlen($file) > 0){
			$pages = json_decode($file, JSON_FORCE_OBJECT);
			foreach($pages as $page){
				if($page['name'] == $name){
					foreach($page['urls'] as $url){
						$this->_exec('lighthouse --output json --output-path --chrome-flags="--headless" '.$folder.'/'.$url['title'].'.json '.$url['url']);
					}
				}
			}
		}
	}

	public function copy(){
		$this->taskRsync()
			 ->fromPath('reports')
			 ->toHost('195.201.38.163')
			 ->toUser('root')
			 ->toPath('/var/www/performance.jmartz.de/')
			 ->recursive()
			 ->progress()
			 ->run();
	}
}

?>
