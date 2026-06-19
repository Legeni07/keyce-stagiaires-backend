<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
$statuts=['en_attente','en_cours','termine','abandonne'];
if(!in_array($body['statut']??'',$statuts)) jsonError('Statut invalide. Valeurs: '.implode(',',$statuts),422);
if(!$pdo->query("SELECT id FROM stages WHERE id=$id")->fetch()) jsonError('Stage introuvable',404);
$pdo->prepare("UPDATE stages SET statut=? WHERE id=?")->execute([$body['statut'],$id]);
jsonResponse(['message'=>'Statut mis à jour','statut'=>$body['statut']]);