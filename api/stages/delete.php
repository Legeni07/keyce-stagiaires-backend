<?php
requireAuth(['admin']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
if(!$pdo->query("SELECT id FROM stages WHERE id=$id")->fetch()) jsonError('Stage introuvable',404);
$pdo->exec("DELETE FROM stages WHERE id=$id");
jsonResponse(['message'=>'Stage supprimé']);