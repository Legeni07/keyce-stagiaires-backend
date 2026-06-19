<?php
requireAuth(['admin','responsable']);
$body=getBody();
$req=['nom','prenom','email','type_tuteur'];
$errors=[];
foreach($req as $f) if(empty($body[$f])) $errors[]="$f requis";
if($errors) jsonError('Données invalides',422,$errors);
$pdo=getPDO();
try{
$pdo->prepare("INSERT INTO tuteurs (nom,prenom,email,telephone,type_tuteur,specialite,entreprise_id) VALUES (?,?,?,?,?,?,?)")
    ->execute([$body['nom'],$body['prenom'],$body['email'],$body['telephone']??null,$body['type_tuteur'],$body['specialite']??null,$body['entreprise_id']??null]);
$id=$pdo->lastInsertId();
jsonResponse(['message'=>'Tuteur créé','data'=>$pdo->query("SELECT * FROM tuteurs WHERE id=$id")->fetch()],201);
}catch(PDOException $e){if($e->getCode()==='23000')jsonError('Email déjà utilisé',409);throw $e;}