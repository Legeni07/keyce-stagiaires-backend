<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$e=$pdo->query("SELECT *,(comp_techniques+comp_relation+autonomie+respect_delais)/4 AS moyenne FROM evaluations WHERE id=$id")->fetch();
if(!$e) jsonError('Évaluation introuvable',404);
jsonResponse(['data'=>$e]);