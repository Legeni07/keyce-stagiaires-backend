<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$v=$pdo->query("SELECT v.*,CONCAT(t.nom,' ',t.prenom) AS tuteur_nom FROM visites v JOIN tuteurs t ON t.id=v.tuteur_id WHERE v.id=$id")->fetch();
if(!$v) jsonError('Visite introuvable',404);
jsonResponse(['data'=>$v]);