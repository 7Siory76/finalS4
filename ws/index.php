<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
Flight::set('flight.views.path', __DIR__ . '/');
require 'routes/Salaire.php';
require 'routes/Pret.php';



Flight::start();