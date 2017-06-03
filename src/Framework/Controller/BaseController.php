<?php


namespace Raven\Framework\Controller;


use Raven\Framework\Container\ContainerAwareInterface;
use Raven\Framework\Container\ContainerTrait;
use Raven\Framework\Network\Request;
use Raven\Framework\Network\Response;

abstract class BaseController implements ContainerAwareInterface
{

    use ContainerTrait;
    protected $request;
    protected $response;
    protected $template = 'default';

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function initialize() {}

    public function render(string $view_path, array $vars = [])
    {
        $ds = DIRECTORY_SEPARATOR;
        $config = $this->container->get('config');
        $config_template = $config->get('template');
        $root_dir = $config->get('framework')['root_dir'];
        list($bundle, $folder, $view) = explode(':', $view_path);
        $builder = $this->container->get('template_builder');
        $builder->setTemplate($config_template['template_dir'] . $this->template . '.php');
        $view = $builder->createView($root_dir . $ds . $bundle . $ds . 'View' . $ds . $folder . $ds .  $view, [], $vars);
        $this->response->setContent($view->render());
        return $this->response;
    }

}