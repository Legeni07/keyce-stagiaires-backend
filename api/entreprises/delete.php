<?php
requireAuth(['admin']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
if(!$pdo->query("SELECT id FROM entreprises WHERE id=$id")->fetch()) jsonError('Entreprise introuvable',404);
try{$pdo->exec("DELETE FROM entreprises WHERE id=$id"); jsonResponse(['message'=>'Entreprise supprimée']);}
catch(PDOException $e){jsonError('Suppression impossible : des stages sont liés',409);}