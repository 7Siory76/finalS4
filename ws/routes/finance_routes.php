<?php 
require_once __DIR__ . '/../controllers/FinanceController.php';

Flight::route('POST /finance', ['FinanceController', 'ajouterFond']);
