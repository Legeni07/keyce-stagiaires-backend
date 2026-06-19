<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
$statuts=['en_attente','valide','rejete'];
if(!in_array($body['statut']??'',$statuts)) jsonError('Statut invalide. Valeurs: '.implode(',',$statuts),422);
if(!$pdo->query("SELECT id FROM documents WHERE id=$id")->fetch()) jsonError('Document introuvable',404);
$pdo->prepare("UPDATE documents SET statut=? WHERE id=?")->execute([$body['statut'],$id]);
jsonResponse(['message'=>'Statut document mis à jour','statut'=>$body['statut']]);