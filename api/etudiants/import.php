<?php
// api/etudiants/import.php — POST /api/etudiants/import
requireAuth(['admin','responsable']);
$body = getBody(); // tableau de lignes CSV déjà parsé côté client
$rows = $body['rows'] ?? [];
if (!$rows) jsonError('Aucune donnée à importer', 422);

$pdo = getPDO();
$inserted = 0; $errors = [];
$stmt = $pdo->prepare("
    INSERT IGNORE INTO etudiants (matricule, nom, prenom, email, telephone, filiere, niveau, annee_scolaire)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");
foreach ($rows as $i => $row) {
    try {
        $stmt->execute([
            $row['matricule'] ?? '', $row['nom'] ?? '', $row['prenom'] ?? '', $row['email'] ?? '',
            $row['telephone'] ?? null, $row['filiere'] ?? null,
            $row['niveau'] ?? null, $row['annee_scolaire'] ?? null,
        ]);
        if ($stmt->rowCount()) $inserted++;
    } catch (Exception $e) {
        $errors[] = "Ligne $i : " . $e->getMessage();
    }
}
jsonResponse(['message' => "$inserted étudiant(s) importé(s)", 'inserted' => $inserted, 'errors' => $errors]);
