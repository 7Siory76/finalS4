<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Simulation et Ajout de Prêt</title>
    <style>
        .container {
            display: flex;
            gap: 20px;
        }
        .form-container, .simulation-container {
            flex: 1;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: inline-block;
            width: 150px;
        }
        input, select {
            padding: 5px;
            width: 250px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-right: 10px;
        }
        .simulation-result {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Simulation et Ajout de prêt</h2>
    
    <div class="container">
        <div class="form-container">
            <h3>Formulaire de prêt</h3>
            <form>
                <div class="form-group">
                    <label for="date_debut">Date de début :</label>
                    <input type="date" id="date_debut" name="date_debut"  required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="date_fin">Date de fin :</label>
                    <input type="date" id="date_fin" name="date_fin"  required onchange="calculerDuree()">
                </div>

                <div class="form-group">
                    <label for="montant_total">Montant total :</label>
                    <input type="number" id="montant_total" name="montant_total" value="20000" required>
                </div>

                <div class="form-group">
                    <label for="id_client">Client :</label>
                    <select id="id_client" name="id_client" required>
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_type_pret">Type de prêt :</label>
                    <select id="id_type_pret" name="id_type_pret" required onchange="afficherDetailsPret()">
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_usage">Usage :</label>
                    <select id="id_usage" name="id_usage" required>
                        <option value="">Chargement...</option>
                    </select>
                </div>

                <!-- <div class="form-group">
                    <label for="id_type_remboursement">Type remboursement :</label>
                    <select id="id_type_remboursement" name="id_type_remboursement" required>
                        <option value="">Chargement...</option>
                    </select>
                </div> -->
                <input type="hidden" id="montant_total_rembourser" name="montant_total_rembourser">
                <button type="button" onclick="chargerDonnees()">Charger les options</button>
                <button type="button" onclick="simulerPret()">Simuler le prêt</button>
                <button type="button" onclick="ajouterUnpret()">Enregistrer le prêt</button>
                <button type="button" class="pdf-button" onclick="generatePDF()">Télécharger en PDF</button>
            </form>

        </div>

        <div class="simulation-container">
            <h3>Résultats de simulation</h3>
            <div id="simulation-details" class="simulation-result">
                <p>Veuillez remplir le formulaire et cliquer sur "Simuler le prêt"</p>
            </div>
            
            <!-- <h3>Derniers prêts enregistrés</h3>
            <div id="historique-prets">
                <p>Chargement de l'historique...</p>
            </div> -->
        </div>
    </div>

    <script>
    const apiBase = "http://localhost/finalS4/ws";
    let allPretData = []; // Pour stocker toutes les données des prêts

    function ajax(method, url, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, apiBase + url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            }
        };
        xhr.send(data);
    }

    function chargerDonnees() {
        // Charger toutes les données depuis la vue
        ajax("GET", "/pret", null, (data) => {
            if (!data || data.error) {
                console.error("Erreur de chargement:", data ? data.error : "Pas de données");
                return;
            }
            
            allPretData = data; // Stocker les données pour les utiliser ailleurs

            // Remplir les clients (en éliminant les doublons)
            const clients = {};
            const selectClient = document.getElementById("id_client");
            selectClient.innerHTML = '<option value="">-- Sélectionnez un client --</option>';
            
            data.forEach(item => {
                if (!clients[item.Id_client]) {
                    clients[item.Id_client] = item.client_email;
                    const option = document.createElement("option");
                    option.value = item.Id_client;
                    option.textContent = item.client_email + " (Salaire: " + item.salaire_mensuel + ")";
                    selectClient.appendChild(option);
                }
            });

            // Remplir les types de prêt (en éliminant les doublons)
            const typesPret = {};
            const selectTypePret = document.getElementById("id_type_pret");
            selectTypePret.innerHTML = '<option value="">-- Sélectionnez un type --</option>';
            
            data.forEach(item => {
                if (!typesPret[item.Id_type_pret]) {
                    typesPret[item.Id_type_pret] = item.type_pret_nom;
                    const option = document.createElement("option");
                    option.value = item.Id_type_pret;
                    option.textContent = item.type_pret_nom + 
                                      " (Taux: " + item.taux_interet_annuel + "%)";
                    selectTypePret.appendChild(option);
                }
            });

            // Remplir les usages (en éliminant les doublons)
            const usages = {};
            const selectUsage = document.getElementById("id_usage");
            selectUsage.innerHTML = '<option value="">-- Sélectionnez un usage --</option>';
            
            data.forEach(item => {
                if (!usages[item.Id_usage]) {
                    usages[item.Id_usage] = item.usage_libelle;
                    const option = document.createElement("option");
                    option.value = item.Id_usage;
                    option.textContent = item.usage_libelle;
                    selectUsage.appendChild(option);
                }
            });

            //Remplir les types de remboursement (en éliminant les doublons)
            // const typesRemb = {};
            // const selectTypeRemb = document.getElementById("id_type_remboursement");
            // selectTypeRemb.innerHTML = '<option value="">-- Sélectionnez un type --</option>';
            
            // data.forEach(item => {
            //     if (!typesRemb[item.Id_type_remboursement_]) {
            //         typesRemb[item.Id_type_remboursement_] = item.type_remboursement_libelle;
            //         const option = document.createElement("option");
            //         option.value = item.Id_type_remboursement_;
            //         option.textContent = item.type_remboursement_libelle + 
            //                           " (" + item.remboursement_mois + " mois)";
            //         selectTypeRemb.appendChild(option);
            //     }
            // });

            // Charger l'historique des prêts
          //  chargerHistorique();
        });
    }
    function generatePDF() {
        //const nom = document.getElementById("nom").value;
        const typePret = document.getElementById("id_type_pret").value;
        // const duree = document.getElementById("duree").value;
        // const montantEmprunter = document.getElementById("montant_emprunter").value;
        // const interetsTotaux = document.getElementById("interets_totaux").value;
        // const montantTotalEstime = document.getElementById("montant_total_estime").value;
        
        // Vérifier que tous les champs PDF sont remplis
        // if (!nom || !typePret || !duree || !montantEmprunter || !interetsTotaux || !montantTotalEstime || !mensualiteEstimee) {
        //   showMessage("Veuillez remplir tous les champs pour générer le PDF", "error");
        //   return;
        // }
        
        // Désactiver le bouton pendant la requête
        const pdfBtn = document.querySelector('.pdf-button');
        pdfBtn.disabled = true;
        pdfBtn.textContent = "Génération en cours...";
        
        // Créer un formulaire temporaire pour le téléchargement
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = apiBase + '/generatePDF';
        form.style.display = 'none';
        
        // Ajouter les champs du formulaire
        const fields = {
          //nom: nom,
          typePret: typePret,
        //   duree: duree,
        //   montantEmprunter: montantEmprunter,
        //   interetsTotaux: interetsTotaux,
        //   montantTotalEstime: montantTotalEstime,
        //   mensualiteEstimee: mensualiteEstimee
        };
        
        Object.keys(fields).forEach(key => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = key;
          input.value = fields[key];
          form.appendChild(input);
        });
        
        // Ajouter le formulaire au document et le soumettre
        document.body.appendChild(form);
        form.submit();
        
        // Nettoyer et réactiver le bouton
        document.body.removeChild(form);
        setTimeout(() => {
          pdfBtn.disabled = false;
          pdfBtn.textContent = "Télécharger en PDF";
          showMessage("PDF généré avec succès!", "success");
        }, 1000);
      }
    function calculerDuree() {
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        
        if (dateDebut && dateFin) {
            const diffTime = Math.abs(new Date(dateFin) - new Date(dateDebut));
            const diffMonths = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 30));
            return diffMonths;
        }
        return 0;
    }

    function afficherDetailsPret() {
        const montant = parseFloat(document.getElementById("montant_total").value);
        const typePretId = document.getElementById("id_type_pret").value;
        if (!typePretId) return;
        
        const typePret = allPretData.find(item => item.Id_type_pret == typePretId);
        if (typePret) {
            // if (montant < typePret.montant_min) {
                
            // }
            // if (montant > typePret.montant_min) {
                
            // }
            const details = `
                <h4>Détails du type de prêt</h4>
                <p><strong>Nom:</strong> ${typePret.type_pret_nom}</p>
                <p><strong>Taux annuel:</strong> ${typePret.taux_interet_annuel}%</p>
                <p><strong>Durée max:</strong> ${typePret.duree_remboursement_en_mois} mois</p>
                <p><strong>Montant min:</strong> ${typePret.montant_min}</p>
                <p><strong>Montant max:</strong> ${typePret.montant_max}</p>
                <p><strong>Frais:</strong> ${typePret.frais}</p>
            `;
            
            document.getElementById("simulation-details").innerHTML = details;
        }
    }

    function simulerPret() {
        // Récupérer les valeurs du formulaire
        const montant = parseFloat(document.getElementById("montant_total").value);
        const typePretId = document.getElementById("id_type_pret").value;
        const clientId = document.getElementById("id_client").value;
        const dateDebut = document.getElementById("date_debut").value;
        const dateFin = document.getElementById("date_fin").value;
        
        if (!montant || !typePretId || !clientId || !dateDebut || !dateFin) {
            alert("Veuillez remplir tous les champs du formulaire");
            return;
        }
        
        const dureeMois = calculerDuree();
        const typePret = allPretData.find(item => item.Id_type_pret == typePretId);
        const client = allPretData.find(item => item.Id_client == clientId);
        
        if (!typePret || !client) {
            alert("Données non trouvées pour la simulation");
            return;
        }
        
        // Calculs de simulation
        const tauxMensuel = typePret.taux_interet_annuel / 12 / 100;
        const interetTotal = montant * tauxMensuel * dureeMois;
        const montantTotal = montant + interetTotal;
       // const mensualite = montantTotal / dureeMois;
       document.getElementById("montant_total_rembourser").value = montantTotal.toFixed(2);
        // Afficher les résultats
        const simulationHTML = `
            <h4>Résultats de simulation</h4>
            <table>
                <tr>
                    <th>Client</th>
                    <td>${client.client_email} (Salaire: ${client.salaire_mensuel})</td>
                </tr>
                <tr>
                    <th>Type de prêt</th>
                    <td>${typePret.type_pret_nom} (${typePret.taux_interet_annuel}%)</td>
                </tr>
                <tr>
                    <th>Durée</th>
                    <td>${dureeMois} mois (du ${dateDebut} au ${dateFin})</td>
                </tr>
                <tr>
                    <th>Montant emprunté</th>
                    <td>${montant.toFixed(2)}</td>
                </tr>
                <tr>
                    <th>Intérêts totaux</th>
                    <td>${interetTotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <th>Montant total à rembourser</th>
                    <td>${montantTotal.toFixed(2)}</td>
                </tr>
                
            </table>
        `;
        
        document.getElementById("simulation-details").innerHTML = simulationHTML;
    }

    // function chargerHistorique() {
    //     // Ici vous devriez faire un appel API pour récupérer les prêts enregistrés
    //     // Pour l'exemple, je vais utiliser allPretData mais en réalité il faudrait un autre endpoint
    //     let historiqueHTML = '<table><tr><th>Client</th><th>Type</th><th>Montant</th><th>Durée</th><th>Date début</th></tr>';
        
    //     allPretData.slice(0, 5).forEach(pret => {
    //         const duree = Math.ceil((new Date(pret.date_fin) - new Date(pret.date_debut)) / (1000 * 60 * 60 * 24 * 30));
    //         historiqueHTML += `
    //             <tr>
    //                 <td>${pret.client_email}</td>
    //                 <td>${pret.type_pret_nom}</td>
    //                 <td>${pret.montant_total}</td>
    //                 <td>${duree} mois</td>
    //                 <td>${pret.date_debut}</td>
    //             </tr>
    //         `;
    //     });
        
    //     historiqueHTML += '</table>';
    //     document.getElementById("historique-prets").innerHTML = historiqueHTML;
    // }
    function ajouterUnpret() {
    // Validation simple côté client
    const dateDebut = document.getElementById("date_debut").value;
    const dateFin = document.getElementById("date_fin").value;
    const montantTotal = document.getElementById("montant_total").value;
    const idClient = document.getElementById("id_client").value;
    const idTypePret = document.getElementById("id_type_pret").value;
    const idUsage = document.getElementById("id_usage").value;
    const montantTotalRembourser = document.getElementById("montant_total_rembourser").value;

    if (!dateDebut || !dateFin || !montantTotal || !idClient || !idTypePret || !idUsage) {
        alert("Veuillez remplir tous les champs et simuler le prêt avant d'enregistrer !");
        return;
    }

    // Construire la chaîne de données au format URL-encodé (noms cohérents)
    const dataToSend = `date_debut=${encodeURIComponent(dateDebut)}` +
                       `&date_fin=${encodeURIComponent(dateFin)}` +
                       `&montant_total=${encodeURIComponent(montantTotal)}` +
                       `&montant_total_rembourser=${encodeURIComponent(montantTotalRembourser)}` +
                       `&Id_client=${encodeURIComponent(idClient)}` +
                       `&Id_type_pret=${encodeURIComponent(idTypePret)}` + // Cohérence avec majuscule
                       `&Id_usage=${encodeURIComponent(idUsage)}`;

    // Envoyer les données au serveur
    ajax("POST", "/pret", dataToSend, (response) => {
        console.log("Réponse du serveur:", response); // Debug
        if (response && response.message && response.message.includes('succès')) {
            alert("Prêt enregistré avec succès!");
            // Réinitialiser le formulaire
            document.querySelector('form').reset();
            document.getElementById("simulation-details").innerHTML = '<p>Veuillez remplir le formulaire et cliquer sur "Simuler le prêt"</p>';
        } else {
            alert("Erreur lors de l'enregistrement: " + (response.message || "Erreur inconnue"));
        }
    });
}
    // Charger les données au chargement de la page
    window.onload = chargerDonnees;
    </script>
</body>
</html>