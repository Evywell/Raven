<?php
// Autoloader
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
$time = microtime(true);
use App\Application;
$application = new Application();
$application->run();
echo "Temps de génération: " . (microtime(true) - $time) . " secondes " . ($application->isCacheActive() ? "avec " : "sans ") . "le cache";