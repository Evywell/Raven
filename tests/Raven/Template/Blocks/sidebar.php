<?php
class PostsController{

    public function sidebar()
    {
        return ['items' => ['Accueil', 'Blog']];
    }

}

$controller = new PostsController();
$this->block('sidebar', [$controller, 'sidebar']);
?>
<ul>
    <?php foreach ($this->vars['items'] as $item): ?>
        <li><?= $item ?></li>
    <?php endforeach; ?>
</ul>
<?php
$this->endBlock();
?>
