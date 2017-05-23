<?php
$container->set('cms.event.subscriber', function($global, $container) {
    $subscriber = new \App\CMSBundle\Event\CMSSubscriber();
    $container->get('event_dispatcher')->addSubscriber($subscriber);
});