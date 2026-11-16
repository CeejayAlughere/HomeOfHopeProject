<?php
declare(strict_types=1);

/**
 * Return active causes with totals calculated from all recorded donations.
 */
function fetchActiveCauses(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT
            c.id,
            c.slug,
            c.title,
            c.description,
            c.image_url,
            c.goal_amount,
            c.display_order,
            COALESCE(SUM(d.amount), 0) AS raised_amount
         FROM causes c
         LEFT JOIN donations d ON d.cause_id = c.id
         WHERE c.is_active = 1
         GROUP BY
            c.id,
            c.slug,
            c.title,
            c.description,
            c.image_url,
            c.goal_amount,
            c.display_order
         ORDER BY c.display_order ASC, c.id ASC"
    );

    return array_map('normaliseCauseRow', $stmt->fetchAll(PDO::FETCH_ASSOC));
}

/**
 * Find one active cause. Raised totals include all recorded donations.
 */
function findActiveCauseById(PDO $pdo, int $causeId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT
            c.id,
            c.slug,
            c.title,
            c.description,
            c.image_url,
            c.goal_amount,
            c.display_order,
            COALESCE(SUM(d.amount), 0) AS raised_amount
         FROM causes c
         LEFT JOIN donations d ON d.cause_id = c.id
         WHERE c.id = ? AND c.is_active = 1
         GROUP BY
            c.id,
            c.slug,
            c.title,
            c.description,
            c.image_url,
            c.goal_amount,
            c.display_order
         LIMIT 1"
    );
    $stmt->execute([$causeId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? normaliseCauseRow($row) : null;
}

function normaliseCauseRow(array $row): array
{
    $goal = (float) $row['goal_amount'];
    $raised = (float) $row['raised_amount'];
    $progress = $goal > 0 ? min(100.0, ($raised / $goal) * 100) : 0.0;

    return [
        'id' => (int) $row['id'],
        'slug' => (string) $row['slug'],
        'title' => (string) $row['title'],
        'description' => (string) $row['description'],
        'image_url' => (string) $row['image_url'],
        'goal_amount' => $goal,
        'raised_amount' => $raised,
        'progress' => round($progress, 1),
        'display_order' => (int) $row['display_order']
    ];
}
