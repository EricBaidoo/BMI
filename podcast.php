<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/settings.php';

header('Content-Type: application/rss+xml; charset=utf-8');

$siteName = setting('site.name', 'Bridge Ministries International');
$siteDesc = setting('site.description', 'Sermons and teachings from Bridge Ministries International.');
$siteUrl  = rtrim(setting('site.url', 'https://bmiglobal.org'), '/');
$logoUrl  = $siteUrl . '/assets/images/logo.png'; // Assuming logo path

// Fetch all audio sermons
try {
    $pdo = db_connect();
    $sermons = $pdo->query(
        "SELECT id, title, speaker, topic, sermon_date, media_url, content, sermon_image 
         FROM sermons 
         WHERE media_type = 'audio' AND media_url != ''
         ORDER BY sermon_date DESC LIMIT 100"
    )->fetchAll();
} catch (Throwable $e) {
    $sermons = [];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
  <channel>
    <title><?php echo htmlspecialchars($siteName); ?> Podcast</title>
    <link><?php echo htmlspecialchars($siteUrl); ?></link>
    <language>en-us</language>
    <itunes:author><?php echo htmlspecialchars($siteName); ?></itunes:author>
    <itunes:summary><?php echo htmlspecialchars($siteDesc); ?></itunes:summary>
    <description><?php echo htmlspecialchars($siteDesc); ?></description>
    <itunes:owner>
      <itunes:name><?php echo htmlspecialchars($siteName); ?></itunes:name>
      <itunes:email><?php echo htmlspecialchars(setting('contact.email_general', 'info@bmiglobal.org')); ?></itunes:email>
    </itunes:owner>
    <itunes:explicit>No</itunes:explicit>
    <itunes:category text="Religion &amp; Spirituality">
      <itunes:category text="Christianity"/>
    </itunes:category>
    <itunes:image href="<?php echo htmlspecialchars($logoUrl); ?>"/>
    
    <?php foreach ($sermons as $sermon): 
        $sermonLink = $siteUrl . '/sermon.php?id=' . (int)$sermon['id'];
        $pubDate = date(DATE_RFC2822, strtotime((string)$sermon['sermon_date']));
        $audioUrl = (string)$sermon['media_url'];
        if (strpos($audioUrl, 'http') !== 0) {
            $audioUrl = $siteUrl . '/' . ltrim($audioUrl, '/');
        }
    ?>
    <item>
      <title><?php echo htmlspecialchars((string)$sermon['title']); ?></title>
      <itunes:author><?php echo htmlspecialchars((string)$sermon['speaker']); ?></itunes:author>
      <itunes:summary><?php echo htmlspecialchars(strip_tags((string)$sermon['content'])); ?></itunes:summary>
      <description><![CDATA[<?php echo nl2br(htmlspecialchars((string)$sermon['content'])); ?>]]></description>
      <enclosure url="<?php echo htmlspecialchars($audioUrl); ?>" type="audio/mpeg" length="1024"/>
      <guid isPermaLink="true"><?php echo htmlspecialchars($sermonLink); ?></guid>
      <pubDate><?php echo $pubDate; ?></pubDate>
      <itunes:duration>3600</itunes:duration>
      <itunes:explicit>No</itunes:explicit>
    </item>
    <?php endforeach; ?>
    
  </channel>
</rss>
