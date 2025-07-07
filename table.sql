CREATE DATABASE tp_flight CHARACTER SET utf8mb4;
USE tp_flight;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE type_pret(
   Id_type_pret INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   taux_interet_annuel DECIMAL(15,2) NOT NULL,
   duree_remboursement_en_mois BIGINT,
   montant_min BIGINT,
   montant_max BIGINT,
   frais INT NOT NULL,
   remboursement_fixe BOOLEAN NOT NULL,
   PRIMARY KEY(Id_type_pret)
);

CREATE TABLE usages(
   Id_usage INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   PRIMARY KEY(Id_usage)
);

CREATE TABLE type_remboursement_(
   Id_type_remboursement_ INT AUTO_INCREMENT,
   libelle VARCHAR(50),
   mois INT,
   PRIMARY KEY(Id_type_remboursement_)
);

CREATE TABLE status(
   Id_status INT AUTO_INCREMENT,
   status VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_status)
);

CREATE TABLE utilisateur(
   Id_utilisateur INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50),
   email VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   PRIMARY KEY(Id_utilisateur) 
);

CREATE TABLE mouvement_fond(
   Id_mouvement_fond INT AUTO_INCREMENT,
   montant_ INT NOT NULL,
   date_ DATE NOT NULL,
   description VARCHAR(340) NOT NULL,
   type_mouvement BOOLEAN,
   PRIMARY KEY(Id_mouvement_fond)
);

CREATE TABLE client(
   Id_client INT AUTO_INCREMENT,
   email VARCHAR(50) NOT NULL,
   salaire_mensuel INT NOT NULL,
   PRIMARY KEY(Id_client)
);

CREATE TABLE pret(
   Id_pret INT AUTO_INCREMENT,
   date_debut DATE NOT NULL,
   date_fin DATE NOT NULL,
   montant_total INT,
   Id_type_remboursement_ INT NOT NULL,
   Id_usage INT NOT NULL,
   Id_type_pret INT NOT NULL,
   Id_client INT NOT NULL,
   PRIMARY KEY(Id_pret),
   FOREIGN KEY(Id_client) REFERENCES client(Id_client),
   FOREIGN KEY(Id_type_remboursement_) REFERENCES type_remboursement_(Id_type_remboursement_),
   FOREIGN KEY(Id_usage) REFERENCES usages(Id_usage),
   FOREIGN KEY(Id_type_pret) REFERENCES type_pret(Id_type_pret)
);

CREATE TABLE remboursement(
   Id_remboursement INT AUTO_INCREMENT,
   Id_client INT NOT NULL,
   date_remboursement_ DATE,
   montant DECIMAL(15,2),
   Id_pret INT NOT NULL,
   PRIMARY KEY(Id_remboursement),
   FOREIGN KEY(Id_client) REFERENCES client(Id_client),
   FOREIGN KEY(Id_pret) REFERENCES pret(Id_pret)
);

CREATE TABLE historique_pret(
   Id_historique_pret INT AUTO_INCREMENT,
   dateI_pret_hist VARCHAR(50) NOT NULL,
   Id_status INT NOT NULL,
   Id_pret INT NOT NULL,
   PRIMARY KEY(Id_historique_pret),
   FOREIGN KEY(Id_status) REFERENCES status(Id_status),
   FOREIGN KEY(Id_pret) REFERENCES pret(Id_pret)
);

CREATE TABLE historique_remb(
   Id_historique_remb INT AUTO_INCREMENT,
   date_remb DATE,
   Id_remboursement INT,
   Id_status INT NOT NULL,
   PRIMARY KEY(Id_historique_remb),
   FOREIGN KEY(Id_remboursement) REFERENCES remboursement(Id_remboursement),
   FOREIGN KEY(Id_status) REFERENCES status(Id_status)
);
CREATE TABLE montant_a_payer_par_mois(
    Id_montant_a_payer INT AUTO_INCREMENT,
    Id_pret INT NOT NULL,
    mois  int NOT NULL,
    annee int NOT NULL,
    montant DECIMAL(15,2) NOT NULL,
    Id_status INT NOT NULL,

    PRIMARY KEY(Id_montant_a_payer),
    FOREIGN KEY(Id_status) REFERENCES status(Id_status),
    FOREIGN KEY(Id_pret) REFERENCES pret(Id_pret)
);

