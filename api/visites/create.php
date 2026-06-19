<?php
requireAuth(['admin','responsable','tuteur']);
$body=getBody();
$req=['stage_id','tuteur_id','date_visite'];
$errors=[];
foreach($req as $f) if(empty($body[$f])) $errors[]="$f requis";
if($errors) jsonError('Données invalides',422,$errors);
$pdo=getPDO();
$pdo->prepare("INSERT INTO visites (stage_id,tuteur_id,date_visite,type_visite,compte_rendu,observations,prochaine_visite) VALUES (?,?,?,?,?,?,?)")
    ->execute([(int)$body['stage_id'],(int)$body['tuteur_id'],$body['date_visite'],$body['type_visite']??'sur_site',
               $body['compte_rendu']??null,$body['observations']??null,$body['prochaine_visite']??null]);
$id=$pdo->lastInsertId();
jsonResponse(['message'=>'Visite créée','data'=>$pdo->query("SELECT * FROM visites WHERE id=$id")->fetch()],201);