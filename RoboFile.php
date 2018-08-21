<?php

class RoboFile extends \Robo\Tasks
{
	public function execute($name)
	{
		$filename = 'page.json';
		$file = file_get_contents($filename);

		$folder = 'reports/'.date('d-m-y-H').'/';

		$this->_exec('mkdir '.$folder);

		if(strlen($file) > 0){
			$pages = json_decode($file, JSON_FORCE_OBJECT);
			foreach($pages as $page){
				if($page['name'] == $name){
					foreach($page['urls'] as $url){
						$this->_exec('lighthouse --output json --chrome-flags="--headless" --output-path '.$folder.'/'.$url['title'].'.json '.$url['url']);
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
			 ->toPath('/var/www/performance.jmartz.de/shared/reports')
			 ->recursive()
			 ->progress()
			 ->run();
	}
}

?>
