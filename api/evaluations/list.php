<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
$pdo=getPDO(); $q=$_GET;
$where="WHERE 1=1"; $params=[];
if(!empty($q['stage_id']))        {$where.=" AND stage_id = ?";         $params[]=(int)$q['stage_id'];}
if(!empty($q['evaluateur_type'])) {$where.=" AND evaluateur_type = ?"; $params[]=$q['evaluateur_type'];}
$pg=paginate($pdo,'evaluations',$where,$params,$q);
$stmt=$pdo->prepare("SELECT e.*,(e.comp_techniques+e.comp_relation+e.autonomie+e.respect_delais)/4 AS moyenne FROM evaluations e $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([...$params,$pg['per_page'],$pg['offset']]);
jsonResponse(['data'=>$stmt->fetchAll(),'pagination'=>$pg]);