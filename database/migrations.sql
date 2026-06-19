-- ============================================================
-- KEYCE ACADEMY — Base de données Gestion des Stagiaires
-- ============================================================

CREATE DATABASE IF NOT EXISTS keyce_stagiaires CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE keyce_stagiaires;

-- ─── UTILISATEURS ────────────────────────────────────────────
CREATE TABLE utilisateurs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role        ENUM('admin','responsable','tuteur','stagiaire') NOT NULL DEFAULT 'stagiaire',
    ref_id      INT DEFAULT NULL COMMENT 'tuteur_id ou etudiant_id selon le role',
    actif       TINYINT(1) DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ─── ÉTUDIANTS ───────────────────────────────────────────────
CREATE TABLE etudiants (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    matricule     VARCHAR(30) NOT NULL UNIQUE,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    telephone     VARCHAR(20),
    filiere       VARCHAR(80),
    niveau        VARCHAR(20),
    annee_scolaire VARCHAR(10),
    photo_url     VARCHAR(255),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ─── ENTREPRISES ─────────────────────────────────────────────
CREATE TABLE entreprises (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(150) NOT NULL,
    secteur       VARCHAR(100),
    ville         VARCHAR(100),
    pays          VARCHAR(100) DEFAULT 'Cameroun',
    email_contact VARCHAR(150),
    telephone     VARCHAR(20),
    site_web      VARCHAR(200),
    partenaire    TINYINT(1) DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ─── TUTEURS ─────────────────────────────────────────────────
CREATE TABLE tuteurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    telephone     VARCHAR(20),
    type_tuteur   ENUM('academique','entreprise') NOT NULL,
    specialite    VARCHAR(100),
    entreprise_id INT DEFAULT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE SET NULL
);

-- ─── STAGES ──────────────────────────────────────────────────
CREATE TABLE stages (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id       INT NOT NULL,
    entreprise_id     INT NOT NULL,
    tuteur_acad_id    INT DEFAULT NULL,
    tuteur_entr_id    INT DEFAULT NULL,
    intitule_poste    VARCHAR(200),
    description       TEXT,
    date_debut        DATE,
    date_fin          DATE,
    type_stage        ENUM('stage_fin_etudes','stage_observation','alternance','projet') DEFAULT 'stage_fin_etudes',
    statut            ENUM('en_attente','en_cours','termine','abandonne') DEFAULT 'en_attente',
    note              DECIMAL(4,2) DEFAULT NULL,
    convention_signee TINYINT(1) DEFAULT 0,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id)    REFERENCES etudiants(id)   ON DELETE CASCADE,
    FOREIGN KEY (entreprise_id)  REFERENCES entreprises(id) ON DELETE RESTRICT,
    FOREIGN KEY (tuteur_acad_id) REFERENCES tuteurs(id)     ON DELETE SET NULL,
    FOREIGN KEY (tuteur_entr_id) REFERENCES tuteurs(id)     ON DELETE SET NULL
);

-- ─── VISITES ─────────────────────────────────────────────────
CREATE TABLE visites (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    stage_id        INT NOT NULL,
    tuteur_id       INT NOT NULL,
    date_visite     DATE NOT NULL,
    type_visite     ENUM('sur_site','video','telephone') DEFAULT 'sur_site',
    compte_rendu    TEXT,
    observations    TEXT,
    prochaine_visite DATE DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id)  REFERENCES stages(id)  ON DELETE CASCADE,
    FOREIGN KEY (tuteur_id) REFERENCES tuteurs(id) ON DELETE RESTRICT
);

-- ─── ÉVALUATIONS ─────────────────────────────────────────────
CREATE TABLE evaluations (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    stage_id            INT NOT NULL,
    evaluateur_type     ENUM('academique','entreprise') NOT NULL,
    comp_techniques     DECIMAL(4,2) DEFAULT 0 COMMENT '/20',
    comp_relation       DECIMAL(4,2) DEFAULT 0 COMMENT '/20',
    autonomie           DECIMAL(4,2) DEFAULT 0 COMMENT '/20',
    respect_delais      DECIMAL(4,2) DEFAULT 0 COMMENT '/20',
    commentaire         TEXT,
    recommande_embauche TINYINT(1) DEFAULT 0,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE
);

