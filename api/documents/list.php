<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
$pdo=getPDO(); $q=$_GET;
$where="WHERE 1=1"; $params=[];
if(!empty($q['stage_id'])) {$where.=" AND stage_id = ?"; $params[]=(int)$q['stage_id'];}
if(!empty($q['statut']))   {$where.=" AND statut = ?";   $params[]=$q['statut'];}
if(!empty($q['type']))     {$where.=" AND type = ?";     $params[]=$q['type'];}
$stmt=$pdo->prepare("SELECT * FROM documents $where ORDER BY created_at DESC");
$stmt->execute($params);
jsonResponse(['data'=>$stmt->fetchAll()]);