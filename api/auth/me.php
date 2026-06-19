<?php
// api/auth/me.php — GET /api/auth/me

$auth = requireAuth();
$pdo  = getPDO();
$stmt = $pdo->prepare("SELECT id, nom, prenom, email, role, ref_id FROM utilisateurs WHERE id = ?");
$stmt->execute([$auth['sub']]);
$user = $stmt->fetch();
if (!$user) jsonError('Utilisateur introuvable', 404);
jsonResponse(['user' => $user]);
