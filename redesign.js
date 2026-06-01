const fs = require('fs');
const path = require('path');

const dir = 'c:/xampp/htdocs/BMI';
const files = [
  'about.php', 'beliefs.php', 'blog.php', 'contact.php', 
  'donate.php', 'event-detail.php', 'events.php', 
  'flagship-programs.php', 'livestream.php', 'ministries.php', 
  'sermons.php', 'visit.php', 'index.php'
];

files.forEach(filename => {
  const filepath = path.join(dir, filename);
  if (!fs.existsSync(filepath)) return;
  
  let content = fs.readFileSync(filepath, 'utf8');
  let original = content;

  // 1. Expand Widths
  content = content.replace(/max-w-7xl\s+mx-auto\s+px-4\s+sm:px-6\s+lg:px-8/g, 'w-[90%] max-w-[112.5rem] mx-auto');
  content = content.replace(/max-w-7xl\s+mx-auto/g, 'w-[90%] max-w-[112.5rem] mx-auto');

  // 2. Remove Rounded Corners (preserve rounded-full)
  content = content.replace(/\brounded(?:-sm|-md|-lg|-xl|-2xl|-3xl)?\b/g, '');

  // 3. Convert px to rem in arbitrary tailwind classes e.g. text-[14px]
  content = content.replace(/\[(\d+(?:\.\d+)?)px\]/g, (match, pxValue) => {
    const remValue = parseFloat(pxValue) / 16;
    return '[' + remValue + 'rem]';
  });

  if (content !== original) {
    fs.writeFileSync(filepath, content, 'utf8');
    console.log('Updated: ' + filename);
  }
});
console.log('Done processing pages.');
