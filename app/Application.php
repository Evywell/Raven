<?php


namespace App;


use Raven\Framework\BaseApplication;

class Application extends BaseApplication
{

    public function registerBundle(): array
    {
        $bundles = [
            new \App\CMSBundle\CMSBundle(),
        ];

        if(in_array($this->getEnvironment(), ['dev', 'test'])) {
            // Add a bundle like $bundles[] = new Namespace\BundleName();
        }
        return $bundles;
    }

    public function registerMiddleware(): array
    {
        return [
            // \Some\Middleware\Class::class
        ];
    }

    public function registerRouteMiddleware(): array
    {
        $middleware = [
            // 'csrf' => \Raven\Middleware\CsrfMiddleware::class
        ];

        return $middleware;
    }

}