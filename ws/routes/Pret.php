<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /pret', ['PretController', 'getDetailPret']);
Flight::route('POST /pret', ['PretController', 'create']);

