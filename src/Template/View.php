<?php


namespace Raven\Template;


use PHPUnit\Framework\Exception;

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
    /**
     * @var array
     */
    private $helpers;
    /**
     * @var string
     */
    private $block_path;
    private $current_block;

    public function __construct(string $view_name, string $template, array $helpers = [], array $vars = [], string $block_path = null)
    {
        $this->view_name = $view_name;
        $this->template = $template;
        $this->vars = $vars;
        $this->helpers = $helpers;
        $this->blocks = [];
        $this->block_path = $block_path ?: dirname(dirname($view_name));
        $this->initialize();
    }

    private function initialize()
    {
        $this->generateHelpers($this->helpers);
        $block_content = new Block();
        $block_content->setContent($this->renderBlock($this->view_name, $this->vars));
        $this->set('content', $block_content);
    }

    private function generateHelpers(array $helpers)
    {
        foreach ($helpers as $helper_name => $helper) {
            $this->{ucfirst($helper_name)} = $helper;
        }
    }

    /**
     * @param $file_name
     * @param array $vars
     * @return string
     */
    private function renderBlock($file_name, array $vars = []): string
    {
        extract($vars);
        ob_start();
            require $file_name;
        return ob_get_clean();
    }

    public function fetch(string $block_name): string
    {
        if(!$this->hasBlock($block_name) && !$this->loadBlock($block_name)) {
            throw new Exception("The block " . $block_name . " does not exist");
        }
        return $this->get($block_name)->getContent();
    }

    public function block(string $block_name, $vars = [])
    {
        $this->verifBlocksClosed();
        $this->current_block = $block_name;
        if(is_callable($vars)) {
            $vars = call_user_func($vars);
        }
        $this->vars = array_merge($this->vars, $vars);
        ob_start();
    }

    public function endBlock()
    {
        $content = ob_get_clean();
        $block = new Block();
        $block->setContent($content);
        $this->set($this->current_block, $block);
        $this->current_block = null;
    }

    private function verifBlocksClosed()
    {
        if($this->current_block !== null){
            throw new Exception(sprintf("The Block %s is not close", $this->current_block));
        }
    }

    private function loadBlock(string $block_name): bool
    {
        $block_file = $this->block_path . DIRECTORY_SEPARATOR . 'Blocks' . DIRECTORY_SEPARATOR . $block_name . '.php';
        if(file_exists($block_file)) {
            require $block_file;
            return true;
        }
        return false;
    }

    public function render()
    {
        $this->verifBlocksClosed();
        return $this->renderBlock($this->template, $this->vars);
    }

    private function hasBlock($block_name): bool
    {
        return array_key_exists($block_name, $this->blocks);
    }

    public function set(string $name, Block $value)
    {
        $this->blocks[$name] = $value;
    }

    public function get(string $name)
    {
        return $this->blocks[$name];
    }

}