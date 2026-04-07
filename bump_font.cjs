const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'resources', 'views', 'livewire', 'layout', 'navigation.blade.php');
let content = fs.readFileSync(file, 'utf8');

// The dropdown headers/parents and links all use `text-sm`.
// To avoid duplicating font-medium, first remove it if it accidentally exists next to text-sm
// Then add it.
content = content.replace(/text-sm font-medium/g, 'text-sm');
content = content.replace(/text-sm/g, 'text-sm font-medium');

fs.writeFileSync(file, content, 'utf8');
console.log('Updated font weights in navigation');
