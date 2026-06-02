<?php
/**
 * Database migration script — re-runnable.
 * Run from XAMPP shell or browser to apply incremental schema changes.
 *
 * Browser: http://localhost/BMI/database/migrate.php
 * CLI:     c:\xampp\php\php.exe database\migrate.php
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/settings.php';

$inCli = PHP_SAPI === 'cli';
$say = static function (string $line) use ($inCli): void {
    echo $inCli ? $line . "\n" : $line . "<br>\n";
};

try {
    $pdo = db_connect();

    // 1. Add image columns if missing
    $eventCols = $pdo->query('DESCRIBE events')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('event_image', $eventCols, true)) {
        $pdo->exec('ALTER TABLE events ADD COLUMN event_image VARCHAR(255) DEFAULT NULL AFTER venue');
        $say('+ Added events.event_image');
    } else {
        $say('= events.event_image already present');
    }

    $sermonCols = $pdo->query('DESCRIBE sermons')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('sermon_image', $sermonCols, true)) {
        $pdo->exec('ALTER TABLE sermons ADD COLUMN sermon_image VARCHAR(255) DEFAULT NULL AFTER content');
        $say('+ Added sermons.sermon_image');
    } else {
        $say('= sermons.sermon_image already present');
    }

    $ministryCols = $pdo->query('DESCRIBE ministries')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('ministry_image', $ministryCols, true)) {
        $pdo->exec('ALTER TABLE ministries ADD COLUMN ministry_image VARCHAR(255) DEFAULT NULL AFTER description');
        $say('+ Added ministries.ministry_image');
    } else {
        $say('= ministries.ministry_image already present');
    }

    $postCols = $pdo->query('DESCRIBE posts')->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('post_image', $postCols, true)) {
        $pdo->exec('ALTER TABLE posts ADD COLUMN post_image VARCHAR(255) DEFAULT NULL AFTER content');
        $say('+ Added posts.post_image');
    } else {
        $say('= posts.post_image already present');
    }

    // 2. Create site_settings table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(80) NOT NULL UNIQUE,
            setting_value LONGTEXT,
            setting_group VARCHAR(40) DEFAULT 'general',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_settings_group (setting_group)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $say('= site_settings table ensured');

    // 2.1. Create hero_slides table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS hero_slides (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle TEXT,
            bg_image VARCHAR(500),
            button_text VARCHAR(100),
            button_url VARCHAR(255),
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $say('= hero_slides table ensured');

    // 2.2. Create testimonies table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS testimonies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author_name VARCHAR(100) NOT NULL,
            author_role VARCHAR(100),
            quote TEXT NOT NULL,
            image_url VARCHAR(500),
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $say('= testimonies table ensured');

    // 2.3. Create weekly_services table
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS weekly_services (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            subtitle VARCHAR(100),
            description TEXT,
            time_info VARCHAR(100),
            image_url VARCHAR(500),
            theme_color VARCHAR(20) DEFAULT 'cyan',
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
    $say('= weekly_services table ensured');

    // Seed hero slides if empty
    if ($pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO hero_slides (title, subtitle, bg_image, button_text, button_url, sort_order) VALUES 
            ('Impact the <span class=\"text-[#c49a45]\">World.</span>', 'Taking the uncompromised message of hope and truth to every nation. Join our global mandate.', 'assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg', 'Discover Ministries', 'ministries', 1),
            ('Encounter <span class=\"text-white\">His Presence.</span>', 'Experience powerful worship and authentic community. There is a place for you in our church family.', 'assets/image/aaron-burden-535Npq1wFG8-unsplash.jpg', 'Plan Your Visit', 'visit', 2),
            ('Uncompromised <span class=\"text-[#c49a45]\">Truth.</span>', 'Dive deep into the Word of God with our latest sermon series designed to build resilient faith.', 'https://images.unsplash.com/photo-1504052434569-70ad5836ab65?q=80&w=1200&auto=format&fit=crop', 'Watch Latest Sermon', 'sermons', 3)");
        $say('+ Seeded hero_slides');
    }

    // Seed testimonies if empty
    if ($pdo->query("SELECT COUNT(*) FROM testimonies")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO testimonies (author_name, author_role, quote, image_url, sort_order) VALUES 
            ('Sarah Mensah', 'Church Member', 'Since joining Bridge Ministries, my entire outlook on purpose and faith has completely shifted. The uncompromised teaching of the Word has anchored my family through our toughest seasons.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop', 1),
            ('David Osei', 'Men\'s Ministry', 'I walked in completely broken. The intense prayer culture and the genuine love from the leadership brought a profound restoration I never thought was possible in my life.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=200&auto=format&fit=crop', 2),
            ('Grace Appiah', 'Women\'s Leader', 'Finding a community that actively lives out the gospel has been the greatest blessing. My spiritual growth here has been truly unprecedented.', 'https://images.unsplash.com/photo-1531123897727-8f129e1bf98c?q=80&w=200&auto=format&fit=crop', 3)");
        $say('+ Seeded testimonies');
    }

    // Seed weekly services if empty
    if ($pdo->query("SELECT COUNT(*) FROM weekly_services")->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO weekly_services (title, subtitle, description, time_info, image_url, theme_color, sort_order) VALUES 
            ('RESTORERS', 'Celebration Service', 'Join us every Sunday morning for an explosive time of worship, profound teaching, and fellowship. Come expectant and leave restored.', 'Sunday 8:45 AM', 'assets/image/flyer_restorers.png', 'cyan', 1),
            ('REPAIRERS', 'Cell Meetings', 'Connect intimately with smaller groups within your community. Dive deeper into the Word and build lasting, accountable relationships.', 'Wednesdays', 'assets/image/flyer_repairers.png', 'purple', 2),
            ('SWITCH ON', 'Youth Service', 'A dynamic, high-energy service tailored for the next generation. We are raising young leaders grounded in uncompromised truth.', 'Sunday 8:45 AM', 'assets/image/flyer_switchon.png', 'orange', 3),
            ('BUILDERS', 'Friday Service', 'End your week in the presence of God with intense prayer and prophetic ministration. A time to build your spiritual capacity.', 'Fridays 7:00 PM', 'assets/image/flyer_builders.png', 'teal', 4)");
        $say('+ Seeded weekly_services');
    }

    // 3. Seed defaults (only inserts missing keys; never overwrites existing values)
    $defaults = [
        // Group: General
        ['site.name', 'Bridge Ministries International', 'general'],
        ['site.logo', '', 'general'],
        ['site.favicon', '', 'general'],
        ['site.tagline', 'Be Switched On.', 'general'],
        ['site.description', 'Bridge Ministries International is a Bible-believing church family in Accra, Ghana, helping people know Christ, grow in faith, and live on mission.', 'general'],
        ['site.founded_year', '2005', 'general'],

        // Home
        ['home.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop', 'home'],
        ['home.founder_title', 'A voice of restoration,<br>a builder of lives &<br><span class="text-[#c49a45]">a repairer of destinies.</span>', 'home'],
        ['home.founder_bio1', 'From the vibrant nation of Ghana in West Africa emerges Reverend Francis Duane Yalley, affectionately known as “The Repairer.” For over two decades, he has served as the General Overseer and founder of Bridge Ministries International, building not just a church, but a global movement of Switching On lives.', 'home'],
        ['home.founder_bio2', 'Rev. F.D. Yalley is more than a preacher. He is a restorer of broken lives. A builder of leaders. A voice of prophetic clarity.', 'home'],
        ['home.mission_title', 'A legacy of <span class="text-[#c49a45]">enduring<br>faith</span> and action.', 'home'],
        ['home.mission_text', 'Under the leadership of our General Overseer, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact on individuals and society.', 'home'],
        ['home.mission_box1_title', 'Missions & Outreach', 'home'],
        ['home.mission_box1_desc', 'Impacting communities across the globe through active missions and support.', 'home'],
        ['home.mission_box2_title', 'Evangelism', 'home'],
        ['home.mission_box2_desc', 'Spreading the message of uncompromised truth to every corner.', 'home'],
        ['home.mission_box3_title', 'Discipleship', 'home'],
        ['home.mission_box3_desc', 'Equipping believers to grow and walk boldly in their faith.', 'home'],
        ['home.marquee_text1', 'WORSHIP WITH US', 'home'],
        ['home.marquee_text2', 'SUNDAYS AT 8:45 AM', 'home'],
        ['home.marquee_text3', 'EXPERIENCE TRANSFORMATION', 'home'],
        ['home.marquee_text4', 'UNCOMPROMISED TRUTH', 'home'],
        ['home.marquee_text5', 'A LEGACY OF ENDURING FAITH', 'home'],

        // Contact
        ['contact.address', 'Behind Sel Fuel station, Anyaa-Awoshie Rd, Accra, Ghana', 'contact'],
        ['contact.phone_primary', '026 235 4383', 'contact'],
        ['contact.phone_secondary', '', 'contact'],
        ['contact.email_general', 'info@bmiglobal.org', 'contact'],
        ['contact.email_prayer', 'prayer@bmiglobal.org', 'contact'],
        ['contact.email_giving', 'giving@bmiglobal.org', 'contact'],
        ['contact.map_query', 'Anyaa-Awoshie Road, Accra, Ghana', 'contact'],

        // Service times
        ['service.sunday_worship', 'Sundays · 8:45 AM', 'service'],
        ['service.bible_study', 'Wednesdays · 6:00 PM', 'service'],
        ['service.prayer_service', 'Fridays · 5:30 PM', 'service'],
        ['service.notes', 'All are welcome. Childcare available during the Sunday service.', 'service'],

        // Social
        ['social.facebook', '', 'social'],
        ['social.instagram', '', 'social'],
        ['social.youtube', '', 'social'],
        ['social.x', '', 'social'],
        ['social.tiktok', '', 'social'],

        // Giving
        ['giving.bank_name', '', 'giving'],
        ['giving.bank_account_name', '', 'giving'],
        ['giving.bank_account_number', '', 'giving'],
        ['giving.bank_branch', '', 'giving'],
        ['giving.momo_mtn', '', 'giving'],
        ['giving.momo_vodafone', '', 'giving'],
        ['giving.momo_airteltigo', '', 'giving'],
        ['giving.paystack_public_key', '', 'giving'],
        ['giving.currency', 'GHS', 'giving'],

        // Livestream
        ['live.embed_url', '', 'live'],
        ['live.youtube_channel_url', '', 'live'],

        // Analytics
        ['analytics.plausible_domain', '', 'analytics'],

        // Page: Home
        ['home.hero_title', 'Glorify. Grow. Go.', 'page_home'],
        ['home.hero_subtitle', 'We are a Bible-believing family helping people know Christ, grow in faith, and live on mission.', 'page_home'],
        ['home.hero_bg_image', 'assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg', 'page_home'],
        
        ['home.founder_title', 'A voice of restoration,<br>a builder of lives &<br><span class="text-[#c49a45]">a repairer of destinies.</span>', 'page_home'],
        ['home.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop', 'page_home'],
        ['home.founder_bio1', 'From the vibrant nation of Ghana in West Africa emerges Reverend Francis Duane Yalley, affectionately known as “The Repairer.” For over two decades, he has served as the General Overseer and founder of Bridge Ministries International, building not just a church, but a global movement of Switching On lives.', 'page_home'],
        ['home.founder_bio2', 'Rev. F.D. Yalley is more than a preacher. He is a restorer of broken lives. A builder of leaders. A voice of prophetic clarity.', 'page_home'],
        
        ['home.mission_title', 'A legacy of <span class="text-[#c49a45]">enduring<br>faith</span> and action.', 'page_home'],
        ['home.mission_text', 'Under the leadership of our General Overseer, Bridge Ministries International operates with a profound commitment to establishing a lasting, positive impact on individuals and society.', 'page_home'],
        ['home.mission_bg', 'assets/image/chad-kirchoff-ivqGyYLtBI8-unsplash.jpg', 'page_home'],
        ['home.mission_box1_title', 'Missions & Outreach', 'page_home'],
        ['home.mission_box1_desc', 'Impacting communities across the globe through active missions and support.', 'page_home'],
        ['home.mission_box2_title', 'Sound Doctrine', 'page_home'],
        ['home.mission_box2_desc', 'Rooted firmly in biblical truth, theology, and the transformative power of the Word.', 'page_home'],
        
        ['home.marquee_text1', 'WORSHIP WITH US', 'page_home'],
        ['home.marquee_text2', 'SUNDAYS AT 8:45 AM', 'page_home'],
        ['home.marquee_text3', 'EXPERIENCE TRANSFORMATION', 'page_home'],
        ['home.marquee_text4', 'UNCOMPROMISED TRUTH', 'page_home'],
        ['home.marquee_text5', 'A LEGACY OF ENDURING FAITH', 'page_home'],
        
        ['home.counter1_number', '20', 'page_home'],
        ['home.counter1_label', 'Years of Ministry', 'page_home'],
        ['home.counter2_number', '10', 'page_home'],
        ['home.counter2_label', 'Lives Impacted', 'page_home'],

        ['home.watch_title', 'Sundays. <br><span class="text-[#c49a45]">Anywhere</span> in the world.', 'page_home'],
        ['home.watch_subtitle', 'Join us live each week or revisit recent messages. Wherever you are, the bridge reaches you.', 'page_home'],
        ['home.watch_bg', 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1200&auto=format&fit=crop', 'page_home'],
        // Page: About
        ['about.hero_bg_image', 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?q=80&w=1200&auto=format&fit=crop', 'page_about'],
        ['about.history_text1', 'Bridge Ministries International began as a small prayer gathering in 2005. A group of five families, hungry for a deeper move of God and uncompromised biblical teaching, began meeting in a living room. What started as a weekly Bible study quickly grew as the community experienced genuine transformation, healings, and a powerful sense of family.', 'page_about'],
        ['about.history_text2', 'By 2008, the fellowship had grown into a full-fledged church. We moved into our first rented facility, and the vision of building a global community of believers took root. Today, by God\'s grace, we minister to thousands across multiple branches, yet our core DNA remains the same: a deep love for God\'s Word, fervent prayer, and authentic community.', 'page_about'],
        ['about.vision_text', 'To see the nations transformed by the power of the Gospel. We envision a church where every member is equipped for ministry, where families are restored, and where the love of Christ overflows into our cities and beyond.', 'page_about'],
        ['about.founder_name', 'Rev. Dr. Emmanuel Mensah', 'page_about'],
        ['about.founder_bio1', 'Rev. Dr. Emmanuel Mensah is the visionary founder and Senior Pastor of Bridge Ministries International. With over two decades of pastoral experience, he is known for his dynamic, expository preaching and his deep passion for raising leaders who will impact their generation for Christ.', 'page_about'],
        ['about.founder_bio2', 'He holds a Doctorate in Ministry and has authored several books on faith, leadership, and family. Rev. Emmanuel is married to his college sweetheart, Grace, and they are blessed with three children. When he is not preaching or studying, he enjoys reading history and spending time with his family.', 'page_about'],
        ['about.founder_image', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=800&auto=format&fit=crop', 'page_about'],

        // Page: Visit
        ['visit.hero_title', 'Plan a Visit', 'page_visit'],
        ['visit.hero_subtitle', 'We can\'t wait to welcome you to Bridge Ministries International.', 'page_visit'],
        ['visit.hero_bg_image', 'assets/image/PXL_20240329_213926615.jpg', 'page_visit'],
        ['visit.expect_text', 'Visiting a new church can be intimidating, but we want you to feel completely at home. Our services are around 90 minutes long. They feature vibrant, contemporary worship, followed by a biblical, practical, and engaging message. Dress however you feel most comfortable — you’ll see everything from jeans and t-shirts to suits and ties.', 'page_visit'],
        ['visit.church_image', 'assets/image/church-building.jpg', 'page_visit'],

        // Page: Beliefs
        ['beliefs.hero_title', 'What We Believe', 'page_beliefs'],
        ['beliefs.hero_subtitle', 'Our statement of faith — what Bridge Ministries International believes about Scripture, God, salvation, the church, and the Christian life.', 'page_beliefs'],
        ['beliefs.intro_text', 'We are a Bible-believing, Christ-centred church standing in the historic stream of evangelical Christian faith. What follows is a summary of the core convictions that shape our preaching, our gatherings, and our life together.', 'page_beliefs'],
        ['beliefs.note_text', 'We don\'t see this statement as the last word — only the Bible is. Rather, we see it as a faithful summary of what we believe the Scriptures teach. We hold these truths with conviction, teach them with clarity, and welcome honest questions from anyone exploring faith.', 'page_beliefs'],

        // Page: Ministries
        ['ministries.hero_title', 'Our Ministries', 'page_ministries'],
        ['ministries.hero_subtitle', 'Find a place to grow, serve, and connect within the Bridge Ministries family.', 'page_ministries'],
        ['ministries.hero_bg_image', 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?q=80&w=1200&auto=format&fit=crop', 'page_ministries'],

        // Page: Sermons
        ['sermons.hero_title', 'Sermons & Teachings', 'page_sermons'],
        ['sermons.hero_subtitle', 'Explore our archive of expository preaching and topical teachings designed to build your faith.', 'page_sermons'],
        ['sermons.hero_bg_image', 'https://images.unsplash.com/photo-1543165365-07232ed12fad?q=80&w=1200&auto=format&fit=crop', 'page_sermons'],

        // Page: Events
        ['events.hero_title', 'Upcoming Events', 'page_events'],
        ['events.hero_subtitle', 'Stay connected with what is happening in the life of our church.', 'page_events'],
        ['events.hero_bg_image', 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=1200&auto=format&fit=crop', 'page_events'],

        // Page: Flagship Programs
        ['flagship.hero_title', 'Flagship Programs', 'page_flagship'],
        ['flagship.hero_subtitle', 'Our major annual conferences, summits, and special gatherings that define our calendar.', 'page_flagship'],
        ['flagship.hero_bg_image', 'https://images.unsplash.com/photo-1544365558-35aa4afc111c?q=80&w=2000&auto=format&fit=crop', 'page_flagship'],

        // Page: Contact
        ['contact.hero_title', 'Contact Us', 'page_contact'],
        ['contact.hero_subtitle', 'We would love to hear from you. Reach out with any questions, prayer requests, or feedback.', 'page_contact'],

        // Page: Livestream
        ['live.hero_title', 'Watch Live', 'page_live'],
        ['live.hero_subtitle', 'Join our Sunday worship and mid-week services from anywhere in the world.', 'page_live'],

        // Page: Donate
        ['donate.hero_title', 'Give Online', 'page_donate'],
        ['donate.hero_subtitle', 'Partner with us financially to advance the gospel and support the work of the ministry.', 'page_donate'],
    ];

    $insert = $pdo->prepare(
        'INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_group) VALUES (:k, :v, :g)'
    );
    $added = 0;
    foreach ($defaults as [$k, $v, $g]) {
        $insert->execute([':k' => $k, ':v' => $v, ':g' => $g]);
        if ($insert->rowCount() > 0) {
            $added++;
        }
    }
    $say('+ Seeded ' . $added . ' new default setting(s); existing values preserved.');

    $say('');
    $say('Migration complete.');
} catch (Throwable $e) {
    $say('ERROR: ' . $e->getMessage());
    exit(1);
}
