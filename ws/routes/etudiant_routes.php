<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';

Flight::route('GET /etudiants', ['EtudiantController', 'getAll']);
Flight::route('GET /prets', ['EtudiantController', 'getAllPret']);
Flight::route('GET /paiements/@id_pret', ['EtudiantController', 'getPaiementsByPret']);
Flight::route('POST /valider_paiement', ['EtudiantController', 'rembourser']);

Flight::route('/login', ['EtudiantController', 'login']);
Flight::route('GET /etudiants/@id', ['EtudiantController', 'getById']);
Flight::route('POST /etudiants', ['EtudiantController', 'create']);
Flight::route('PUT /etudiants/@id', ['EtudiantController', 'update']);
Flight::route('DELETE /etudiants/@id', ['EtudiantController', 'delete']);
