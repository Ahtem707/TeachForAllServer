<?php

// Testing
include('testing/Console.php');

// Extension
include('extension/TokenGenerator.php');
include('extension/Date.php');
include('extension/Response.php');

// Modules
include('modules/Constants.php');
// include('modules/Configuration.php');
include('modules/sqlManager.php');
include('modules/Router.php');

// Start
new Router();