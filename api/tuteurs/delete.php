<?php
requireAuth(['admin']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
if(!$pdo->query("SELECT id FROM tuteurs WHERE id=$id")->fetch()) jsonError('Tuteur introuvable',404);
$pdo->exec("DELETE FROM tuteurs WHERE id=$id");
jsonResponse(['message'=>'Tuteur supprimé']);