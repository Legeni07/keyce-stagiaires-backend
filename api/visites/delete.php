<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
if(!$pdo->query("SELECT id FROM visites WHERE id=$id")->fetch()) jsonError('Visite introuvable',404);
$pdo->exec("DELETE FROM visites WHERE id=$id");
jsonResponse(['message'=>'Visite supprimée']);