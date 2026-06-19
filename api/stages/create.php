<?php
requireAuth(['admin','responsable']);
$body=getBody();
$req=['etudiant_id','entreprise_id'];
$errors=[];
foreach($req as $f) if(empty($body[$f])) $errors[]="$f requis";
if($errors) jsonError('Données invalides',422,$errors);
$pdo=getPDO();
$pdo->prepare("INSERT INTO stages (etudiant_id,entreprise_id,tuteur_acad_id,tuteur_entr_id,intitule_poste,description,date_debut,date_fin,type_stage,statut,convention_signee)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)")
    ->execute([(int)$body['etudiant_id'],(int)$body['entreprise_id'],$body['tuteur_acad_id']??null,$body['tuteur_entr_id']??null,
               $body['intitule_poste']??null,$body['description']??null,$body['date_debut']??null,$body['date_fin']??null,
               $body['type_stage']??'stage_fin_etudes',$body['statut']??'en_attente',(int)($body['convention_signee']??0)]);
$id=$pdo->lastInsertId();
jsonResponse(['message'=>'Stage créé','data'=>$pdo->query("SELECT * FROM stages WHERE id=$id")->fetch()],201);