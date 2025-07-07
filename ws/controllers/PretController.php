<?php
// Fichier : PretController.php
require_once __DIR__ . '/../models/PretModel.php';

class PretController
{
    public static function getTaux()
    {
        $moisDebut = Flight::request()->query['moisDebut'];
        $anneeDebut = Flight::request()->query['anneeDebut'];
        $moisFin = Flight::request()->query['moisFin'];
        $anneeFin = Flight::request()->query['anneeFin'];
        
        try {
            $resultats = PretModel::getTaux($moisDebut, $anneeDebut, $moisFin, $anneeFin);
            if (empty($resultats)) {
                Flight::json(['message' => 'Aucun taux trouvé pour la période spécifiée']);
            } else {
                Flight::json($resultats);
            }
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la récupération des taux: ' . $e->getMessage()], 500);
        }
    }
    
    public static function generatePDF()
    {
        require_once 'fpdf186/fpdf.php';

        // Récupération des données POST
        $nom = Flight::request()->data['nom'] ?? '';
        $typePret = Flight::request()->data['typePret'] ?? '';
        $duree = Flight::request()->data['duree'] ?? '';
        $montantEmprunter = Flight::request()->data['montantEmprunter'] ?? '';
        $interetsTotaux = Flight::request()->data['interetsTotaux'] ?? '';
        $montantTotalEstime = Flight::request()->data['montantTotalEstime'] ?? '';
        $mensualiteEstimee = Flight::request()->data['mensualiteEstimee'] ?? '';
        
        // Validation des données
        if (empty($nom) || empty($typePret) || empty($duree) || empty($montantEmprunter) || 
            empty($interetsTotaux) || empty($montantTotalEstime) || empty($mensualiteEstimee)) {
            Flight::json(['error' => 'Tous les champs sont requis pour générer le PDF'], 400);
            return;
        }
        
        try {
            
            $pdf = new FPDF();
            $pdf->AddPage();
            
            // En-tête du document
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 15, 'SIMULATION DE PRET', 0, 1, 'C');
            $pdf->Ln(5);
            
            // Informations générales
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Informations du demandeur', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 8, 'Nom: ' . $nom, 0, 1, 'L');
            $pdf->Cell(0, 8, 'Date de simulation: ' . date('d/m/Y'), 0, 1, 'L');
            $pdf->Ln(5);
            
            // Tableau des détails du prêt
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Details du pret', 0, 1, 'L');
            $pdf->Ln(2);
            
            // En-têtes du tableau
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            
            $headers = [
                'Type de pret' => 35,
                'Duree (mois)' => 25,
                'Montant emprunte' => 35,
                'Interets totaux' => 35,
                'Montant total' => 35,
                'Mensualite' => 25
            ];
            
            foreach ($headers as $header => $width) {
                $pdf->Cell($width, 8, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            
            // Données du tableau
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetFillColor(255, 255, 255);
            
            $data = [
                $typePret,
                $duree,
                number_format($montantEmprunter, 0, ',', ' ') . ' €',
                number_format($interetsTotaux, 2, ',', ' ') . ' €',
                number_format($montantTotalEstime, 2, ',', ' ') . ' €',
                number_format($mensualiteEstimee, 2, ',', ' ') . ' €'
            ];
            
            $i = 0;
            foreach ($headers as $header => $width) {
                $pdf->Cell($width, 8, $data[$i], 1, 0, 'C');
                $i++;
            }
            $pdf->Ln();
            
            // Calculs et informations supplémentaires
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Resume financier', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 11);
            
            // Calcul du taux d'intérêt approximatif
            $tauxApprox = ($interetsTotaux / $montantEmprunter) * 100;
            $coutTotal = $montantTotalEstime - $montantEmprunter;
            
            $pdf->Cell(0, 8, 'Montant emprunte: ' . number_format($montantEmprunter, 0, ',', ' ') . ' €', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Cout total du credit: ' . number_format($coutTotal, 2, ',', ' ') . ' €', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Taux d\'interet approximatif: ' . number_format($tauxApprox, 2, ',', ' ') . ' %', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Duree de remboursement: ' . $duree . ' mois (' . round($duree/12, 1) . ' ans)', 0, 1, 'L');
            $pdf->Cell(0, 8, 'Mensualite: ' . number_format($mensualiteEstimee, 2, ',', ' ') . ' €', 0, 1, 'L');
            
           
            
            // Génération du nom de fichier
            $fileName = 'simulation_pret_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nom) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Définir les en-têtes pour le téléchargement PDF
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            // Envoi du PDF au navigateur
            $pdf->Output('D', $fileName);
            exit; // Important : arrêter l'exécution après l'envoi du PDF
            
        } catch (Exception $e) {
            Flight::json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
        }
    }
}
?>