<?php

define("_config", parse_ini_file("config.ini", true));
define("_isMoker", $_SERVER['SERVER_NAME'] == 'localhost');