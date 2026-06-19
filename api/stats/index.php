<?php
requireAuth(['admin','responsable','tuteur']);
$pdo=getPDO();
$stats=[
  'nb_etudiants'         => (int)$pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn(),
  'nb_entreprises'       => (int)$pdo->query("SELECT COUNT(*) FROM entreprises")->fetchColumn(),
  'nb_entreprises_partenaires' => (int)$pdo->query("SELECT COUNT(*) FROM entreprises WHERE partenaire=1")->fetchColumn(),
  'nb_tuteurs'           => (int)$pdo->query("SELECT COUNT(*) FROM tuteurs")->fetchColumn(),
  'nb_stages'            => (int)$pdo->query("SELECT COUNT(*) FROM stages")->fetchColumn(),
  'stages_en_attente'    => (int)$pdo->query("SELECT COUNT(*) FROM stages WHERE statut='en_attente'")->fetchColumn(),
  'stages_en_cours'      => (int)$pdo->query("SELECT COUNT(*) FROM stages WHERE statut='en_cours'")->fetchColumn(),
  'stages_termines'      => (int)$pdo->query("SELECT COUNT(*) FROM stages WHERE statut='termine'")->fetchColumn(),
  'stages_sans_convention'=> (int)$pdo->query("SELECT COUNT(*) FROM stages WHERE convention_signee=0 AND statut IN('en_attente','en_cours') AND DATEDIFF(NOW(),created_at)>7")->fetchColumn(),
  'stages_sans_visite'   => (int)$pdo->query("SELECT COUNT(*) FROM stages s WHERE statut='en_cours' AND NOT EXISTS(SELECT 1 FROM visites v WHERE v.stage_id=s.id AND v.date_visite>DATE_SUB(NOW(),INTERVAL 30 DAY))")->fetchColumn(),
  'documents_en_attente' => (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE statut='en_attente'")->fetchColumn(),
  'repartition_filieres' => $pdo->query("SELECT et.filiere,COUNT(*) AS total FROM stages s JOIN etudiants et ON et.id=s.etudiant_id WHERE s.statut='en_cours' GROUP BY et.filiere ORDER BY total DESC")->fetchAll(),
  'repartition_type_stage'=> $pdo->query("SELECT type_stage,COUNT(*) AS total FROM stages GROUP BY type_stage ORDER BY total DESC")->fetchAll(),
  'repartition_statut'   => $pdo->query("SELECT statut,COUNT(*) AS total FROM stages GROUP BY statut")->fetchAll(),
];
jsonResponse(['data'=>$stats,'generated_at'=>date('c')]);