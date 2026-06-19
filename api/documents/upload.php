<?php
requireAuth(['admin','responsable','tuteur','stagiaire']);
if(empty($_POST['stage_id'])) jsonError('stage_id requis',422);
if(empty($_FILES['fichier'])) jsonError('Aucun fichier reçu',422);

$stageId=(int)$_POST['stage_id'];
$file=$_FILES['fichier'];
$type=$_POST['type']??'autre';

$maxBytes=MAX_FILE_MB*1024*1024;
if($file['size']>$maxBytes) jsonError('Fichier trop volumineux (max '.MAX_FILE_MB.'Mo)',422);

$allowed=['application/pdf','image/jpeg','image/png','application/msword',
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$mime=mime_content_type($file['tmp_name']);
if(!in_array($mime,$allowed)) jsonError('Type de fichier non autorisé',422);

$ext=pathinfo($file['name'],PATHINFO_EXTENSION);
$nom=uniqid('doc_')."_".time().".$ext";
$chemin=UPLOAD_DIR.$nom;
if(!move_uploaded_file($file['tmp_name'],$chemin)) jsonError('Erreur lors de l'enregistrement du fichier',500);

$pdo=getPDO();
$pdo->prepare("INSERT INTO documents (stage_id,type,nom_fichier,chemin,taille,mime_type,statut) VALUES (?,?,?,?,?,?,'en_attente')")
    ->execute([$stageId,$type,$file['name'],'uploads/'.$nom,$file['size'],$mime]);
$id=$pdo->lastInsertId();
jsonResponse(['message'=>'Fichier uploadé','data'=>$pdo->query("SELECT * FROM documents WHERE id=$id")->fetch()],201);