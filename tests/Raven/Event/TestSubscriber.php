<?php


namespace Test\Raven\Event;


use Raven\Framework\Event\SubscriberInterface;

class TestSubscriber implements SubscriberInterface
{

    public function implementedEvents(): array
    {
        return [
            'kernel.start' => [
                'callable' => 'test2',
                'priority' => 22
            ],
        ];
    }

    public function qsdqsd($nom, $prenom)
    {
        echo $nom . " " . $prenom;
    }

    public function test2($nom, $prenom)
    {
        return $prenom . " " . $nom;
    }
}