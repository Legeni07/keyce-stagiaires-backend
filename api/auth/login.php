<?php
// api/auth/login.php — POST /api/auth/login

$body  = getBody();
$email = trim($body['email'] ?? '');
$mdp   = $body['password'] ?? '';

if (!$email || !$mdp) jsonError('Email et mot de passe requis', 422);

$pdo  = getPDO();
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($mdp, $user['mot_de_passe'])) {
    jsonError('Identifiants incorrects', 401);
}

$payload = [
    'sub'    => $user['id'],
    'email'  => $user['email'],
    'role'   => $user['role'],
    'ref_id' => $user['ref_id'],
    'iat'    => time(),
    'exp'    => time() + JWT_EXPIRY,
];

$token = jwtEncode($payload);

jsonResponse([
    'token'   => $token,
    'expires_in' => JWT_EXPIRY,
    'user'    => [
        'id'     => $user['id'],
        'nom'    => $user['nom'],
        'prenom' => $user['prenom'],
        'email'  => $user['email'],
        'role'   => $user['role'],
        'ref_id' => $user['ref_id'],
    ],
]);
