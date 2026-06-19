<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM stages WHERE id=$id")->fetch()) jsonError('Stage introuvable',404);
$fields=['etudiant_id','entreprise_id','tuteur_acad_id','tuteur_entr_id','intitule_poste','description','date_debut','date_fin','type_stage','statut','note','convention_signee'];
$set=[]; $params=[];
foreach($fields as $f) if(array_key_exists($f,$body)){$set[]="$f=?";$params[]=$body[$f];}
if(!$set) jsonError('Aucun champ',422);
$params[]=$id;
$pdo->prepare("UPDATE stages SET ".implode(',',$set)." WHERE id=?")->execute($params);
jsonResponse(['message'=>'Stage mis à jour','data'=>$pdo->query("SELECT * FROM stages WHERE id=$id")->fetch()]);