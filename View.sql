CREATE VIEW vue_pret_detail AS
SELECT 
    p.Id_pret,
    p.date_debut,
    p.date_fin,
    p.montant_total,

    c.Id_client,
    c.email AS client_email,
    c.salaire_mensuel,

    tp.Id_type_pret,
    tp.nom AS type_pret_nom,
    tp.taux_interet_annuel,
    tp.duree_remboursement_en_mois,
    tp.montant_min,
    tp.montant_max,
    tp.frais,
    tp.remboursement_fixe,

    u.Id_usage,
    u.libelle AS usage_libelle,

    tr.Id_type_remboursement_,
    tr.libelle AS type_remboursement_libelle,
    tr.mois AS remboursement_mois

FROM pret p
JOIN client c ON p.Id_client = c.Id_client
JOIN type_pret tp ON p.Id_type_pret = tp.Id_type_pret
JOIN usages u ON p.Id_usage = u.Id_usage
JOIN type_remboursement_ tr ON p.Id_type_remboursement_ = tr.Id_type_remboursement_;


CREATE VIEW vue_usage_type_pret_client AS
SELECT 
    u.Id_usage,
    u.libelle AS usage_libelle,
    tp.Id_type_pret,
    tp.nom AS type_pret_nom,
    tp.taux_interet_annuel,
    tp.duree_remboursement_en_mois,
    tp.montant_min,
    tp.montant_max,
    tp.frais,
    tp.remboursement_fixe,
    c.Id_client,
    c.email AS client_email,
    c.salaire_mensuel,
    tr.libelle AS type_remboursement_libelle,
    tr.mois AS remboursement_mois,
    ta.Id_type_assurance,
    ta.taux_assurance,
    ta.nom AS type_assurance_nom,
    (SELECT mf.montant_ FROM mouvement_fond mf ORDER BY mf.Id_mouvement_fond DESC LIMIT 1) AS dernier_montant_fond
FROM 
    usages u
JOIN 
    type_pret tp ON 1=1  
JOIN 
    client c ON 1=1      
JOIN 
    type_remboursement_ tr ON 1=1  
JOIN
    type_assurance ta ON 1=1;       