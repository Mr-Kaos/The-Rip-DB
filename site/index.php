<?php
require 'vendor/autoload.php';

const VIEW_DIR = 'private_core/view/';
Flight::set('flight.views.path', VIEW_DIR);
require_once('private_core/routes.php');

Flight::start();
