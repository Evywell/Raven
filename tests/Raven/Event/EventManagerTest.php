<?php


namespace Test\Raven\Event;


use Raven\Framework\Event\EventDispatcher;
use Test\Raven\RavenTestCase;

class EventDispatcherTest extends RavenTestCase
{

    private $manager;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = new EventDispatcher();
    }

    public function testAddListener()
    {
        $this->manager->on('kernel.start', function() { return true; });
        $this->assertTrue($this->manager->hasListener('kernel.start'));
    }

    public function testDispatchSubscribedEvents()
    {
        $entity = new TestEntity();
        $entity->setName('entity_name');
        $entity->setContent('entity_content');
        $orm = new TestORM($this->manager, $entity);
        // Add subscriber on beforeSave and afterSave events
        $subscriber = new ORMSubscriber();
        $this->manager->addSubscriber($subscriber);
        // Do modifications
        $orm->save();

        $this->assertEquals('entity_name', $entity->getName());
        $this->assertEquals('beforeentity_contentafter', $entity->getContent());
    }

}