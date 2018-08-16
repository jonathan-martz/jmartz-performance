<?php

class RoboFile extends \Robo\Tasks
{
	public function execute($name)
	{
		$filename = 'page.json';
		$file = file_get_contents($filename);

		$folder = "reports";

		if(strlen($file) > 0){
			$pages = json_decode($file, JSON_FORCE_OBJECT);
			foreach($pages as $page){
				if($page['name'] == $name){
					foreach($page['urls'] as $url){
						$this->_exec('lighthouse --output json --output-path '.$folder.'/'.$url['title'].'.json '.$url['url']);
					}
				}
			}
		}
	}
}

?>
