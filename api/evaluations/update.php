<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM evaluations WHERE id=$id")->fetch()) jsonError('Évaluation introuvable',404);
$fields=['comp_techniques','comp_relation','autonomie','respect_delais','commentaire','recommande_embauche'];
$set=[]; $params=[];
foreach($fields as $f) if(array_key_exists($f,$body)){$set[]="$f=?";$params[]=$body[$f];}
if(!$set) jsonError('Aucun champ',422);
$params[]=$id;
$pdo->prepare("UPDATE evaluations SET ".implode(',',$set)." WHERE id=?")->execute($params);
jsonResponse(['message'=>'Évaluation mise à jour','data'=>$pdo->query("SELECT *,(comp_techniques+comp_relation+autonomie+respect_delais)/4 AS moyenne FROM evaluations WHERE id=$id")->fetch()]);