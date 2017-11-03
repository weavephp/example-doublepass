<?php
declare(strict_types = 1);

namespace App;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
	protected $_config;

	public function __construct(array $config = [])
	{
		$this->_config = $config;
	}

	public function define(Container $container)
	{
		$container->types[\Weave\Middleware\MiddlewareAdaptorInterface::class] = $container->lazyNew(
			\Weave\Middleware\Relay\Relay::class
		);

		$container->types[\Weave\Http\ResponseEmitterInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\responseEmitter::class
		);

		$container->types[\Weave\Http\RequestFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\RequestFactory::class
		);

		$container->types[\Weave\Http\ResponseFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\ResponseFactory::class
		);

		$container->types[\Weave\Router\RouterAdaptorInterface::class] = $container->lazyNew(
			\Weave\Router\Aura\Aura::class
		);

		$container->params[\App\Controller\Hello::class] = ['message' => $this->_config['HelloMessage']];
	}

	public function modify(Container $container)
	{
	}
}