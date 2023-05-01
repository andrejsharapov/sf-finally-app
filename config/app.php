<?php

require_once dirname(__DIR__, 1) . '/app/Models/Model.php';
require_once dirname(__DIR__, 1) . '/resources/views/View.php';
require_once dirname(__DIR__, 1) . '/app/Http/Controllers/Controller.php';
require_once dirname(__DIR__, 1) . '/routes/Route.php';
require_once dirname(__DIR__, 1) . '/database/db.php';

(new Route)->start();

