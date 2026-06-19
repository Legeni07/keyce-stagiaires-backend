<?php
requireAuth();
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$e=$pdo->query("SELECT * FROM entreprises WHERE id=$id")->fetch();
if(!$e) jsonError('Entreprise introuvable',404);
$stagiaires=$pdo->query("SELECT et.nom,et.prenom,et.matricule,s.intitule_poste,s.statut FROM stages s JOIN etudiants et ON et.id=s.etudiant_id WHERE s.entreprise_id=$id ORDER BY s.date_debut DESC")->fetchAll();
$tuteurs=$pdo->query("SELECT * FROM tuteurs WHERE entreprise_id=$id")->fetchAll();
jsonResponse(['data'=>$e,'stagiaires'=>$stagiaires,'tuteurs'=>$tuteurs]);