CREATE TABLE taux_interet_par_mois(
    id_taux_interet INT AUTO_INCREMENT,
    montant DECIMAL(15,2) NOT NULL,
    mois INT NOT NULL,
    annee INT NOT NULL,
    Id_remboursement INT NOT NULL,
    PRIMARY KEY(id_taux_interet),
    FOREIGN KEY(Id_remboursement) REFERENCES remboursement(Id_remboursement)
);


SET FOREIGN_KEY_CHECKS = 1;
CREATE VIEW vue_details_pret AS
SELECT 
    p.Id_pret,
    p.date_debut,
    p.date_fin,
    p.montant_total,
    
    -- Client
    c.Id_client,
    c.email AS client_email,
    c.salaire_mensuel,

    -- Type de prêt
    tp.nom AS type_pret_nom,
    tp.taux_interet_annuel,
    tp.duree_remboursement_en_mois,
    tp.montant_min,
    tp.montant_max,
    tp.frais,
    tp.remboursement_fixe,

    -- Usage
    u.libelle AS usage_libelle,

    -- Type de remboursement
    tr.libelle AS type_remboursement_libelle,
    tr.mois AS frequence_remboursement

FROM pret p
JOIN client c ON p.Id_client = c.Id_client
JOIN type_pret tp ON p.Id_type_pret = tp.Id_type_pret
JOIN usages u ON p.Id_usage = u.Id_usage
JOIN type_remboursement_ tr ON p.Id_type_remboursement_ = tr.Id_type_remboursement_;


INSERT INTO type_pret (nom, taux_interet_annuel, duree_remboursement_en_mois, montant_min, montant_max, frais, remboursement_fixe)
VALUES 
('Crédit Immobilier', 5.00, 240, 10000000, 100000000, 500000, TRUE),
('Crédit Consommation', 7.50, 60, 500000, 10000000, 100000, FALSE);

INSERT INTO usages (libelle)
VALUES 
('Achat Maison'),
('Achat Voiture'),
('Voyage');

INSERT INTO type_remboursement_ (libelle, mois)
VALUES 
('Mensuel', 1),
('Trimestriel', 3),
('Annuel', 12);

INSERT INTO status (status)
VALUES 
('En attente'),
('Validé'),
('Rejeté'),
('Payé');

INSERT INTO utilisateur (nom, prenom, email, mdp)
VALUES 
('Rabe', 'Jean', 'jean.rabe@example.com', 'password123'),
('Ando', 'Fanja', 'fanja.ando@example.com', 'adminpass');

INSERT INTO mouvement_fond (montant_, date_, description, type_mouvement)
VALUES 
(10000000, '2025-01-01', 'Dépôt initial', 1),
(-2500000, '2025-02-10', 'Prêt accordé client', 0);


INSERT INTO client (email, salaire_mensuel)
VALUES 
('client1@example.com', 1500000),
('client2@example.com', 800000);

INSERT INTO pret (date_debut, date_fin, montant_total, Id_type_remboursement_, Id_usage, Id_type_pret, Id_client)
VALUES 
('2025-01-01', '20300101', 5000000, 1, 2, 2, 1),
('2025-04-01', '20290401', 20000000, 2, 1, 1, 2);

INSERT INTO montant_a_payer_par_mois (Id_pret, mois, annee, montant, Id_status)
VALUES 
(2, 4, 2025, 131926.13, 1),
(2, 5, 2025, 131926.13, 1),
(2, 6, 2025, 131926.13, 1),
(2, 7, 2025, 131926.13, 1),
(2, 8, 2025, 131926.13, 1),
(2, 9, 2025, 131926.13, 1),
(2, 10, 2025, 131926.13, 1),
(2, 11, 2025, 131926.13, 1),
(2, 12, 2025, 131926.13, 1),
(2, 1, 2026, 131926.13, 1),
(2, 2, 2026, 131926.13, 1),
(2, 3, 2026, 131926.13, 1);



INSERT INTO remboursement (Id_client, date_remboursement_, montant, Id_pret)
VALUES 
(1, '2025-06-01', 100000, 1),
(2, '2025-06-01', 400000, 2);


INSERT INTO historique_pret (dateI_pret_hist, Id_status, Id_pret)
VALUES 
('2025-01-02', 1, 1),
('2025-01-03', 2, 1),
('2025-04-02', 1, 2);

INSERT INTO historique_remb (date_remb, Id_remboursement, Id_status)
VALUES 
('2025-06-02', 1, 4),
('2025-06-03', 2, 4);


