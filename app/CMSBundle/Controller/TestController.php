<?php


namespace App\CMSBundle\Controller;


use Raven\Framework\Controller\BaseController;
use Raven\Framework\Event\BasicEvent;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class TestController extends BaseController
{

    public function index(Request $request, $id, $slug)
    {
        return $this->render('CMSBundle:Test:index.php', compact('id', 'slug'));
    }

    public function index2(Request $request)
    {
        return $this->render('CMSBundle:Test:index2.php', []);
    }

    public function ip()
    {
        return $this->response;
    }

    public function add()
    {
        $post = new \stdClass();
        $post->name = "Super article";
        $post->content = "Je suis un article super intéressant !";

        // Ajout du slug via un événement (ça ne sert pas à grand chose, mais c'est un exemple)
        $manager = $this->container->get('event_dispatcher');
        $manager->dispatch('cms.add_post', new BasicEvent($post));

        return new Response("Article ajouté <pre>" . print_r($post, true) . "</pre>");
    }

    public function edit(Request $request, $id)
    {
        return new Response("Edition id: " . $id);
    }

}