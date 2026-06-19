<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM visites WHERE id=$id")->fetch()) jsonError('Visite introuvable',404);
$fields=['date_visite','type_visite','compte_rendu','observations','prochaine_visite'];
$set=[]; $params=[];
foreach($fields as $f) if(array_key_exists($f,$body)){$set[]="$f=?";$params[]=$body[$f];}
if(!$set) jsonError('Aucun champ',422);
$params[]=$id;
$pdo->prepare("UPDATE visites SET ".implode(',',$set)." WHERE id=?")->execute($params);
jsonResponse(['message'=>'Visite mise à jour','data'=>$pdo->query("SELECT * FROM visites WHERE id=$id")->fetch()]);