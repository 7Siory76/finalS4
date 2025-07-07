<?php
require_once __DIR__ . '/../db.php';

class PretModel
{

 public static function getTaux($moisDebut, $anneeDebut, $moisFin, $anneeFin)
{
    $db = getDB();
    
   
    $dateDebut = $anneeDebut * 100 + $moisDebut;
    $dateFin = $anneeFin * 100 + $moisFin;
    
    $stmt = $db->prepare("SELECT 
            annee, 
            mois, 
            SUM(montant) AS total_par_mois
        FROM taux_interet_par_mois
        WHERE (annee * 100 + mois) BETWEEN :dateDebut AND :dateFin
        GROUP BY annee, mois
        ORDER BY annee, mois");
    

    $stmt->bindParam(':dateDebut', $dateDebut, PDO::PARAM_INT);
    $stmt->bindParam(':dateFin', $dateFin, PDO::PARAM_INT);
    
 
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
