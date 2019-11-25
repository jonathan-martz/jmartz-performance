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

		$folder = 'reports/'.date('d-m-y-H');

		if(!file_exists('reports')){
			$this->_exec('mkdir reports');
		}

		if(!file_exists($folder)){
			$this->_exec('mkdir '.$folder);
		}

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
		$ip = '195.201.38.163';
		$user = 'root';
		$dir = '/var/www/performance.jmartz.de/shared/';

		$this->taskRsync()
			 ->fromPath('reports')
			 ->toHost($ip)
			 ->toUser($user)
			 ->toPath($dir)
			 ->recursive()
			 ->progress()
			 ->run();

		$this->taskSshExec($ip, $user)
			->remoteDir($dir)
			->exec('chown -R www-data:www-data reports')
			->run();

	}
}

?>