-- ─── DOCUMENTS ───────────────────────────────────────────────
CREATE TABLE documents (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    stage_id   INT NOT NULL,
    type       ENUM('convention','rapport','attestation','evaluation_form','autre') DEFAULT 'autre',
    nom_fichier VARCHAR(255),
    chemin     VARCHAR(500),
    taille     INT DEFAULT NULL COMMENT 'octets',
    mime_type  VARCHAR(100),
    statut     ENUM('en_attente','valide','rejete') DEFAULT 'en_attente',
    uploaded_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stage_id) REFERENCES stages(id) ON DELETE CASCADE
);

-- ============================================================
-- DONNÉES DE DÉMO
-- ============================================================

-- Utilisateurs (mot de passe : Keyce2025! → bcrypt)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, ref_id) VALUES
('Admin',       'Keyce',    'admin@keyce.cm',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin',       NULL),
('Responsable', 'Keyce',    'responsable@keyce.cm',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'responsable', NULL),
('BOGNI',       'Danchi',   'tuteur1@keyce.cm',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur',      1),
('KAMDEM',      'Paul',     'tuteur2@keyce.cm',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur',      2),
('NKEMBI',      'Alice',    'stagiaire1@keyce.cm',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'stagiaire',   1),
('MBARGA',      'Jean',     'stagiaire2@keyce.cm',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'stagiaire',   2);

-- Étudiants
INSERT INTO etudiants (matricule, nom, prenom, email, telephone, filiere, niveau, annee_scolaire) VALUES
('KY2025001', 'NKEMBI',  'Alice',   'alice.nkembi@etudiant.keyce.cm',  '+237699001001', 'IABD', 'B1', '2025-2026'),
('KY2025002', 'MBARGA',  'Jean',    'jean.mbarga@etudiant.keyce.cm',   '+237699001002', 'RSI',  'B1', '2025-2026'),
('KY2025003', 'ESSAMA',  'Céline',  'celine.essama@etudiant.keyce.cm', '+237699001003', 'GL',   'B1', '2025-2026'),
('KY2025004', 'FOUDA',   'Marc',    'marc.fouda@etudiant.keyce.cm',    '+237699001004', 'IABD', 'B2', '2025-2026'),
('KY2025005', 'BELLO',   'Fatima',  'fatima.bello@etudiant.keyce.cm',  '+237699001005', 'RSI',  'B2', '2025-2026');

-- Entreprises
INSERT INTO entreprises (nom, secteur, ville, pays, email_contact, telephone, site_web, partenaire) VALUES
('Orange Cameroun',    'Télécommunications', 'Douala',   'Cameroun', 'rh@orange.cm',       '+237233432200', 'https://orange.cm',       1),
('MTN Cameroon',       'Télécommunications', 'Douala',   'Cameroun', 'recrutement@mtn.cm', '+237233500000', 'https://mtn.cm',          1),
('Afriland First Bank','Finance',            'Yaoundé',  'Cameroun', 'stagiaires@afriland.cm','+237222221600','https://afriland.net',  1),
('Camtel',             'Télécommunications', 'Yaoundé',  'Cameroun', 'drh@camtel.cm',      '+237222234567', 'https://camtel.cm',       0),
('SCDP',               'Pétrochimie',        'Douala',   'Cameroun', 'rh@scdp.cm',         '+237233401234', 'https://scdp.cm',         1);

-- Tuteurs
INSERT INTO tuteurs (nom, prenom, email, telephone, type_tuteur, specialite, entreprise_id) VALUES
('BOGNI',    'Danchi',  'danchi.bogni@keyce.cm',      '+237699200001', 'academique',  'Développement Web',      NULL),
('KAMDEM',   'Paul',    'paul.kamdem@keyce.cm',        '+237699200002', 'academique',  'Intelligence Artificielle', NULL),
('NDONGO',   'Serge',   'serge.ndongo@orange.cm',      '+237699300001', 'entreprise',  'Réseaux & Systèmes',     1),
('MBOMO',    'Claire',  'claire.mbomo@mtn.cm',         '+237699300002', 'entreprise',  'Data Science',           2),
('TCHINDA',  'Roger',   'roger.tchinda@afriland.cm',   '+237699300003', 'entreprise',  'Finance digitale',       3);

-- Stages
INSERT INTO stages (etudiant_id, entreprise_id, tuteur_acad_id, tuteur_entr_id, intitule_poste, description, date_debut, date_fin, type_stage, statut, convention_signee) VALUES
(1, 1, 1, 3, 'Développeur Frontend React', 'Développement d'une interface web pour la gestion des abonnés.', '2025-06-01', '2025-08-31', 'stage_fin_etudes', 'en_cours',  1),
(2, 2, 2, 4, 'Analyste Data',              'Analyse des données réseau et reporting BI.',                     '2025-06-15', '2025-09-15', 'stage_fin_etudes', 'en_cours',  1),
(3, 3, 1, 5, 'Développeur Backend PHP',    'Création d'une API REST pour la banque mobile.',                  '2025-07-01', '2025-09-30', 'stage_fin_etudes', 'en_attente',0),
(4, 4, 2, NULL,'Technicien Réseaux',       'Maintenance et supervision des équipements réseau.',              '2025-05-01', '2025-07-31', 'stage_observation','termine',   1),
(5, 5, 1, 3, 'Développeur FullStack',      'Application de suivi des stocks pétroliers.',                    '2025-06-01', '2025-08-31', 'stage_fin_etudes', 'en_cours',  1);

-- Visites
INSERT INTO visites (stage_id, tuteur_id, date_visite, type_visite, compte_rendu, observations, prochaine_visite) VALUES
(1, 1, '2025-06-20', 'sur_site',  'Visite initiale. L'étudiant est bien intégré dans l'équipe.', 'Motivation excellente. Maîtrise des outils en cours d'acquisition.', '2025-07-20'),
(1, 1, '2025-07-22', 'sur_site',  'Deuxième visite. Bons progrès sur React.', 'A livré 2 composants fonctionnels. À améliorer : tests unitaires.', '2025-08-20'),
(2, 2, '2025-07-01', 'video',     'Visioconférence avec l'équipe Data.', 'Familiarisé avec Power BI. Rapport intermédiaire attendu.', '2025-08-01'),
(5, 1, '2025-06-25', 'telephone', 'Point téléphonique rapide.', 'Démarrage difficile côté backend. Un suivi rapproché est conseillé.', '2025-07-15');

-- Évaluations
INSERT INTO evaluations (stage_id, evaluateur_type, comp_techniques, comp_relation, autonomie, respect_delais, commentaire, recommande_embauche) VALUES
(4, 'academique',  15, 17, 14, 16, 'Bon stage malgré quelques difficultés techniques au départ.',    1),
(4, 'entreprise',  14, 18, 16, 15, 'Stagiaire sérieux, bon esprit d'équipe, à recruter si possible.', 1),
(1, 'academique',  16, 15, 14, 17, 'Bon niveau technique en React. Progrès notables.',               0);

-- Documents
INSERT INTO documents (stage_id, type, nom_fichier, chemin, taille, mime_type, statut) VALUES
(1, 'convention',  'convention_stage_nkembi.pdf', 'uploads/convention_stage_nkembi.pdf', 245000, 'application/pdf', 'valide'),
(2, 'convention',  'convention_mbarga.pdf',        'uploads/convention_mbarga.pdf',        198000, 'application/pdf', 'valide'),
(4, 'rapport',     'rapport_final_fouda.pdf',      'uploads/rapport_final_fouda.pdf',      512000, 'application/pdf', 'valide'),
(4, 'attestation', 'attestation_fouda.pdf',        'uploads/attestation_fouda.pdf',        98000,  'application/pdf', 'valide'),
(5, 'convention',  'convention_bello.pdf',         'uploads/convention_bello.pdf',         210000, 'application/pdf', 'en_attente');
