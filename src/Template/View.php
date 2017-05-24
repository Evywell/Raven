<?php


namespace Raven\Template;


class View
{

    /**
     * @var string
     */
    private $view_name;
    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $vars;
    /**
     * @var Block[]
     */
    private $blocks;

    public function __construct(string $view_name, string $template, array $helpers = [], array $vars = [])
    {
        $this->view_name = $view_name;
        $this->template = $template;
        $this->vars = $vars;
        $this->generateHelpers($helpers);
    }

    private function generateHelpers(array $helpers)
    {
        foreach ($helpers as $helper_name => $helper) {
            $this->{$helper_name} = $helper;
        }
    }

    private function renderBlock($name)
    {

    }

    public function render()
    {

    }

    public function set(string $name, $value)
    {
        $this->blocks[$name] = $value;
    }

    public function get(string $name)
    {
        return $this->blocks[$name];
    }

    public function fetch(string $name)
    {
        $this->get($name)->render();
    }

}