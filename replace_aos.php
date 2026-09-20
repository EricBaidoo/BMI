<?php
$files = ['index.php','about.php','sermons.php','sermon.php','events.php','event-detail.php','contact.php','donate.php','ministries.php','visit.php','beliefs.php','flagship-programs.php','livestream.php'];
foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        $o = $c;
        $c = str_replace('data-aos="fade-up"', 'class="reveal"', $c);
        $c = str_replace('data-aos="fade-right"', 'class="reveal-left"', $c);
        $c = str_replace('data-aos="fade-left"', 'class="reveal-right"', $c);
        $c = preg_replace('/ data-aos-delay="\d+"/', '', $c);
        
        // Also remove any rogue spaces inside class if needed, but this is fine.
        if ($c !== $o) {
            file_put_contents($f, $c);
            echo "Updated: $f\n";
        } else {
            echo "No change: $f\n";
        }
    }
}
echo "Done\n";

