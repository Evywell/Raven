<?php
return [
    'cms_homepage' =>
        [
            'path' => '/index/{id}/{slug}.txt',
            '_controller' => 'CMSBundle:Test:index',
            'parameters' => ['id' => '[0-9]+', 'slug' => '[a-z]+']
        ],
    'cms_homepage2' =>
        [
            'path' => '/',
            '_controller' => 'CMSBundle:Test:index2',
            '_middleware' => ['web']
        ],
    'cms_ip' =>
        [
            'path' => '/ip',
            '_controller' => 'CMSBundle:Test:ip',
            '_middleware' => ['ip']
        ],
];