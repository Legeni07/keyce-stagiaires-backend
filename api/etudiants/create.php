<?php
// api/etudiants/create.php — POST /api/etudiants
requireAuth(['admin','responsable']);
$body = getBody();
$required = ['matricule','nom','prenom','email'];
$errors = [];
foreach ($required as $f) if (empty($body[$f])) $errors[] = "$f requis";
if ($errors) jsonError('Données invalides', 422, $errors);

$pdo = getPDO();
$stmt = $pdo->prepare("
    INSERT INTO etudiants (matricule, nom, prenom, email, telephone, filiere, niveau, annee_scolaire, photo_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
try {
    $stmt->execute([
        $body['matricule'], $body['nom'], $body['prenom'], $body['email'],
        $body['telephone'] ?? null, $body['filiere'] ?? null,
        $body['niveau'] ?? null, $body['annee_scolaire'] ?? null, $body['photo_url'] ?? null,
    ]);
    $id = $pdo->lastInsertId();
    $etudiant = $pdo->query("SELECT * FROM etudiants WHERE id = $id")->fetch();
    jsonResponse(['message' => 'Étudiant créé', 'data' => $etudiant], 201);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') jsonError('Matricule ou email déjà existant', 409);
    throw $e;
}
