<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $q=$_GET;
$where="WHERE 1=1"; $params=[];
if(!empty($q['stage_id']))  {$where.=" AND v.stage_id = ?";  $params[]=(int)$q['stage_id'];}
if(!empty($q['tuteur_id'])) {$where.=" AND v.tuteur_id = ?"; $params[]=(int)$q['tuteur_id'];}
if(!empty($q['type']))      {$where.=" AND v.type_visite = ?";$params[]=$q['type'];}
$pg=paginate($pdo,'visites v',$where,$params,$q);
$stmt=$pdo->prepare("SELECT v.*,CONCAT(t.nom,' ',t.prenom) AS tuteur_nom,s.intitule_poste,et.nom AS etudiant_nom,et.prenom AS etudiant_prenom
    FROM visites v JOIN tuteurs t ON t.id=v.tuteur_id JOIN stages s ON s.id=v.stage_id JOIN etudiants et ON et.id=s.etudiant_id
    $where ORDER BY v.date_visite DESC LIMIT ? OFFSET ?");
$stmt->execute([...$params,$pg['per_page'],$pg['offset']]);
jsonResponse(['data'=>$stmt->fetchAll(),'pagination'=>$pg]);