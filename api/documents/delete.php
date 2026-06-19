<?php
requireAuth(['admin','responsable']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$doc=$pdo->query("SELECT * FROM documents WHERE id=$id")->fetch();
if(!$doc) jsonError('Document introuvable',404);
if(file_exists(UPLOAD_DIR.basename($doc['chemin']))) unlink(UPLOAD_DIR.basename($doc['chemin']));
$pdo->exec("DELETE FROM documents WHERE id=$id");
jsonResponse(['message'=>'Document supprimé']);