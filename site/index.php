<?php

/**
 * RipDB index
 * All requests are sent through here and routed through routes.php
 */
require 'vendor/autoload.php';

const VIEW_DIR = 'private_core/view/';
Flight::set('flight.views.path', VIEW_DIR);
require_once('private_core/routes.php');
Flight::start();
