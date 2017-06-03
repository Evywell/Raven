<?php
$configurator->add('framework', function ($globals) {
    return [
        'environment' => 'dev',
        'root_dir' => $globals['root_dir']
    ];
});

$configurator->add('template', function ($globals) {
    return [
        'template_dir' => $globals['root_dir'] . '/template/'
    ];
});

