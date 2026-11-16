-- Home of Hope: shared causes and cause-specific donations
-- Run this file once against the `homeofhope` database.
-- Existing donations remain unassigned (cause_id = NULL), so every cause starts at R0.

CREATE TABLE `causes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `slug` varchar(100) NOT NULL,
    `title` varchar(150) NOT NULL,
    `description` text NOT NULL,
    `image_url` varchar(500) NOT NULL,
    `goal_amount` decimal(12,2) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `display_order` int(11) NOT NULL DEFAULT 0,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_causes_slug` (`slug`),
    KEY `idx_causes_active_order` (`is_active`, `display_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `causes`
    (`slug`, `title`, `description`, `image_url`, `goal_amount`, `display_order`)
VALUES
    (
        'feed-a-hungry-child',
        'Feed A Hungry Child',
        'Thousands of children in our community go to school on an empty stomach. Help us provide daily nutritious meals to keep them focused and healthy. Our feeding program aims to reach over 500 children daily across three local schools.',
        'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070',
        10000.00,
        1
    ),
    (
        'clean-water-for-all',
        'Clean Water For All',
        'Many families still lack access to safe drinking water. Your donation brings us closer to installing clean water facilities in communities that need them most. We are currently working on a borehole project that will serve 200 households.',
        'https://images.unsplash.com/photo-1594708767771-a7502209ff51?q=80&w=2070',
        10000.00,
        2
    ),
    (
        'education-for-every-child',
        'Education For Every Child',
        'A lack of school supplies and resources should never stand between a child and their future. Help us equip learners with the tools they need to succeed. We provide stationery, textbooks, and school uniforms to underprivileged students.',
        'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=2073',
        20000.00,
        3
    ),
    (
        'safe-shelter-for-families',
        'Safe Shelter For Families',
        'No family should sleep without a roof over their heads. We are raising funds to expand our shelter facilities and offer more families a safe place to stay. Our goal is to add 5 new family units and a community counselling room.',
        'https://images.unsplash.com/photo-1460317442991-0ec209397118?q=80&w=2070',
        15000.00,
        4
    );

ALTER TABLE `donations`
    ADD COLUMN `cause_id` int(11) NULL AFTER `user_id`,
    ADD KEY `idx_donations_cause_status` (`cause_id`, `status`),
    ADD CONSTRAINT `fk_donations_cause`
        FOREIGN KEY (`cause_id`) REFERENCES `causes` (`id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
