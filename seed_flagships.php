<?php
require_once __DIR__ . '/includes/db.php';
try {
    $pdo = db_connect();

    // Remove existing flagship events
    $pdo->exec("DELETE FROM events WHERE event_type = 'flagship'");

    $stmt = $pdo->prepare("INSERT INTO events (title, slug, description, event_type, event_date, end_date, venue, event_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // 1. Annual Year Fast
    $stmt->execute([
        'Annual Year Fast',
        'annual-year-fast',
        'Join us for 21 days of fervent fasting and prayers as we seek divine guidance, clarity, and breakthroughs for the year ahead. Together, we embark on a spiritual journey to align our hearts and minds with God\'s purpose.',
        'flagship',
        '2026-01-01',
        '2026-01-21',
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 2. Singles and Married Conference
    $stmt->execute([
        'Singles and Married Conference',
        'singles-and-married-conference',
        'A dedicated time to nurture relationships, whether you\'re single or married. Engage in insightful sessions, workshops, and discussions to strengthen bonds and gain wisdom for thriving relationships.',
        'flagship',
        '2026-02-14',
        null,
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 3. Easter Convocation
    $stmt->execute([
        'Easter Convocation',
        'easter-convocation',
        'A momentous occasion to commemorate the sacrificial love of our Lord Jesus Christ. Join us in celebration, reflection, and gratitude for the ultimate sacrifice that brings us salvation and hope.',
        'flagship',
        '2026-04-03',
        '2026-04-05',
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1504052434569-70ad5836ab65?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 4. Revive Thy Works
    $stmt->execute([
        'Revive Thy Works',
        'revive-thy-works',
        'Gather with us to pray fervently, igniting renewed passion and dedication as we enter the second half of the year. It\'s a time of seeking divine intervention and aligning our efforts with God\'s purpose.',
        'flagship',
        '2026-06-15',
        null,
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 5. Fruits and Vegetable Fast
    $stmt->execute([
        'Fruits and Vegetable Fast',
        'fruits-and-vegetable-fast',
        'Nurture your body and spirit with a focus on health and wellness. Participate in a period dedicated to embracing a diet rich in fruits and vegetables, promoting physical well-being within our community.',
        'flagship',
        '2026-08-01',
        '2026-08-07',
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 6. CAN GOD? Annual Anniversary Celebration
    $stmt->execute([
        'CAN GOD? Annual Anniversary Celebration',
        'can-god-anniversary',
        'Celebrate another year of God\'s faithfulness and blessings upon BMI. Join us for a joyous occasion filled with gratitude, testimonies, and worship, commemorating our journey together.',
        'flagship',
        '2026-10-10',
        '2026-10-12',
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 7. Feast of Miracles
    $stmt->execute([
        'Feast of Miracles',
        'feast-of-miracles',
        'Every three months, come experience a night of miraculous encounters. It\'s a time of healing, restoration, and empowerment where destinies are aligned and lives are transformed.',
        'flagship',
        '2026-03-31',
        null,
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1504356611382-747683921786?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 8. Semper Fidelis
    $stmt->execute([
        'Semper Fidelis',
        'semper-fidelis',
        'Gather with us for an extraordinary evening of heartfelt gratitude and worship at Semper Fidelis, our annual Worship and Thanksgiving Service Music Night. Through soul-stirring melodies and harmonious praise, join our choir, guest artists, and musicians as we raise our voices in celebration of God\'s faithfulness. It\'s a night where music becomes the language of our gratitude, resonating with the depths of our spirits as we reflect, rejoice, and offer heartfelt thanks for the blessings received throughout the year. Save the date and be part of this unforgettable celebration of worship and thanksgiving through the power of music at Bridge Ministries International!',
        'flagship',
        '2026-11-20',
        null,
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1510511459019-5d25979bb959?q=80&w=2000&auto=format&fit=crop'
    ]);

    // 9. A Night of Repairing, Restoring and Building
    $stmt->execute([
        'A Night of Repairing, Restoring and Building',
        'cross-over-night',
        'Join us on the eve of December 31st as we bid farewell to the passing year and welcome the promise of a brand-new beginning! Our 31st Night Celebration is an unforgettable night of reflection, gratitude, and anticipation as we step into the future with faith and hope.',
        'flagship',
        '2026-12-31',
        null,
        'Bridge Ministries International',
        'https://images.unsplash.com/photo-1467810563316-b5476525c0f9?q=80&w=2000&auto=format&fit=crop'
    ]);

    echo "Successfully seeded flagship programs!";

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
