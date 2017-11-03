<?php
declare(strict_types = 1);
/**
 * Weave example app Middleware.
 */

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Weave example app Middleware.
 *
 * All this middleware does is uppercase the 'owner' attribute. Take a look in the
 * App class to see how this comes from the url by using a Router route config and
 * then take a look at the Hello Controller to see how it is used.
 *
 * Note that in this example app we are using Relay, a double-pass middleware pipeline
 * so we have a double-pass invoke signature. In a single-pass middleware you ould need
 * to provide a process() method instead, that takes different parameters depending
 * on the PSR15 draft you are working to.
 *
 * @param Request  $request  The Request.
 * @param Response $response The Response.
 * @param callable $next     A callable to pass execution on to the next middleware.
 *
 * @return Response
 */
class UppercaseOwner
{
	public function __invoke(Request $request, Response $response, callable $next)
	{
		$owner = $request->getAttribute('owner');
		return $next($request->withAttribute('owner', strtoupper($owner)), $response);
	}
}