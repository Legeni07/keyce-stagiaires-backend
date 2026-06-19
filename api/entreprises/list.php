<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
$pdo = getPDO(); $q = $_GET;
$where = "WHERE 1=1"; $params = [];
if (!empty($q['search']))   { $s="%{$q['search']}%"; $where.=" AND (nom LIKE ? OR secteur LIKE ? OR ville LIKE ?)"; array_push($params,$s,$s,$s); }
if (!empty($q['secteur']))  { $where.=" AND secteur = ?"; $params[]=$q['secteur']; }
if (isset($q['partenaire'])){ $where.=" AND partenaire = ?"; $params[]=(int)$q['partenaire']; }
$pg = paginate($pdo,'entreprises',$where,$params,$q);
$stmt=$pdo->prepare("SELECT e.*,(SELECT COUNT(*) FROM stages s WHERE s.entreprise_id=e.id) AS nb_stages FROM entreprises e $where ORDER BY nom LIMIT ? OFFSET ?");
$stmt->execute([...$params,$pg['per_page'],$pg['offset']]);
jsonResponse(['data'=>$stmt->fetchAll(),'pagination'=>$pg]);