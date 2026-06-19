<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM tuteurs WHERE id=$id")->fetch()) jsonError('Tuteur introuvable',404);
$fields=['nom','prenom','email','telephone','type_tuteur','specialite','entreprise_id'];
$set=[]; $params=[];
foreach($fields as $f) if(array_key_exists($f,$body)){$set[]="$f=?";$params[]=$body[$f];}
if(!$set) jsonError('Aucun champ',422);
$params[]=$id;
$pdo->prepare("UPDATE tuteurs SET ".implode(',',$set)." WHERE id=?")->execute($params);
jsonResponse(['message'=>'Tuteur mis à jour','data'=>$pdo->query("SELECT * FROM tuteurs WHERE id=$id")->fetch()]);