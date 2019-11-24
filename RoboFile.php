<?php

class RoboFile extends \Robo\Tasks
{
	public function execute($name)
	{
		$filename = 'page.json';
		$file = file_get_contents($filename);

		$folder = 'reports/'.date('d-m-y-H').'/';

		$this->_exec('mkdir reports');
		$this->_exec('mkdir '.$folder);

		$this->taskNpmInstall()->run();

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
