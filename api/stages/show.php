<?php
requireAuth();
$pdo=getPDO(); $id=(int)($GLOBALS['route_params']['id']??0);
$s=$pdo->query("SELECT s.*,et.nom AS etudiant_nom,et.prenom AS etudiant_prenom,et.matricule,et.filiere,et.email AS etudiant_email,
    ent.nom AS entreprise_nom,ent.ville,ent.secteur,
    CONCAT(ta.nom,' ',ta.prenom) AS tuteur_acad_nom,ta.email AS tuteur_acad_email,ta.telephone AS tuteur_acad_tel,
    CONCAT(te.nom,' ',te.prenom) AS tuteur_entr_nom,te.email AS tuteur_entr_email,te.telephone AS tuteur_entr_tel
    FROM stages s
    JOIN etudiants et ON et.id=s.etudiant_id
    JOIN entreprises ent ON ent.id=s.entreprise_id
    LEFT JOIN tuteurs ta ON ta.id=s.tuteur_acad_id
    LEFT JOIN tuteurs te ON te.id=s.tuteur_entr_id
    WHERE s.id=$id")->fetch();
if(!$s) jsonError('Stage introuvable',404);
$visites=$pdo->query("SELECT v.*,CONCAT(t.nom,' ',t.prenom) AS tuteur_nom FROM visites v JOIN tuteurs t ON t.id=v.tuteur_id WHERE v.stage_id=$id ORDER BY v.date_visite DESC")->fetchAll();
$evals=$pdo->query("SELECT * FROM evaluations WHERE stage_id=$id")->fetchAll();
$docs=$pdo->query("SELECT * FROM documents WHERE stage_id=$id ORDER BY created_at DESC")->fetchAll();
jsonResponse(['data'=>$s,'visites'=>$visites,'evaluations'=>$evals,'documents'=>$docs]);