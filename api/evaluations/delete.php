<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
if(!$pdo->query("SELECT id FROM evaluations WHERE id=$id")->fetch()) jsonError('Évaluation introuvable',404);
$pdo->exec("DELETE FROM evaluations WHERE id=$id");
jsonResponse(['message'=>'Évaluation supprimée']);