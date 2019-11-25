<?php

class RoboFile extends \Robo\Tasks
{
	public function loadConfig(){
		$ip = '195.201.38.163';
		$user = 'root';

		$this->taskRsync()
			->toPath('.')
			->fromHost($ip)
			->fromUser($user)
			->fromPath('/var/www/performance.jmartz.de/shared/config')
			->recursive()
			->progress()
			->run();
	}

	public function execute()
	{
		$filename = 'config/lighthouse.json';
		$file = file_get_contents($filename);

		$folder = 'reports/'.date('d-m-y-H').'/';

		if(!file_exists('reports')){
			$this->_exec('mkdir reports');
		}

		if(!file_exists($folder)){
			$this->_exec('mkdir '.$folder);
		}

		$this->taskNpmInstall()->run();

		if(strlen($file) > 0){
			$pages = json_decode($file, JSON_FORCE_OBJECT);
			foreach($pages as $page){
				foreach($page['urls'] as $url){
					$this->_exec('lighthouse --output json --chrome-flags="--headless" --output-path '.$folder.'/'.$url['title'].'.json '.$url['url']);
				}
			}
		}
	}

	public function copy(){
		$this->taskRsync()
			 ->fromPath('reports')
			 ->toHost('195.201.38.163')
			 ->toUser('root')
			 ->toPath('/var/www/performance.jmartz.de/shared')
			 ->recursive()
			 ->progress()
			 ->run();
	}
}

?>
