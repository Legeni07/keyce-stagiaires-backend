<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$t=$pdo->query("SELECT t.*,e.nom AS entreprise_nom FROM tuteurs t LEFT JOIN entreprises e ON e.id=t.entreprise_id WHERE t.id=$id")->fetch();
if(!$t) jsonError('Tuteur introuvable',404);
$stages=$pdo->query("SELECT s.id,s.intitule_poste,s.statut,et.nom,et.prenom,et.matricule FROM stages s JOIN etudiants et ON et.id=s.etudiant_id WHERE s.tuteur_acad_id=$id OR s.tuteur_entr_id=$id")->fetchAll();
jsonResponse(['data'=>$t,'stages'=>$stages]);