const fs = require('fs');
const path = require('path');

const navPath = path.join(__dirname, 'resources', 'views', 'livewire', 'layout', 'navigation.blade.php');
const sidebarLinkPath = path.join(__dirname, 'resources', 'views', 'components', 'sidebar-link.blade.php');

function replaceToWhite(file) {
    let content = fs.readFileSync(file, 'utf8');

    // Currently we have:
    // in sidebar-link: text-blue-200/70
    // in navigation: text-blue-300/60, text-blue-200/70

    // For active/hover states, it's usually already text-white
    content = content.replace(/text-blue-200\/70/g, 'text-white');
    content = content.replace(/text-blue-300\/60/g, 'text-white');
    
    // Some others might be text-gray-200
    content = content.replace(/text-sm font-medium text-gray-200/g, 'text-sm font-medium text-white');

    fs.writeFileSync(file, content, 'utf8');
    console.log(`Updated to white font in ${file}`);
}

replaceToWhite(navPath);
replaceToWhite(sidebarLinkPath);
console.log('Finished text color replacements.');
