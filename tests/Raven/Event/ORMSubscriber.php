<?php


namespace Test\Raven\Event;


use Raven\Framework\Event\Event;
use Raven\Framework\Event\SubscriberInterface;

class ORMSubscriber implements SubscriberInterface
{

    public function implementedEvents(): array
    {
        return [
            'orm.beforeSave' => [
                'callable' => 'beforeSave',
                'priority' => 22
            ],
            'orm.afterSave' => [
                'callable' => 'afterSave'
            ],
        ];
    }

    public function beforeSave(Event $event)
    {
        $entity = $event->getData();
        $entity->setContent('before' . $entity->getContent());
    }

    public function afterSave(Event $event)
    {
        $entity = $event->getData();
        $entity->setContent($entity->getContent() . 'after');
    }
}