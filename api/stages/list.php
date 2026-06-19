<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
$auth=requireAuth();
$pdo=getPDO(); $q=$_GET;
$where="WHERE 1=1"; $params=[];
if(!empty($q['statut']))       {$where.=" AND s.statut = ?";        $params[]=$q['statut'];}
if(!empty($q['type_stage']))   {$where.=" AND s.type_stage = ?";    $params[]=$q['type_stage'];}
if(!empty($q['filiere']))      {$where.=" AND et.filiere = ?";      $params[]=$q['filiere'];}
if(!empty($q['entreprise_id'])){$where.=" AND s.entreprise_id = ?"; $params[]=(int)$q['entreprise_id'];}
if(!empty($q['etudiant_id']))  {$where.=" AND s.etudiant_id = ?";   $params[]=(int)$q['etudiant_id'];}
if(!empty($q['search']))       {$s="%{$q['search']}%";$where.=" AND (et.nom LIKE ? OR et.prenom LIKE ? OR ent.nom LIKE ?)";array_push($params,$s,$s,$s);}
if($auth['role']==='tuteur'&&$auth['ref_id']){
  $where.=" AND (s.tuteur_acad_id = ? OR s.tuteur_entr_id = ?)";
  array_push($params,$auth['ref_id'],$auth['ref_id']);
}
$pg=paginate($pdo,'stages s JOIN etudiants et ON et.id=s.etudiant_id JOIN entreprises ent ON ent.id=s.entreprise_id',$where,$params,$q);
$sql="SELECT s.*,et.nom AS etudiant_nom,et.prenom AS etudiant_prenom,et.matricule,et.filiere,ent.nom AS entreprise_nom,
      CONCAT(ta.nom,' ',ta.prenom) AS tuteur_acad_nom,CONCAT(te.nom,' ',te.prenom) AS tuteur_entr_nom
      FROM stages s
      JOIN etudiants et ON et.id=s.etudiant_id
      JOIN entreprises ent ON ent.id=s.entreprise_id
      LEFT JOIN tuteurs ta ON ta.id=s.tuteur_acad_id
      LEFT JOIN tuteurs te ON te.id=s.tuteur_entr_id
      $where ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
$stmt=$pdo->prepare($sql);
$stmt->execute([...$params,$pg['per_page'],$pg['offset']]);
jsonResponse(['data'=>$stmt->fetchAll(),'pagination'=>$pg]);