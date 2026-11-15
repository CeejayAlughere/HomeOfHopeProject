<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function sendJson(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode(
        $payload,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );
    exit;
}

function requirePostRequest(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        sendJson(405, [
            'success' => false,
            'message' => 'Only POST requests are allowed.'
        ]);
    }
}

function readJsonBody(): array
{
    $rawBody = file_get_contents('php://input');

    if ($rawBody === false || trim($rawBody) === '') {
        sendJson(400, [
            'success' => false,
            'message' => 'The request body is empty.'
        ]);
    }

    $body = json_decode($rawBody, true);

    if (!is_array($body)) {
        sendJson(400, [
            'success' => false,
            'message' => 'The request body must contain valid JSON.'
        ]);
    }

    return $body;
}

function cleanText($value): string
{
    return trim((string) ($value ?? ''));
}

function ensureApiTokenTable(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS api_tokens (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            token_hash CHAR(64) NOT NULL UNIQUE,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_used_at DATETIME NULL,
            INDEX idx_api_tokens_user_id (user_id),
            INDEX idx_api_tokens_expires_at (expires_at),
            CONSTRAINT fk_api_tokens_user
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );
}

function issueApiToken(PDO $pdo, int $userId): string
{
    $plainToken = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $plainToken);

    $stmt = $pdo->prepare(
        'INSERT INTO api_tokens (user_id, token_hash, expires_at)
         VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))'
    );
    $stmt->execute([$userId, $tokenHash]);

    return $plainToken;
}

function getBearerToken(): ?string
{
    $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
        return null;
    }

    $token = trim($matches[1]);
    return $token !== '' ? $token : null;
}

function revokeApiToken(PDO $pdo, string $plainToken): void
{
    $stmt = $pdo->prepare(
        'DELETE FROM api_tokens WHERE token_hash = ?'
    );
    $stmt->execute([hash('sha256', $plainToken)]);
}

function findUserByIdentifier(PDO $pdo, string $identifier)
{
    $stmt = $pdo->prepare(
        'SELECT * FROM users
         WHERE LOWER(username) = LOWER(?)
            OR LOWER(email) = LOWER(?)
         LIMIT 1'
    );
    $stmt->execute([$identifier, $identifier]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function userPayload(array $user): array
{
    $createdAt = isset($user['created_at'])
        ? strtotime((string) $user['created_at'])
        : false;

    return [
        'id' => (int) $user['id'],
        'fullName' => (string) $user['full_name'],
        'idNumber' => (string) $user['sa_id'],
        'username' => (string) $user['username'],
        'email' => (string) $user['email'],
        'memberNumber' => 'HOH-' . str_pad(
            (string) $user['id'],
            6,
            '0',
            STR_PAD_LEFT
        ),
        'status' => 'Active',
        'joinDate' => ($createdAt !== false ? $createdAt : time()) * 1000
    ];
}

try {
    ensureApiTokenTable(getPDO());
} catch (Throwable $e) {
    error_log('Home of Hope API setup error: ' . $e->getMessage());
    sendJson(500, [
        'success' => false,
        'message' => 'The account service is temporarily unavailable.'
    ]);
}
