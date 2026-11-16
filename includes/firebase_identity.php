<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// No private key is needed to verify Firebase client ID tokens.
function firebaseGoogleIdentity(string $idToken): array
{
    if (strlen($idToken) > 16384 || substr_count($idToken, '.') !== 2) {
        throw new UnexpectedValueException('Invalid token');
    }
    $ch = curl_init('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 15, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2]);
    $raw = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false || $status !== 200) {
        throw new RuntimeException('Google verification service unavailable');
    }
    $certificates = json_decode($raw, true, 16, JSON_THROW_ON_ERROR);
    if (!is_array($certificates) || !$certificates) {
        throw new RuntimeException('Google verification keys unavailable');
    }
    $keys = [];
    foreach ($certificates as $kid => $certificate) {
        $keys[$kid] = new Key($certificate, 'RS256');
    }
    $claims = JWT::decode($idToken, $keys);
    validateFirebaseGoogleClaims($claims);
    return ['uid' => $claims->sub, 'email' => strtolower(trim($claims->email)),
        'name' => is_string($claims->name ?? null) ? trim($claims->name) : ''];
}

function validateFirebaseGoogleClaims(object $claims): void
{
    $now = time();
    if (($claims->aud ?? null) !== 'home-of-hope-app'
        || ($claims->iss ?? null) !== 'https://securetoken.google.com/home-of-hope-app'
        || !is_string($claims->sub ?? null) || $claims->sub === '' || strlen($claims->sub) > 128
        || !is_int($claims->exp ?? null) || $claims->exp <= $now
        || !is_int($claims->iat ?? null) || $claims->iat > $now
        || !is_int($claims->auth_time ?? null) || $claims->auth_time > $now
        || $claims->auth_time < $now - 600
        || ($claims->email_verified ?? false) !== true
        || ($claims->firebase->sign_in_provider ?? null) !== 'google.com'
        || !is_string($claims->email ?? null) || strlen($claims->email) > 150
        || !filter_var($claims->email, FILTER_VALIDATE_EMAIL)) {
        throw new UnexpectedValueException('Sign in with Google again');
    }
}
