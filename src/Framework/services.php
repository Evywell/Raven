<?php
use Raven\Framework\Router\RavenRouter;

$container->set('router', function ($globals, $container) {
    $router = new RavenRouter($container, $globals['root_dir'] . '/config/routing.php');
    // $router->setActiveCache(true);
    $router->initialize();
    return $router;
})->lock();

$container->set('session', \Raven\Framework\Session\Session::class)->lock();
$container->set('event_dispatcher', \Raven\Framework\Event\EventDispatcher::class)->lock();
$container->set('template_builder', \Raven\Template\ViewBuilder::class)->lock();