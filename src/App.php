<?php
declare(strict_types = 1);

namespace App;

class App
{
	use \Weave\Weave, \Weave\Config\Zend\Zend, \Weave\Error\Whoops\Whoops, \Weave\Container\Aura\Aura;

	const ENV_DEVELOPMENT = 'development';

	protected function _provideContainerConfigs(array $config = [], $environment = null)
	{
		return [
			new Config($config)
		];
	}

	protected function _provideMiddlewarePipeline($pipelineName = null)
	{
		switch ($pipelineName) {
			case 'uppercaseOwner':
				return [
					Middleware\UppercaseOwner::class,
					\Weave\Middleware\Dispatch::class
				];

			default:
				return [\Weave\Router\Router::class];
		}
	}

	protected function _provideRouteConfiguration($router)
	{
		$router->get('root', '{/owner}', 'uppercaseOwner|' . Controller\Hello::class . '->hello')
		->defaults(['owner' => 'World']);
	}
}
