<?php
// api/etudiants/list.php — GET /api/etudiants
requireAuth(['admin','responsable','tuteur']);
$pdo = getPDO();
$q = $_GET;

$where = "WHERE 1=1";
$params = [];
if (!empty($q['search'])) {
    $s = "%{$q['search']}%";
    $where .= " AND (nom LIKE ? OR prenom LIKE ? OR matricule LIKE ? OR email LIKE ?)";
    array_push($params, $s, $s, $s, $s);
}
if (!empty($q['filiere']))  { $where .= " AND filiere = ?";       $params[] = $q['filiere']; }
if (!empty($q['niveau']))   { $where .= " AND niveau = ?";        $params[] = $q['niveau']; }
if (!empty($q['annee']))    { $where .= " AND annee_scolaire = ?"; $params[] = $q['annee']; }

$pg = paginate($pdo, 'etudiants', $where, $params, $q);

$stmt = $pdo->prepare("SELECT * FROM etudiants $where ORDER BY nom, prenom LIMIT ? OFFSET ?");
$stmt->execute([...$params, $pg['per_page'], $pg['offset']]);
$data = $stmt->fetchAll();

jsonResponse(['data' => $data, 'pagination' => $pg]);
