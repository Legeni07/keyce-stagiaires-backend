<?php
$auth=requireAuth(['stagiaire']);
if(!$auth['ref_id']) jsonError('Aucun profil étudiant associé',403);
$pdo=getPDO();
$etId=(int)$auth['ref_id'];
$s=$pdo->query("SELECT s.*,
    et.nom AS etudiant_nom,et.prenom AS etudiant_prenom,et.matricule,et.filiere,et.email AS etudiant_email,
    ent.nom AS entreprise_nom,ent.ville,ent.secteur,ent.telephone AS entreprise_tel,
    CONCAT(ta.nom,' ',ta.prenom) AS tuteur_acad_nom,ta.email AS tuteur_acad_email,ta.telephone AS tuteur_acad_tel,
    CONCAT(te.nom,' ',te.prenom) AS tuteur_entr_nom,te.email AS tuteur_entr_email,te.telephone AS tuteur_entr_tel
    FROM stages s
    JOIN etudiants et ON et.id=s.etudiant_id
    JOIN entreprises ent ON ent.id=s.entreprise_id
    LEFT JOIN tuteurs ta ON ta.id=s.tuteur_acad_id
    LEFT JOIN tuteurs te ON te.id=s.tuteur_entr_id
    WHERE s.etudiant_id=$etId AND s.statut IN('en_cours','en_attente')
    ORDER BY s.date_debut DESC LIMIT 1")->fetch();
if(!$s) jsonError('Aucun stage actif trouvé',404);
$sid=(int)$s['id'];
$jTotal=0; $jEcoules=0; $pct=0;
if($s['date_debut']&&$s['date_fin']){
  $debut=new DateTime($s['date_debut']); $fin=new DateTime($s['date_fin']); $now=new DateTime();
  $jTotal=(int)$debut->diff($fin)->days;
  $jEcoules=min($jTotal,(int)$debut->diff($now)->days);
  $pct=$jTotal>0?round($jEcoules/$jTotal*100):0;
  $s['jours_restants']=max(0,(int)$now->diff($fin)->days*(($now<=$fin)?1:-1));
}
$s['progression_pct']=$pct;
$s['jours_ecoules']=$jEcoules;
$s['jours_total']=$jTotal;
$visites=$pdo->query("SELECT v.*,CONCAT(t.nom,' ',t.prenom) AS tuteur_nom FROM visites v JOIN tuteurs t ON t.id=v.tuteur_id WHERE v.stage_id=$sid ORDER BY v.date_visite DESC")->fetchAll();
$evals=$pdo->query("SELECT *,(comp_techniques+comp_relation+autonomie+respect_delais)/4 AS moyenne FROM evaluations WHERE stage_id=$sid")->fetchAll();
$docs=$pdo->query("SELECT * FROM documents WHERE stage_id=$sid ORDER BY created_at DESC")->fetchAll();
jsonResponse(['data'=>$s,'visites'=>$visites,'evaluations'=>$evals,'documents'=>$docs]);