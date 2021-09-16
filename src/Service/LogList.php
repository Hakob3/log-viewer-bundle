<?php

namespace Evotodi\LogViewerBundle\Service;


use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class LogList
{
	private ParameterBagInterface $parameterBag;
	private array $logFiles;
	private bool $useAppLogs;
    private ?string $appPattern;
    private ?string $appDateFormat;

	protected array $levels = [
		"debug" => "DEBUG",
        "info" => "INFO",
        "notice" => "NOTICE",
        "warning" => "WARNING",
        "error" => "ERROR",
        "alert" => "ALERT",
        "critical" => "CRITICAL",
        "emergency" => "EMERGENCY",
	];
	public function __construct(ParameterBagInterface $parameterBag, array $logFiles, bool $useAppLogs = false, ?string $appPattern = null, ?string $appDateFormat = null)
	{
		$this->parameterBag = $parameterBag;
		$this->logFiles = $logFiles;
		$this->useAppLogs = $useAppLogs;
        $this->appPattern = $appPattern;
        $this->appDateFormat = $appDateFormat;
        if(is_null($this->appDateFormat)){
            $this->appDateFormat = 'Y-m-d H:i:s';
        }
    }

	public function getLogList(): array
    {
	    $logs = [];
		$id = 0;

	    if($this->useAppLogs){
		    $finder = new Finder();
            /** @noinspection MissingService */
            $finder->files()->in($this->parameterBag->get('kernel.logs_dir'));

		    foreach ($finder as $file){
			    $logs[] = ['id' => $id, 'name' => $file->getFilename(), 'path' => $file->getRealPath(), 'pattern' => $this->appPattern, 'days' => 0, 'date_format' => $this->appDateFormat, 'exists' => true, 'levels' => $this->levels];
			    $id++;
		    }
	    }

	    foreach ($this->logFiles as $logFile){
	    	$exists = true;
	    	if(!file_exists($logFile['path'])){
	    		$exists = false;
		    }
		    if(is_null($logFile['name'])){
			    $logFile['name'] = basename($logFile['path']);
		    }
		    $logs[] = ['id' => $id, 'name' => $logFile['name'], 'path' => $logFile['path'], 'pattern' => $logFile['pattern'], 'days' => $logFile['days'], 'date_format' => $logFile['date_format'], 'exists' => $exists, 'levels' => $logFile['levels']];
		    $id++;
	    }
        return $logs;
    }
}
