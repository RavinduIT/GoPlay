<?php

namespace App\Models;

use Core\BaseModel;

class LoginAttempt extends BaseModel
{
    protected string $table = 'login_attempts';

    const MAX_PER_EMAIL  = 5;
    const MAX_PER_IP     = 10;
    const WINDOW_MINUTES = 15;

    public function isLockedByEmail(string $email): bool
    {
        $row = $this->queryFirst(
            "SELECT COUNT(*) AS cnt FROM login_attempts
             WHERE email = ? AND success = 0
             AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$email, self::WINDOW_MINUTES]
        );
        return (int)($row['cnt'] ?? 0) >= self::MAX_PER_EMAIL;
    }

    public function isLockedByIp(string $ip): bool
    {
        $row = $this->queryFirst(
            "SELECT COUNT(*) AS cnt FROM login_attempts
             WHERE ip_address = ? AND success = 0
             AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$ip, self::WINDOW_MINUTES]
        );
        return (int)($row['cnt'] ?? 0) >= self::MAX_PER_IP;
    }

    public function record(string $email, string $ip, bool $success): void
    {
        $this->query(
            "INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)",
            [$email, $ip, (int)$success]
        );
    }

    // Returns seconds until the lockout window expires for this email+IP pair.
    public function getRetryAfterSeconds(string $email, string $ip): int
    {
        $row = $this->queryFirst(
            "SELECT MIN(attempted_at) AS oldest FROM login_attempts
             WHERE (email = ? OR ip_address = ?) AND success = 0
             AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)",
            [$email, $ip, self::WINDOW_MINUTES]
        );
        if (!$row || !$row['oldest']) {
            return self::WINDOW_MINUTES * 60;
        }
        return max(0, (strtotime($row['oldest']) + self::WINDOW_MINUTES * 60) - time());
    }
}
