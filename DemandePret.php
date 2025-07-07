<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajout de Prêt</title>
</head>
<body>
    <h2>Formulaire d'ajout de prêt</h2>
        <label for="date_debut">Date de début :</label>
        <input type="date" id="date_debut" name="date_debut" required><br><br>

        <label for="date_fin">Date de fin :</label>
        <input type="date" id="date_fin" name="date_fin" required><br><br>

        <label for="montant_total">Montant total :</label>
        <input type="number" id="montant_total" name="montant_total" required><br><br>

        <label for="id_client">Client :</label>
        <select id="id_client" name="id_client" required>
            <option value=""></option>
        </select><br><br>

        <label for="id_type_pret">Type de prêt :</label>
        <select id="id_type_pret" name="id_type_pret" required>
            <option value=""></option>    
        </select><br><br>

        <label for="id_usage">Usage :</label>
        <select id="id_usage" name="id_usage" required>
            <option value=""></option>
        </select><br><br>

        <button onclick="ajouterUnpret()">Enregistrer le pret </button>

    <script>
        
    const apiBase = "http://localhost/finalS4/ws";

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
        
        function ajouterUnpret() {
          ajax("GET", "/pret", null, (data) => {
            const selectC = document.getElementById("id_client");
            const selectR = document.getElementById("id_type_pret");
            const selectU = document.getElementById("id_usage");
            const dataselect=Array();
            dataselect=[selectC,selectR,selectU];
            select.innerHTML = '<option value="">Sélectionnez</option>';
            for (let index = 0; index < dataselect.length; index++) {
               
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item[valueField];
                    option.textContent = item[textField];
                    select.appendChild(option);
                });   
                
            }
        });
        }
    </script>
</body>
</html>
