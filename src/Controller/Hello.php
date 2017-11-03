<?php
declare(strict_types = 1);

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Hello
{
	protected $_message;

	public function __construct($message) {
		$this->_message = $message;
	}

	public function hello(Request $request, Response $response)
	{
		$owner = $request->getAttribute('owner');
		$response->getBody()->write($this->_message . ", " . $owner . "\n");
		return $response->withHeader('Content-Type', 'text/plain');
	}
}