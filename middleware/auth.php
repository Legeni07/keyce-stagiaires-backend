<?php
// middleware/auth.php

require_once __DIR__ . '/../config/database.php';

// ── JWT pur PHP (HS256, sans dépendance) ─────────────────────

function jwtEncode(array $payload): string {
    $header  = base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64url(json_encode($payload));
    $sig     = base64url(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    return "$header.$payload.$sig";
}

function jwtDecode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;
    [$header, $payload, $sig] = $parts;
    $expected = base64url(hash_hmac('sha256', "$header.$payload", JWT_SECRET, true));
    if (!hash_equals($expected, $sig)) return null;
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
    if (!$data || ($data['exp'] ?? 0) < time()) return null;
    return $data;
}

function base64url(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

// ── Extraction du JWT depuis l'en-tête ───────────────────────

function getBearerToken(): ?string {
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)$/i', $auth, $m)) return $m[1];
    return null;
}

// ── Middleware : authentification obligatoire ─────────────────

function requireAuth(array $roles = []): array {
    $token = getBearerToken();
    if (!$token) jsonError('Token manquant', 401);

    $payload = jwtDecode($token);
    if (!$payload) jsonError('Token invalide ou expiré', 401);

    if ($roles && !in_array($payload['role'], $roles, true)) {
        jsonError('Accès refusé — rôle insuffisant', 403);
    }
    return $payload;
}

// ── Middleware optionnel (pas d'erreur si pas de token) ───────

function optionalAuth(): ?array {
    $token = getBearerToken();
    if (!$token) return null;
    return jwtDecode($token);
}
