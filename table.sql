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
   type_mouvement BOOLEAN NOT NULL,
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
   date_debut Date NOT NULL,
   date_fin Date NOT NULL,
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

