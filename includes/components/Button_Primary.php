<?php
/**
 * Primary Button Component
 * 
 * @param array $args
 * - text (string)
 * - url (string)
 * - style (string) 'light' | 'dark' | 'accent'
 * - class (string) extra classes
 */
function render_button_primary($args = []) {
    $text = $args['text'] ?? 'Click Here';
    $url = $args['url'] ?? '#';
    $style = $args['style'] ?? 'light';
    $extra_class = $args['class'] ?? '';
    
    $base_class = 'inline-flex items-center justify-center px-10 py-4 font-sans font-bold uppercase tracking-widest-xl text-xs rounded-full transition-all duration-500 hover:scale-105';
    
    switch ($style) {
        case 'dark':
            $style_class = 'bg-obsidian-950 text-white border border-white/10 hover:bg-white hover:text-black hover:border-white hover:shadow-glow';
            break;
        case 'accent':
            $style_class = 'bg-accent text-obsidian-950 border border-accent hover:bg-white hover:text-black hover:border-white hover:shadow-glow';
            break;
        case 'light':
        default:
            $style_class = 'bg-white text-obsidian-950 border border-white hover:bg-accent hover:text-white hover:border-accent hover:shadow-glow';
            break;
    }
    
    $final_class = trim("$base_class $style_class $extra_class");
    ?>
    <a href="<?php echo htmlspecialchars($url); ?>" class="<?php echo $final_class; ?>">
        <span><?php echo htmlspecialchars($text); ?></span>
        <svg class="w-4 h-4 ml-3 opacity-70 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
    </a>
    <?php
}
