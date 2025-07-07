<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
Flight::set('flight.views.path', __DIR__ . '/');
require 'routes/Salaire.php';
require 'routes/Pret.php';

require 'routes/finance_routes.php';
require 'routes/type_pret_routes.php';


Flight::start();