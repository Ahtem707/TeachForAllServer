<?php

// Extension
include('extension/TokenGenerator.php');
include('extension/Date.php');
include('extension/Response.php');

// Modules
include('modules/Configuration.php');
include('modules/sqlManager.php');
include('modules/Router.php');

// Testing
include('testing/Console.php');

// Start
new Router();