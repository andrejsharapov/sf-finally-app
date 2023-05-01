<?php

require_once dirname(__DIR__) . '/app/Models/Model.php';
require_once dirname(__DIR__) . '/resources/views/View.php';
require_once dirname(__DIR__) . '/app/Http/Controllers/Controller.php';
require_once dirname(__DIR__) . '/routes/Route.php';
require_once dirname(__DIR__) . '/database/db.php';

(new Route)->start();

