<?php


namespace Test\Raven\Template;


use Raven\Template\ViewBuilder;
use Test\Raven\RavenTestCase;
use Test\Raven\Template\Helpers\HtmlHelper;

class ViewBuilderTest extends RavenTestCase
{

    public function testCreateView()
    {
        $builder = new ViewBuilder();
        $builder->setTemplate(__DIR__ . '/views/default.php');
        $builder->addHelpers(['html' => new HtmlHelper()]);
        $view = $builder->createView(__DIR__ . '/views/home.php');
        $content = $view->render();
        var_dump($content);
    }

}