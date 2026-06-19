<?php
requireAuth(['admin','responsable','tuteur']);
$body=getBody();
if(empty($body['stage_id'])||empty($body['evaluateur_type'])) jsonError('stage_id et evaluateur_type requis',422);
$pdo=getPDO();
$pdo->prepare("INSERT INTO evaluations (stage_id,evaluateur_type,comp_techniques,comp_relation,autonomie,respect_delais,commentaire,recommande_embauche) VALUES (?,?,?,?,?,?,?,?)")
    ->execute([(int)$body['stage_id'],$body['evaluateur_type'],(float)($body['comp_techniques']??0),(float)($body['comp_relation']??0),
               (float)($body['autonomie']??0),(float)($body['respect_delais']??0),$body['commentaire']??null,(int)($body['recommande_embauche']??0)]);
$id=$pdo->lastInsertId();
// Mettre à jour la note moyenne du stage
$pdo->exec("UPDATE stages SET note=(SELECT AVG((comp_techniques+comp_relation+autonomie+respect_delais)/4) FROM evaluations WHERE stage_id=(SELECT stage_id FROM evaluations WHERE id=$id)) WHERE id=(SELECT stage_id FROM evaluations WHERE id=$id)");
jsonResponse(['message'=>'Évaluation créée','data'=>$pdo->query("SELECT * FROM evaluations WHERE id=$id")->fetch()],201);