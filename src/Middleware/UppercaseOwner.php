<?php
declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UppercaseOwner
{
	public function __invoke(Request $request, Response $response, callable $next)
	{
		$owner = $request->getAttribute('owner');
		return $next($request->withAttribute('owner', strtoupper($owner)), $response);
	}
}