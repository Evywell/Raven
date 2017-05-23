<?php


namespace Test\Raven\Event;


use Raven\Framework\Event\EventDispatcher;

class TestORM
{

    /**
     * @var EventManager
     */
    private $manager;
    private $entity;

    public function __construct(EventDispatcher $manager, $entity)
    {
        $this->manager = $manager;
        $this->entity = $entity;
    }

    public function save()
    {
        $event = new EntityEvent($this->entity);
        $this->manager->dispatch('orm.beforeSave', $event);
        $this->manager->dispatch('orm.afterSave', $event);
    }

}