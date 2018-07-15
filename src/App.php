<?php
declare(strict_types = 1);
/**
 * Weave example app for a double-pass middleware stack.
 */

namespace App;

/**
 * Weave Example App for a double-pass middlleware stack.
 */
class App
{
	use \Weave\Weave, \Weave\Config\Zend\Zend, \Weave\Error\Whoops\Whoops, \Weave\Container\Aura\Aura;
	// ^^ Use Zend for config, Whoops for error handling and Aura for the DIC.

	/**
	 * Helpful const to control which environment we are in.
	 */
	const ENV_DEVELOPMENT = 'development';

	/**
	 * Provide the config classes for the DIC.
	 *
	 * This method is specific to the Aura.Di DIC - different DICs will have different needs.
	 * In Aura.Di's case, you can pass an array of class name strings and instances that meet
	 * the Aura\Di\ContainerConfigInterface interface to configure. In our case, we have an
	 * instance of App\Config we pass in.
	 *
	 * @param array  $config      Optional config array as provided from loadConfig().
	 * @param string $environment Optional indication of the runtime environment.
	 *
	 * @return array
	 */
	protected function provideContainerConfigs(array $config = [], $environment = null)
	{
		return [
			new Config($config)
		];
	}

	/**
	 * Provide middleware pipeline sets for Relay Middleware.
	 *
	 * This app uses Relay for the Middleware stack (see App\Config for the DIC setup that
	 * does this). Relay accepts an array of class name strings, invokable class instances
	 * and callables which are executed in the Middleware stack in the order provided.
	 *
	 * Weave supports the concept of multiple Middleware Stacks / Pipelines. The default
	 * pipeline has no name but you can refer to others in the Router config (see below).
	 * This method provides the arrays of middlewares on demand.
	 *
	 * If you want to use a Router (which is often the case), you simply decide where to
	 * place the router in the pipeline along with other middlewares. In the example here
	 * we don't do anything in the default pipeline other than trigger the router.
	 *
	 * You will also notice a second pipeline defined called 'uppercaseOwner'. This pipeline
	 * is dispatched via the router (see below). In cases like this if you still want to
	 * also call a Controller after executing some more middlewares, you can use the
	 * Dispatch middleware (as shown here) to decide when the Controller is executed.
	 *
	 * @param string $pipelineName The name of the pipeline to return a definition for.
	 *
	 * @return mixed Whatever the chosen Middleware stack uses for a pipeline of middlewares.
	 */
	protected function provideMiddlewarePipeline($pipelineName = null)
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

	/**
	 * Setup routes for the Router.
	 *
	 * In the example here, we are using Aura.Router - note that what happens in this method
	 * very much depends on which Router you decide to use. In the case of Aura.Router,
	 * an Aura\Router\Map instance is provided as $router.
	 *
	 * Here we are setting up a single route that simply routes '/' with an optional
	 * value which is provided as a Request attribute called 'owner' and which defaults
	 * to 'World'. If you called this app with '/wibble' then 'owner' would be set to
	 * 'wibble'.
	 *
	 * The single defined route has been configured with a handler string of
	 * 'uppercaseOwner|App\Controller\Hello->hello'
	 *
	 * To break this down it means that when the route is matched, the middleware pipeline
	 * called 'uppercaseOwner' will be dispatched but before doing this, the router will
	 * also provide the Request with configuration that allows a Dispatch middleware
	 * somewhere in the pipeline to dispatch to an instance of the Hello controller and
	 * execute the hello() method on that instance.
	 *
	 * Note that it is possible to chain multiple pipelines using the | and each chunk
	 * will be consumed by a Dispatch. It's also possible for middleware to modify,
	 * remove or create the attribute used by the Dispatch middleware.
	 *
	 * @param mixed $router The object to setup routes against.
	 *
	 * @return null
	 */
	protected function provideRouteConfiguration($router)
	{
		$router->get(
			'root',
			'{/owner}',
			['uppercaseOwner|', Controller\Hello::class . '->hello']
		)
		->defaults(['owner' => 'World']);
	}
}
