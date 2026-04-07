const fs = require('fs');
const path = require('path');

const navPath = path.join(__dirname, 'resources', 'views', 'livewire', 'layout', 'navigation.blade.php');
const sidebarLinkPath = path.join(__dirname, 'resources', 'views', 'components', 'sidebar-link.blade.php');

function replaceColors(file) {
    let content = fs.readFileSync(file, 'utf8');

    // Replace backgrounds
    content = content.replace(/bg-gray-900/g, 'bg-blue-950');
    content = content.replace(/bg-gray-950\/50/g, 'bg-blue-900/50');
    content = content.replace(/bg-gray-950\/30/g, 'bg-blue-900/30');
    content = content.replace(/bg-gray-800/g, 'bg-blue-800');
    
    // Replace borders
    content = content.replace(/border-gray-800/g, 'border-blue-800/50');
    
    // Replace text colors
    content = content.replace(/text-gray-400/g, 'text-blue-200/70');
    content = content.replace(/text-gray-500/g, 'text-blue-300/60');
    
    // Replace hover states
    // These might have been caught by the bg-gray-800 replacement above.
    
    fs.writeFileSync(file, content, 'utf8');
    console.log(`Updated ${file}`);
}

replaceColors(navPath);
replaceColors(sidebarLinkPath);
console.log('Finished color replacements.');
