<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $q=$_GET;
$where="WHERE 1=1"; $params=[];
if(!empty($q['search'])){$s="%{$q['search']}%";$where.=" AND (t.nom LIKE ? OR t.prenom LIKE ? OR t.email LIKE ?)";array_push($params,$s,$s,$s);}
if(!empty($q['type_tuteur'])){$where.=" AND t.type_tuteur = ?";$params[]=$q['type_tuteur'];}
if(!empty($q['entreprise_id'])){$where.=" AND t.entreprise_id = ?";$params[]=(int)$q['entreprise_id'];}
$pg=paginate($pdo,'tuteurs t',$where,$params,$q);
$stmt=$pdo->prepare("SELECT t.*,e.nom AS entreprise_nom FROM tuteurs t LEFT JOIN entreprises e ON e.id=t.entreprise_id $where ORDER BY t.nom LIMIT ? OFFSET ?");
$stmt->execute([...$params,$pg['per_page'],$pg['offset']]);
jsonResponse(['data'=>$stmt->fetchAll(),'pagination'=>$pg]);