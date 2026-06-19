<?php
// middleware/cors.php

function setCorsHeaders(): void {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

function jsonResponse(mixed $data, int $code = 200): never {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function jsonError(string $message, int $code = 400, array $errors = []): never {
    $body = ['error' => true, 'message' => $message];
    if ($errors) $body['errors'] = $errors;
    jsonResponse($body, $code);
}

function getBody(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function getMethod(): string {
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function paginate(PDO $pdo, string $table, string $where, array $params, array $query): array {
    $page     = max(1, (int)($query['page'] ?? 1));
    $perPage  = min(50, max(1, (int)($query['per_page'] ?? 10)));
    $offset   = ($page - 1) * $perPage;

    $countSql  = "SELECT COUNT(*) FROM $table $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    return [
        'page'      => $page,
        'per_page'  => $perPage,
        'offset'    => $offset,
        'total'     => $total,
        'last_page' => max(1, (int)ceil($total / $perPage)),
    ];
}
