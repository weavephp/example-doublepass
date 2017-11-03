<?php
declare(strict_types = 1);
/**
 * Weave example app Aura.Di config class.
 */

namespace App;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

/**
 * Weave example app Aura.Di config class.
 */
class Config extends ContainerConfig
{
	/**
	 * The config loaded in this case via Zend Config.
	 *
	 * @var array
	 */
	protected $_config;

	/**
	 * Constructor.
	 *
	 * @param array $config The config.
	 */
	public function __construct(array $config = [])
	{
		$this->_config = $config;
	}

	/**
	 * Define params, setters, and services before the Container is locked.
	 *
	 * @param Container $container The DI container.
	 *
	 * @return null
	 */
	public function define(Container $container)
	{
		// Specify we want to use Relay for our Middleware
		$container->types[\Weave\Middleware\MiddlewareAdaptorInterface::class] = $container->lazyNew(
			\Weave\Middleware\Relay\Relay::class
		);

		// Specify we want to use Zend Diactoros for our PSR7 stuff
		$container->types[\Weave\Http\ResponseEmitterInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\responseEmitter::class
		);

		$container->types[\Weave\Http\RequestFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\RequestFactory::class
		);

		$container->types[\Weave\Http\ResponseFactoryInterface::class] = $container->lazyNew(
			\Weave\Http\ZendDiactoros\ResponseFactory::class
		);

		// Specify we want to use Aura for our router
		$container->types[\Weave\Router\RouterAdaptorInterface::class] = $container->lazyNew(
			\Weave\Router\Aura\Aura::class
		);

		// Setup a parameter for our Hello Controller based on the content of the config.
		$container->params[\App\Controller\Hello::class] = ['message' => $this->_config['HelloMessage']];
	}
}