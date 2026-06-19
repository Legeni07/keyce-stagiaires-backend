<?php
// api/etudiants/show.php — GET /api/etudiants/{id}
requireAuth(['admin','responsable','tuteur']);
$pdo = getPDO();
$id  = (int)($GLOBALS['route_params']['id'] ?? 0);
$etudiant = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM stages s WHERE s.etudiant_id = e.id) AS nb_stages FROM etudiants e WHERE e.id = $id")->fetch();
if (!$etudiant) jsonError('Étudiant introuvable', 404);
// Historique des stages
$stages = $pdo->query("SELECT s.*, ent.nom AS entreprise_nom FROM stages s JOIN entreprises ent ON ent.id = s.entreprise_id WHERE s.etudiant_id = $id ORDER BY s.date_debut DESC")->fetchAll();
jsonResponse(['data' => $etudiant, 'stages' => $stages]);
