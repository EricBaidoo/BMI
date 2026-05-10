<?php
/**
 * Seed sample content into ministries, sermons, and posts tables.
 *
 * Idempotent: only inserts rows that don't already exist (matched by a unique field).
 *
 * Browser: http://localhost/BMI/database/seed_content.php
 * CLI:     c:\xampp\php\php.exe database\seed_content.php
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$inCli = PHP_SAPI === 'cli';
$say = static function (string $line) use ($inCli): void {
    echo $inCli ? $line . "\n" : $line . "<br>\n";
};

try {
    $pdo = db_connect();
    $added = ['ministries' => 0, 'sermons' => 0, 'posts' => 0];

    /* ---------- Ministries ---------- */
    $ministries = [
        [
            'name' => 'RESTORERS — Celebration Service',
            'description' => 'Our main Sunday gathering for the whole church family. A vibrant time of worship, prayer, biblical teaching, and community.',
            'leader_name' => 'Senior Pastoral Team',
            'meeting_schedule' => 'Sundays · 8:45 AM',
        ],
        [
            'name' => 'REPAIRERS — Cell Meetings',
            'description' => 'Mid-week home and small-group gatherings — where the church becomes a family. Pray, study Scripture, and grow together in smaller circles.',
            'leader_name' => 'Cell Coordinators',
            'meeting_schedule' => 'Wednesdays · Various Locations',
        ],
        [
            'name' => 'SWITCH ON — Youth Service',
            'description' => 'A high-energy, Christ-centred space for teenagers and young adults to encounter Jesus, build real friendships, and discover their purpose.',
            'leader_name' => 'Youth Pastor',
            'meeting_schedule' => 'Sundays · 8:45 AM',
        ],
        [
            'name' => 'BUILDERS — Leadership Meeting',
            'description' => 'A weekly meeting for ministry leaders, elders, and emerging leaders — for prayer, vision-casting, and equipping the team that builds the church.',
            'leader_name' => 'General Overseer',
            'meeting_schedule' => 'Fridays · 7:00 PM',
        ],
        [
            'name' => "Children's Ministry",
            'description' => 'Age-appropriate Bible teaching, worship, and activities that help children encounter Jesus and grow in faith joyfully — in a safe, fun environment.',
            'leader_name' => "Children's Ministry Lead",
            'meeting_schedule' => 'Sundays during service',
        ],
        [
            'name' => "Women's Ministry",
            'description' => 'Encouraging women through Bible study, mentorship, prayer, and practical support — at every season of life.',
            'leader_name' => "Women's Ministry Lead",
            'meeting_schedule' => 'Monthly · Last Saturday',
        ],
        [
            'name' => "Men's Ministry",
            'description' => 'Equipping men to lead with integrity at home, at work, in the church, and in the community — anchored in the Word.',
            'leader_name' => "Men's Ministry Lead",
            'meeting_schedule' => 'Monthly · First Saturday',
        ],
        [
            'name' => 'Marriage & Family',
            'description' => 'Strengthening marriages and families with biblical wisdom, mentoring couples, and walking alongside parents and singles preparing for marriage.',
            'leader_name' => 'Family Ministry Coordinator',
            'meeting_schedule' => 'Quarterly seminars · Pastoral counselling on request',
        ],
        [
            'name' => 'Worship & Music',
            'description' => 'The team that leads the church into the presence of God each week — vocalists, instrumentalists, and tech serving with excellence and humility.',
            'leader_name' => 'Worship Director',
            'meeting_schedule' => 'Saturday rehearsals · Sunday services',
        ],
        [
            'name' => 'Prayer Ministry',
            'description' => 'The engine room of the church. Intercessors covering services, leaders, families, and the city in strategic, persistent prayer.',
            'leader_name' => 'Prayer Coordinator',
            'meeting_schedule' => 'Tuesdays · 6:00 PM',
        ],
        [
            'name' => 'Outreach & Missions',
            'description' => 'Carrying the love of Christ beyond our walls — community service, evangelism, and supporting missionaries reaching unreached people.',
            'leader_name' => 'Missions Coordinator',
            'meeting_schedule' => 'Monthly outreach Saturdays',
        ],
        [
            'name' => 'Hospitality & Ushering',
            'description' => 'The first faces guests meet on Sunday — welcoming, seating, and serving the church family with warmth and order.',
            'leader_name' => 'Head Usher',
            'meeting_schedule' => 'Sundays · Before service',
        ],
    ];

    $checkM = $pdo->prepare('SELECT COUNT(*) FROM ministries WHERE name = :n');
    $insM = $pdo->prepare('INSERT INTO ministries (name, description, leader_name, meeting_schedule) VALUES (:n, :d, :l, :s)');
    foreach ($ministries as $m) {
        $checkM->execute([':n' => $m['name']]);
        if ((int) $checkM->fetchColumn() > 0) {
            continue;
        }
        $insM->execute([':n' => $m['name'], ':d' => $m['description'], ':l' => $m['leader_name'], ':s' => $m['meeting_schedule']]);
        $added['ministries']++;
    }
    $say("+ ministries: {$added['ministries']} new (existing rows skipped)");

    /* ---------- Sermons ---------- */
    $today = new DateTimeImmutable('today');
    $sermons = [
        [
            'title' => 'Glorify, Grow, Go: A Bridge Generation',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Vision Series',
            'date' => $today->modify('-7 days')->format('Y-m-d'),
            'media_type' => 'video',
            'content' => 'God is raising a generation of believers who refuse to live small. This message unpacks the threefold call of every disciple — to glorify God, grow in Christ, and go on mission to the world.',
        ],
        [
            'title' => 'The Power of a Praying Church',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Prayer',
            'date' => $today->modify('-14 days')->format('Y-m-d'),
            'media_type' => 'audio',
            'content' => 'Acts 2 shows us that the early church was a praying church before it was a preaching church. We unpack what it looks like to be a community whose first instinct is prayer, not panic.',
        ],
        [
            'title' => 'Faith That Moves Mountains',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Faith',
            'date' => $today->modify('-21 days')->format('Y-m-d'),
            'media_type' => 'video',
            'content' => "Jesus didn't say faith would remove every mountain — He said it would move them. What is the difference between presumption and faith? And how do we live with the kind of faith Jesus actually commends?",
        ],
        [
            'title' => 'When God Says Wait',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Discipleship',
            'date' => $today->modify('-28 days')->format('Y-m-d'),
            'media_type' => 'audio',
            'content' => 'Few things test our faith like waiting. From Joseph in the prison to David before the throne, Scripture shows us how God shapes us in the seasons of delay.',
        ],
        [
            'title' => 'Bridges, Not Walls',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Reconciliation',
            'date' => $today->modify('-35 days')->format('Y-m-d'),
            'media_type' => 'video',
            'content' => "The Gospel tears down dividing walls and builds bridges where there once was hostility. A timely word on what it means to be a bridge-building community in a fragmented world.",
        ],
        [
            'title' => 'The Joy of Generosity',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Stewardship',
            'date' => $today->modify('-42 days')->format('Y-m-d'),
            'media_type' => 'audio',
            'content' => "The Bible doesn't shy away from money — it speaks about it more than almost anything else. This message reframes giving as worship, not duty.",
        ],
        [
            'title' => 'Living on Mission',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Mission',
            'date' => $today->modify('-49 days')->format('Y-m-d'),
            'media_type' => 'video',
            'content' => 'Every Christian is sent. Whether you go across the street or across the ocean, the Great Commission is for every disciple of Jesus — including you.',
        ],
        [
            'title' => 'The Bridge We Stand On',
            'speaker' => 'Rev. Francis Duane Yalley',
            'topic' => 'Gospel',
            'date' => $today->modify('-56 days')->format('Y-m-d'),
            'media_type' => 'text',
            'content' => 'Jesus is the only bridge between a holy God and broken humanity. A Gospel-centred message that returns us to the heart of the faith — the cross and resurrection of Christ.',
        ],
    ];

    $checkS = $pdo->prepare('SELECT COUNT(*) FROM sermons WHERE title = :t AND sermon_date = :d');
    $insS = $pdo->prepare(
        'INSERT INTO sermons (title, speaker, sermon_date, topic, media_type, media_url, content)
         VALUES (:t, :sp, :d, :tp, :mt, :mu, :c)'
    );
    foreach ($sermons as $s) {
        $checkS->execute([':t' => $s['title'], ':d' => $s['date']]);
        if ((int) $checkS->fetchColumn() > 0) {
            continue;
        }
        $insS->execute([
            ':t' => $s['title'],
            ':sp' => $s['speaker'],
            ':d' => $s['date'],
            ':tp' => $s['topic'],
            ':mt' => $s['media_type'],
            ':mu' => null,
            ':c' => $s['content'],
        ]);
        $added['sermons']++;
    }
    $say("+ sermons: {$added['sermons']} new");

    /* ---------- Blog Posts ---------- */
    $posts = [
        [
            'title' => 'Welcome to BMI: A Word from the General Overseer',
            'category' => 'announcement',
            'days_ago' => 2,
            'content' => "It is my joy to welcome you to Bridge Ministries International. Whether you are a long-standing member, a curious visitor, or someone exploring faith for the very first time — you are welcome here.\n\nWe are a church family with a simple calling: to glorify God, to grow as disciples, and to go on mission to our world. Everything we do is built on that foundation.\n\nIf you are new, take a few minutes to look around our website. Read about who we are, watch a recent message, and plan a visit. We have saved you a seat.\n\nIn Christ,\nRev. Francis Duane Yalley\nGeneral Overseer",
        ],
        [
            'title' => 'Walking in Grace: A Mid-Week Devotional',
            'category' => 'devotional',
            'days_ago' => 5,
            'content' => "\"My grace is sufficient for you, for my power is made perfect in weakness.\" — 2 Corinthians 12:9\n\nWe often think of grace as the door we walked through to be saved. And it is. But grace is also the path we walk on every day after.\n\nThe same grace that forgave you yesterday is the grace that strengthens you today. The same grace that justified you is the grace sanctifying you. There is no spiritual battle, no parenting struggle, no quiet sorrow that exceeds the supply of His grace.\n\nToday — whatever you are walking into — walk in grace. You don't need to perform for God. You don't need to earn what He has already given. Receive it, and let it carry you.\n\nA simple prayer: Lord, give me eyes to see Your grace in this day, and strength to extend that same grace to those I meet.",
        ],
        [
            'title' => 'The Bridge We Stand On',
            'category' => 'devotional',
            'days_ago' => 9,
            'content' => "There is a reason this church is called Bridge Ministries International — and it is not about the building.\n\nThe central truth of the Christian faith is that Jesus Christ is the bridge. He is the bridge between a holy God and a broken humanity. He bridges the gap our sin created. He bridges the distance between heaven and earth. He bridges the divide between people groups, languages, and nations.\n\n\"For there is one God, and one mediator between God and men, the Man Christ Jesus.\" — 1 Timothy 2:5\n\nWhen the world is being torn apart by walls — political, racial, generational — the church gets to model a different way. Because we stand on a bridge, we can build bridges. We can be people of reconciliation, of welcome, of healing.\n\nThat is who we are at BMI. And we want you to be part of it.",
        ],
        [
            'title' => 'Found at the Bridge: A Testimony',
            'category' => 'blog',
            'days_ago' => 14,
            'content' => "I came to BMI for the first time on a Sunday I almost didn't make it out of bed.\n\nLife had become loud. Work was draining. Relationships felt heavy. I had stopped going to church for nearly a year, telling myself I just needed a break. But the silence had become its own weight.\n\nA friend invited me — \"just one Sunday,\" she said. I came late. I sat at the back. I almost left during the worship.\n\nBut someone smiled at me. A pastor shook my hand and remembered my name. The message wasn't fancy — but it was true. And by the end of the service I was crying in a way I hadn't cried in years. Not sad tears. Found tears.\n\nThat was three years ago. I have been part of this family ever since. I serve in the welcome team now — partly because of that smile that found me when I was hiding.\n\nIf you are reading this and you almost didn't make it out of bed today — there is a seat at the bridge with your name on it.",
        ],
    ];

    $checkP = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE slug = :s');
    $insP = $pdo->prepare(
        'INSERT INTO posts (title, slug, content, category, published_at) VALUES (:t, :s, :c, :cat, :p)'
    );
    foreach ($posts as $p) {
        $slug = slugify($p['title']);
        $checkP->execute([':s' => $slug]);
        if ((int) $checkP->fetchColumn() > 0) {
            continue;
        }
        $publishedAt = (new DateTimeImmutable('now'))->modify('-' . (int) $p['days_ago'] . ' days')->format('Y-m-d H:i:s');
        $insP->execute([
            ':t' => $p['title'],
            ':s' => $slug,
            ':c' => $p['content'],
            ':cat' => $p['category'],
            ':p' => $publishedAt,
        ]);
        $added['posts']++;
    }
    $say("+ posts: {$added['posts']} new");

    /* ---------- Events ---------- */
    $today = new DateTimeImmutable('today');
    $eventSeed = [
        [
            'title' => 'Sunday Celebration Service',
            'description' => "Our weekly celebration service. Come and worship with the BMI family.",
            'days_ahead' => 7,
            'time' => '08:45',
            'venue' => 'BMI Sanctuary, Anyaa-Awoshie',
        ],
        [
            'title' => 'Bridge Generation: Youth Conference',
            'description' => "A two-day youth conference for teens and young adults. Worship, teaching, and breakout sessions on identity, calling, and purpose.",
            'days_ahead' => 14,
            'time' => '17:00',
            'venue' => 'BMI Main Auditorium',
        ],
        [
            'title' => 'Marriage & Family Seminar',
            'description' => "A practical, biblical seminar for married couples and those preparing for marriage. Light refreshments included.",
            'days_ahead' => 21,
            'time' => '10:00',
            'venue' => 'BMI Hall B',
        ],
        [
            'title' => 'Community Outreach Day',
            'description' => "We are taking the love of Christ to our neighbourhood — food distribution, prayer tents, and free community services.",
            'days_ahead' => 28,
            'time' => '09:00',
            'venue' => 'Anyaa-Awoshie Community',
        ],
        [
            'title' => "Women's Fellowship: Strength & Grace",
            'description' => "A monthly gathering for women of all ages — worship, the Word, and warm fellowship over breakfast.",
            'days_ahead' => 35,
            'time' => '08:00',
            'venue' => 'BMI Hall A',
        ],
        [
            'title' => 'BMI Anniversary Celebration',
            'description' => "Celebrating another year of God\'s faithfulness. Special guests, music, and a powerful message.",
            'days_ahead' => 49,
            'time' => '08:45',
            'venue' => 'BMI Sanctuary, Anyaa-Awoshie',
        ],
    ];

    $checkE = $pdo->prepare('SELECT COUNT(*) FROM events WHERE title = :t AND event_date = :d');
    $insE = $pdo->prepare(
        'INSERT INTO events (title, description, event_date, event_time, venue) VALUES (:t, :des, :d, :tm, :v)'
    );
    $added['events'] = 0;
    foreach ($eventSeed as $e) {
        $date = $today->modify('+' . (int) $e['days_ahead'] . ' days')->format('Y-m-d');
        $checkE->execute([':t' => $e['title'], ':d' => $date]);
        if ((int) $checkE->fetchColumn() > 0) {
            continue;
        }
        $insE->execute([
            ':t' => $e['title'],
            ':des' => $e['description'],
            ':d' => $date,
            ':tm' => $e['time'],
            ':v' => $e['venue'],
        ]);
        $added['events']++;
    }
    $say("+ events: {$added['events']} new");

    $say('');
    $say('Seed complete.');
} catch (Throwable $e) {
    $say('ERROR: ' . $e->getMessage());
    exit(1);
}
