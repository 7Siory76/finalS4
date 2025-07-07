<?php
require_once __DIR__ . '/../models/Etudiant.php';
require_once __DIR__ . '/../models/Pret_rModel.php';
require_once __DIR__ . '/../helpers/Utils.php';



class EtudiantController {
    public static function getAll() {
        $etudiants = Etudiant::getAll();
        Flight::json($etudiants);
    }
    public static function getAllPret() {
    try {
        $Prets = Pret_rModel::getAll();
        if (!is_array($Prets)) {
            Flight::json(["error" => "Données invalides ou vide"]);
        } else {
            Flight::json($Prets);
        }
    } catch (Exception $e) {
        Flight::json(["error" => $e->getMessage()]);
    }
}

    public static function getPaiementsByPret($id_pret) {
        $db = getDB();
        $stmt = $db->prepare("SELECT mois, annee, montant, Id_status FROM montant_a_payer_par_mois WHERE Id_pret = ? ORDER BY annee, mois");
        $stmt->execute([$id_pret]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        Flight::json($rows);
    }
    public static function getById($id) {
        $etudiant = Etudiant::getById($id);
        Flight::json($etudiant);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Etudiant::create($data);
        $dateFormatted = Utils::formatDate('2025-01-01');
        Flight::json(['message' => 'Étudiant ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Etudiant::update($id, $data);
        Flight::json(['message' => 'Étudiant modifié']);
    }

    public static function delete($id) {
        Etudiant::delete($id);
        Flight::json(['message' => 'Étudiant supprimé']);
    }
    public static function login(){
        Flight::render('login');
    }
    public static function rembourser() {
    $db = getDB();
    $data = Flight::request()->data;

    $id_pret = $data['id_pret'];
    $mois = $data['mois'];
    $annee = $data['annee'];
    $montant = $data['montant'];
    $date = $data['date_remboursement'];

    // Récupérer client
    $stmt = $db->prepare("SELECT Id_client FROM pret WHERE Id_pret = ?");
    $stmt->execute([$id_pret]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        Flight::json(['message' => "Prêt introuvable."], 400);
        return;
    }
    $id_client = $row['Id_client'];

    $stmt = $db->prepare("SELECT date_debut, date_fin, montant_total, Id_type_pret FROM pret WHERE Id_pret = ?");
    $stmt->execute([$id_pret]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        Flight::json(['message' => "Prêt introuvable."], 400);
        return;
    }

    $date_debut = new DateTime($pret['date_debut']);
    $date_fin = new DateTime($pret['date_fin']);

    $interval = $date_debut->diff($date_fin);
    $duree_mois = $interval->y * 12 + $interval->m;
    if ($interval->d > 0) $duree_mois++;

    $montant_total = $pret['montant_total'];

    // Récupérer taux d'intérêt annuel
    $stmt = $db->prepare("SELECT taux_interet_annuel FROM type_pret WHERE Id_type_pret = ?");
    $stmt->execute([$pret['Id_type_pret']]);
    $type_pret = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$type_pret) {
        Flight::json(['message' => "Type de prêt introuvable."], 400);
        return;
    }
    $taux_annuel = $type_pret['taux_interet_annuel'];

    // Calculs
    $capital_mensuel = $montant_total / $duree_mois;
    $taux_mensuel_montant = $montant-$capital_mensuel;

    // Insérer dans remboursement
    $stmt = $db->prepare("INSERT INTO remboursement (Id_client, date_remboursement_, montant, Id_pret) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_client, $date, $montant, $id_pret]);
    $id_remboursement = $db->lastInsertId();

    // Insérer dans taux_interet_par_mois
    $stmt = $db->prepare("INSERT INTO taux_interet_par_mois (montant, mois, annee, Id_remboursement) VALUES (?, ?, ?, ?)");
    $stmt->execute([$taux_mensuel_montant, $mois, $annee, $id_remboursement]);

    // Mettre à jour le statut
    $stmt = $db->prepare("UPDATE montant_a_payer_par_mois SET Id_status = 4 WHERE Id_pret = ? AND mois = ? AND annee = ?");
    $stmt->execute([$id_pret, $mois, $annee]);

    Flight::json(['message' => "Paiement enregistré avec succès."]);
}


}
