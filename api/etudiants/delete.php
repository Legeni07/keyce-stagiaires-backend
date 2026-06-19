<?php
// api/etudiants/delete.php — DELETE /api/etudiants/{id}
requireAuth(['admin']);
$pdo = getPDO();
$id  = (int)($GLOBALS['route_params']['id'] ?? 0);
$check = $pdo->query("SELECT id FROM etudiants WHERE id = $id")->fetch();
if (!$check) jsonError('Étudiant introuvable', 404);
$pdo->exec("DELETE FROM etudiants WHERE id = $id");
jsonResponse(['message' => 'Étudiant supprimé']);
