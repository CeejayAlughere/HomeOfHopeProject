<?php
declare(strict_types=1);
$root = $argv[1] ?? getcwd();
require rtrim($root, '/') . '/includes/firebase_identity.php';
$valid = (object) [
    'aud' => 'home-of-hope-app',
    'iss' => 'https://securetoken.google.com/home-of-hope-app',
    'sub' => 'test-uid', 'exp' => time() + 3600, 'iat' => time(),
    'auth_time' => time(), 'email' => 'test@example.com', 'email_verified' => true,
    'firebase' => (object) ['sign_in_provider' => 'google.com']
];
validateFirebaseGoogleClaims($valid);
$cases = [
    ['aud', 'another-project'], ['iss', 'https://example.com'], ['sub', ''],
    ['exp', time() - 1], ['iat', time() + 3600], ['auth_time', time() - 700],
    ['email_verified', false], ['email', 'invalid'],
    ['firebase', (object) ['sign_in_provider' => 'password']]
];
foreach ($cases as [$field, $value]) {
    $claims = clone $valid;
    $claims->$field = $value;
    try {
        validateFirebaseGoogleClaims($claims);
    } catch (UnexpectedValueException $e) {
        continue;
    }
    fwrite(STDERR, "FAIL: accepted invalid $field\n");
    exit(1);
}
try {
    firebaseGoogleIdentity('not-a-token');
    fwrite(STDERR, "FAIL: accepted malformed token\n");
    exit(1);
} catch (UnexpectedValueException $e) {}
echo "PASS: claim rejection checks and malformed-token rejection.\n";
echo "Real Google signature verification and account linking still require the sign-in tests.\n";
