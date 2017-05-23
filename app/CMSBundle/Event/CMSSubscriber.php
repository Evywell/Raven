<?php


namespace App\CMSBundle\Event;


use Raven\Framework\Event\BasicEvent;
use Raven\Framework\Event\SubscriberInterface;

class CMSSubscriber implements SubscriberInterface
{

    public function implementedEvents(): array
    {
        return [
            'cms.add_post' => ['callable' => 'addNewPost']
        ];
    }

    public function addNewPost(BasicEvent $event)
    {
        $post = $event->getData();
        $post->slug = $this->generateSlug($post->name);
    }

    private function generateSlug(string $name)
    {
        return strtolower(str_replace([' ', '_'], '-', $name));
    }
}