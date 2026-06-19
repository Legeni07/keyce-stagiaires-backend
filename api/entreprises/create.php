<?php
requireAuth(['admin','responsable']);
$body=getBody();
if(empty($body['nom'])) jsonError('nom requis',422);
$pdo=getPDO();
$pdo->prepare("INSERT INTO entreprises (nom,secteur,ville,pays,email_contact,telephone,site_web,partenaire) VALUES (?,?,?,?,?,?,?,?)")
    ->execute([$body['nom'],$body['secteur']??null,$body['ville']??null,$body['pays']??'Cameroun',$body['email_contact']??null,$body['telephone']??null,$body['site_web']??null,(int)($body['partenaire']??0)]);
$id=$pdo->lastInsertId();
jsonResponse(['message'=>'Entreprise créée','data'=>$pdo->query("SELECT * FROM entreprises WHERE id=$id")->fetch()],201);