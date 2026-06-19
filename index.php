<?php
// index.php — Routeur principal

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/middleware/cors.php';
require_once __DIR__ . '/middleware/auth.php';

setCorsHeaders();

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = '/' . trim($uri, '/');
$method = getMethod();

// ── Routes ───────────────────────────────────────────────────
$routes = [
    // Auth
    ['POST',  '/api/auth/login',              'auth/login'],
    ['GET',   '/api/auth/me',                 'auth/me'],

    // Étudiants
    ['GET',   '/api/etudiants',               'etudiants/list'],
    ['POST',  '/api/etudiants',               'etudiants/create'],
    ['GET',   '/api/etudiants/{id}',          'etudiants/show'],
    ['PUT',   '/api/etudiants/{id}',          'etudiants/update'],
    ['DELETE','/api/etudiants/{id}',          'etudiants/delete'],
    ['POST',  '/api/etudiants/import',        'etudiants/import'],

    // Entreprises
    ['GET',   '/api/entreprises',             'entreprises/list'],
    ['POST',  '/api/entreprises',             'entreprises/create'],
    ['GET',   '/api/entreprises/{id}',        'entreprises/show'],
    ['PUT',   '/api/entreprises/{id}',        'entreprises/update'],
    ['DELETE','/api/entreprises/{id}',        'entreprises/delete'],

    // Tuteurs
    ['GET',   '/api/tuteurs',                 'tuteurs/list'],
    ['POST',  '/api/tuteurs',                 'tuteurs/create'],
    ['GET',   '/api/tuteurs/{id}',            'tuteurs/show'],
    ['PUT',   '/api/tuteurs/{id}',            'tuteurs/update'],
    ['DELETE','/api/tuteurs/{id}',            'tuteurs/delete'],

    // Stages
    ['GET',   '/api/stages',                  'stages/list'],
    ['POST',  '/api/stages',                  'stages/create'],
    ['GET',   '/api/stages/{id}',             'stages/show'],
    ['PUT',   '/api/stages/{id}',             'stages/update'],
    ['DELETE','/api/stages/{id}',             'stages/delete'],
    ['PATCH', '/api/stages/{id}/statut',      'stages/statut'],
    ['PATCH', '/api/stages/{id}/convention',  'stages/convention'],

    // Visites
    ['GET',   '/api/visites',                 'visites/list'],
    ['POST',  '/api/visites',                 'visites/create'],
    ['GET',   '/api/visites/{id}',            'visites/show'],
    ['PUT',   '/api/visites/{id}',            'visites/update'],
    ['DELETE','/api/visites/{id}',            'visites/delete'],

    // Évaluations
    ['GET',   '/api/evaluations',             'evaluations/list'],
    ['POST',  '/api/evaluations',             'evaluations/create'],
    ['GET',   '/api/evaluations/{id}',        'evaluations/show'],
    ['PUT',   '/api/evaluations/{id}',        'evaluations/update'],
    ['DELETE','/api/evaluations/{id}',        'evaluations/delete'],

    // Documents
    ['GET',   '/api/documents',               'documents/list'],
    ['POST',  '/api/documents/upload',        'documents/upload'],
    ['PATCH', '/api/documents/{id}',          'documents/statut'],
    ['DELETE','/api/documents/{id}',          'documents/delete'],

    // Stats
    ['GET',   '/api/stats',                   'stats/index'],

    // Stagiaire — vue personnelle
    ['GET',   '/api/stagiaires/mon-stage',    'stagiaires/mon_stage'],
];

// ── Résolution de route ───────────────────────────────────────
$params = [];
$handler = null;

foreach ($routes as [$routeMethod, $routePath, $file]) {
    if ($method !== $routeMethod) continue;
    $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $routePath);
    if (preg_match('#^' . $pattern . '$#', $uri, $m)) {
        foreach ($m as $k => $v) {
            if (is_string($k)) $params[$k] = $v;
        }
        $handler = $file;
        break;
    }
}

if (!$handler) {
    jsonError("Route non trouvée : $method $uri", 404);
}

$handlerFile = __DIR__ . "/api/$handler.php";
if (!file_exists($handlerFile)) {
    jsonError("Handler introuvable : $handler", 500);
}

// Passe les paramètres d'URL au handler
$GLOBALS['route_params'] = $params;

require $handlerFile;
