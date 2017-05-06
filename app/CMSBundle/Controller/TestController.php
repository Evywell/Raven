<?php


namespace App\CMSBundle\Controller;


use Raven\Framework\Controller\BaseController;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

class TestController extends BaseController
{

    public function index(Request $request, $id, $slug)
    {
        return new Response("lol");
    }

    public function index2(Request $request)
    {
        return new Response("INDEX 2");
    }

}