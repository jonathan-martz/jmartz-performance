<?php

use \Robo\Tasks;

/**
 * Class RoboFile
 */
class RoboFile extends Tasks
{
    /**
     * @var array
     */
    const config = 'config';

    /**
     * @var string
     */
    const server = 'server.json';

    /**
     * @var string
     */
    const lighthouse = 'lighthouse.json';

    /**
     * @var string
     */
    const reports = 'reports';

    /**
     * @var string
     */
    public $date = '';

    /**
     * @var array
     */
    public $config = [];

    /**
     * RoboFile constructor.
     */
    public function __construct()
    {
        $this->date = date('d-m-y-H-i');
    }

	/**
	 * @return void
	 */
	public function downloadConfig(): void
    {
        // Todo: replace strings with config
        $ip = '195.201.38.163';
        $user = 'root';
        $folder = '/var/www/performance.jmartz.de/shared/';

        $this->taskRsync()
            ->toPath('.')
            ->fromHost($ip)
            ->fromUser($user)
            ->fromPath($folder . self::config)
            ->recursive()
            ->progress()
            ->run();
    }

	/**
	 * @return array
	 */
	public function loadLighthouseConfig(): array
    {
        $filename = self::config . '/' . self::lighthouse;
        $file = file_get_contents($filename);
        return json_decode($file, JSON_FORCE_OBJECT);
    }

	/**
	 * @return array
	 */
	public function loadServerConfig(): array
    {
        $filename = self::config . '/' . self::server;
        $file = file_get_contents($filename);
        return json_decode($file, JSON_FORCE_OBJECT);
    }

	/**
	 * @return void
	 */
	public function execute(): void
    {
        $pages = $this->loadLighthouseConfig();

        $folder = self::reports . '/' . $this->date;

        if (!file_exists(self::reports)) {
            $this->_exec('mkdir ' . self::reports);
        }

        if (!file_exists($folder)) {
            $this->_exec('mkdir ' . $folder);
        }

        if (count($pages) > 0) {
            $exec = $this->taskParallelExec();
            foreach ($pages as $page) {
                foreach ($page['urls'] as $url) {
                    $exec->process('lighthouse --output json --chrome-flags="--headless" --output-path ' . $folder . '/lighthouse-' . $url['title'] . '.json ' . $url['url']);
                }
            }
            $exec->run();
        }
    }

	/**
	 * @return void
	 */
	public function copy(): void
    {
        $this->config['server'] = $this->loadServerConfig();

        $this->taskRsync()
            ->fromPath('reports')
            ->toHost($this->config['server']['ip'])
            ->toUser($this->config['server']['user'])
            ->toPath($this->config['server']['folder'])
            ->recursive()
            ->progress()
            ->run();

        $this->taskSshExec($this->config['server']['ip'], $this->config['server']['user'])
            ->remoteDir($this->config['server']['folder'])
            ->exec('chown -R www-data:www-data '.self::reports)
            ->run();
    }
}

?>
