<?php
// api/etudiants/update.php — PUT /api/etudiants/{id}
requireAuth(['admin','responsable']);
$pdo  = getPDO();
$id   = (int)($GLOBALS['route_params']['id'] ?? 0);
$body = getBody();
$check = $pdo->query("SELECT id FROM etudiants WHERE id = $id")->fetch();
if (!$check) jsonError('Étudiant introuvable', 404);

$fields = ['matricule','nom','prenom','email','telephone','filiere','niveau','annee_scolaire','photo_url'];
$set = []; $params = [];
foreach ($fields as $f) {
    if (array_key_exists($f, $body)) { $set[] = "$f = ?"; $params[] = $body[$f]; }
}
if (!$set) jsonError('Aucun champ à mettre à jour', 422);
$params[] = $id;
$pdo->prepare("UPDATE etudiants SET " . implode(', ', $set) . " WHERE id = ?")->execute($params);
$etudiant = $pdo->query("SELECT * FROM etudiants WHERE id = $id")->fetch();
jsonResponse(['message' => 'Étudiant mis à jour', 'data' => $etudiant]);
