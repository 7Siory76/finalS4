<?php
// Fichier : routes.php
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /create_type_pret', ['TypePretController', 'ajouterTypePret']);
Flight::route('GET /pret/taux', ['PretController', 'getTaux']);