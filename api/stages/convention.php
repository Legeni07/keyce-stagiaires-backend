<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0); $body=getBody();
if(!$pdo->query("SELECT id FROM stages WHERE id=$id")->fetch()) jsonError('Stage introuvable',404);
$val=(int)($body['convention_signee']??1);
$pdo->prepare("UPDATE stages SET convention_signee=? WHERE id=?")->execute([$val,$id]);
jsonResponse(['message'=>'Convention mise à jour','convention_signee'=>$val]);