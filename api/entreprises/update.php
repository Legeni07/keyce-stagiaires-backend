<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM entreprises WHERE id=$id")->fetch()) jsonError('Entreprise introuvable',404);
$fields=['nom','secteur','ville','pays','email_contact','telephone','site_web','partenaire'];
$set=[]; $params=[];
foreach($fields as $f) if(array_key_exists($f,$body)){$set[]="$f=?";$params[]=$body[$f];}
if(!$set) jsonError('Aucun champ',422);
$params[]=$id;
$pdo->prepare("UPDATE entreprises SET ".implode(',',$set)." WHERE id=?")->execute($params);
jsonResponse(['message'=>'Entreprise mise à jour','data'=>$pdo->query("SELECT * FROM entreprises WHERE id=$id")->fetch()]);