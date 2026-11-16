<?php
declare(strict_types=1);

// Returns [HTTP status, public response]. Call only after verifying Firebase identity.
function resolveGoogleAccount(PDO $pdo, array $identity, array $input): array
{
    // Serialise the small identity provisioning operation, including password attempt limits.
    $lock = $pdo->query("SELECT GET_LOCK('homeofhope_google_identity', 5)")->fetchColumn();
    if ((int) $lock !== 1) return [503, ['success' => false, 'message' => 'Please try again shortly.']];
    try {
        $stmt = $pdo->prepare('SELECT u.* FROM google_identities g JOIN users u ON u.id = g.user_id WHERE g.firebase_uid = ?');
        $stmt->execute([$identity['uid']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) return [200, ['success' => true, 'account' => $user]];

        $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?) LIMIT 2');
        $stmt->execute([$identity['email']]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($matches) > 1) return [409, ['success' => false, 'message' => 'More than one account uses this email. Contact support to resolve the duplicate accounts.']];
        if ($matches) {
            $user = $matches[0];
            $stmt = $pdo->prepare('SELECT firebase_uid FROM google_identities WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            if ($stmt->fetchColumn()) return [409, ['success' => false, 'message' => 'This website account is already linked to a different Google identity.']];
            $password = $input['password'] ?? '';
            if (!is_string($password) || $password === '') return [409, [
                'success' => false, 'action' => 'link', 'email' => $identity['email'],
                'message' => 'Confirm your existing website password to link Google and keep your donation history.'
            ]];
            $stmt = $pdo->prepare('SELECT attempts, window_start > DATE_SUB(NOW(), INTERVAL 15 MINUTE) AS active FROM google_link_attempts WHERE user_id = ?');
            $stmt->execute([$user['id']]);
            $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($attempt && $attempt['active'] && (int) $attempt['attempts'] >= 5) return [429, [
                'success' => false, 'action' => 'link', 'email' => $identity['email'],
                'message' => 'Too many attempts. Wait 15 minutes before trying again.'
            ]];
            if (strlen($password) > 4096 || !password_verify($password, $user['password_hash'])) {
                $count = $attempt && $attempt['active'] ? (int) $attempt['attempts'] + 1 : 1;
                $stmt = $pdo->prepare('INSERT INTO google_link_attempts (user_id, attempts, window_start) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE attempts = VALUES(attempts), window_start = IF(VALUES(attempts) = 1, NOW(), window_start)');
                $stmt->execute([$user['id'], $count]);
                return [401, ['success' => false, 'action' => 'link', 'email' => $identity['email'], 'message' => 'Incorrect website password. Try again.']];
            }
            $pdo->beginTransaction();
        } else {
            $name = trim(is_string($input['fullName'] ?? null) ? $input['fullName'] : '');
            $idNumber = trim(is_string($input['idNumber'] ?? null) ? $input['idNumber'] : '');
            if ($name === '' || mb_strlen($name) > 100 || !preg_match('/^\d{13}$/D', $idNumber)) return [422, [
                'success' => false, 'action' => 'profile', 'email' => $identity['email'], 'displayName' => $identity['name'],
                'message' => 'Enter your full name (up to 100 characters) and 13-digit South African ID number.'
            ]];
            $stmt = $pdo->prepare('SELECT id FROM users WHERE sa_id = ? LIMIT 1');
            $stmt->execute([$idNumber]);
            if ($stmt->fetchColumn()) return [409, ['success' => false, 'action' => 'profile',
                'message' => 'This ID number belongs to an existing account. Sign in to that account and contact support to link the correct Google email.']];
            $pdo->beginTransaction();
            // A random unknown password prevents Google-only accounts accepting an empty password.
            $stmt = $pdo->prepare('INSERT INTO users (full_name, sa_id, username, email, password_hash) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $idNumber, 'google_' . bin2hex(random_bytes(16)), $identity['email'], password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT)]);
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$pdo->lastInsertId()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $stmt = $pdo->prepare('INSERT INTO google_identities (firebase_uid, user_id) VALUES (?, ?)');
        $stmt->execute([$identity['uid'], $user['id']]);
        $stmt = $pdo->prepare('DELETE FROM google_link_attempts WHERE user_id = ?');
        $stmt->execute([$user['id']]);
        $pdo->commit();
        return [200, ['success' => true, 'account' => $user]];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('homeofhope_google_identity')");
    }
}
