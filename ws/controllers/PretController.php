<?php
require_once __DIR__ . '/../models/PretModel.php';
require_once __DIR__ . '/../helpers/Utils.php';



class PretController {
    public static function getAll() {
        $Prets = PretModel::getAll();
        Flight::json($Prets);
    }
    public static function getDetailPret() {
        $Prets = PretModel::getPretDetail();
        Flight::json($Prets);
    }


    public static function getById($id) {
        $Pret = PretModel::getById($id);
        Flight::json($Pret);
    }

    public static function create() {
        $email = Flight::request()->data->email;
        $Pret = Flight::request()->data->Pret;
        $data=['email'=>$email,
                'Pret_mensuel'=>$Pret];
        $id = PretModel::create($data);
        Flight::json(['message' => 'Étudiant ajouté']);
    }

    

    
}
