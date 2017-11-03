<?php
declare(strict_types = 1);

namespace App;

require_once '../vendor/autoload.php';

(new App)->start(App::ENV_DEVELOPMENT, realpath(__DIR__ . '/../config'));
