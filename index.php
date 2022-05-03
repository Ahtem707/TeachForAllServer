<?php

// Testing
include('testing/Console.php');

// Extension
include('extension/TokenGenerator.php');
include('extension/Date.php');
include('extension/Response.php');
include('extension/RequesMethod.php');

// Modules
include('modules/Constants.php');
// include('modules/Configuration.php');
include('modules/sqlManager.php');

// Router
include('router/index.php');

// Start
new Router();