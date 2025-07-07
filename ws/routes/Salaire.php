<?php
require_once __DIR__ . '/../controllers/SalaireController.php';

Flight::route('POST /insertSalaire', ['SalaireController', 'create']);
