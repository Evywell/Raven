<?php


namespace Raven\Template;


class ViewBuilder
{

    /**
     * @var string
     */
    protected $template;
    protected $helpers = [];
    protected $vars = [];

    public function createView(string $view_path, array $helpers = [], array $vars = [])
    {
        $this->addHelpers($helpers);
        $this->addVars($vars);

        return new View($view_path, $this->template, $this->helpers, $this->vars);
    }

    public function addHelpers(array $helpers)
    {
        $this->helpers = array_merge($this->helpers, $helpers);

        return $this;
    }

    public function addVars(array $vars)
    {
        $this->vars = array_merge($this->vars, $vars);

        return $this;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

}