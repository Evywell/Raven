<?php
namespace Test\Raven\Middleware;

use Raven\Framework\Container\Container;
use Raven\Framework\Middleware\MiddlewareQueue;
use Raven\Framework\Middleware\MiddlewareRunner;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;
use Test\Raven\RavenTestCase;

class MiddlewareTest extends RavenTestCase
{

    /**
     * @var MiddlewareRunner
     */
    private $runner;
    /**
     * @var Response;
     */
    private $response;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var MiddlewareQueue
     */
    private $queue;
    /**
     * @var array
     */
    private $middleware;

    protected function setUp()
    {
        parent::setUp();
        $this->request = new Request();
        $this->response = new Response();
        $this->queue = new MiddlewareQueue(new Container());
        $middleware = [
            \Test\Raven\Middleware\FooMiddleware::class,
            \Test\Raven\Middleware\BarMiddleware::class
        ];
        $this->middleware = $middleware;
        foreach ($middleware as $item) {
            $this->queue->add($item);
        }
        $this->runner = new MiddlewareRunner();
    }

    public function testRunMiddlewareWithSpecificQueue()
    {
        $this->queue->clear();
        $this->queue->add(\Test\Raven\Middleware\FooMiddleware::class);
        $response = $this->runner->run($this->queue, $this->request, $this->response);
        $this->assertEquals("foo", $response->getContent());

        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $response = $this->runner->run($this->queue, $this->request, $this->response);
        $this->assertEquals("foobar", $response->getContent());
    }

    public function testAddMiddlewareInQueue()
    {
        // Initial test
        $count = $this->queue->count();
        $this->assertCount($count, $this->middleware);

        $class = \Test\Raven\Middleware\BarMiddleware::class;
        $this->queue->add($class);
        $this->assertEquals($count + 1, $this->queue->count());

        $this->queue->clear();
        for ($i = 0; $i < 3; $i++) {
            $this->queue->add(\Test\Raven\Middleware\FooMiddleware::class);
        }
        $this->assertEquals(3, $this->queue->count());

        $instance = new \Test\Raven\Middleware\BarMiddleware();
        // Test add at first position
        $this->queue->addFirst(\Test\Raven\Middleware\BarMiddleware::class);
        $middleware = $this->queue->get(0);
        $this->assertEquals($instance, $middleware);

        // Test add at end position
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $middleware = $this->queue->get($this->queue->count() - 1);
        $this->assertEquals($instance, $middleware);

        // Test add at $index position (0 to count() - 1)
        $index = 2;
        $this->queue->addAt($index, \Test\Raven\Middleware\BarMiddleware::class);
        $this->assertEquals($instance, $this->queue->get($index));
    }

    public function testAddAtInQueue()
    {
        $index = 0;
        $this->queue->clear();
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
        $instance = new \Test\Raven\Middleware\FooMiddleware();
        $this->assertEquals($instance, $this->queue->get($index));

        $index = 1;
        $this->queue->clear();
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
        $this->assertEquals($instance, $this->queue->get($index));


        $index = 2;
        $this->queue->clear();
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
        $this->assertEquals($instance, $this->queue->get($this->queue->count() - 1));

        $this->expectException(\InvalidArgumentException::class);
        $index = 3;
        $this->queue->clear();
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
        $this->expectException(\InvalidArgumentException::class);
        $index = 5;
        $this->queue->clear();
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
        $index = -1;
        $this->queue->clear();
        $this->queue->addAt($index, \Test\Raven\Middleware\FooMiddleware::class);
    }

    public function testClearQueue()
    {
        $this->queue->clear();
        $this->assertEquals(0, $this->queue->count());
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->clear();
        $this->assertEquals(0, $this->queue->count());
    }

    public function testUnsetMiddleware()
    {
        $this->queue->clear();
        $this->queue->add(\Test\Raven\Middleware\BarMiddleware::class);
        $this->queue->add(\Test\Raven\Middleware\FooMiddleware::class);
        $fooInstance = new \Test\Raven\Middleware\FooMiddleware();
        $this->queue->unset(0);
        $this->assertEquals($fooInstance, $this->queue->get(0));
    }

}