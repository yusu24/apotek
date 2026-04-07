const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, 'resources', 'views');

function findBladeFiles(dir, fileList = []) {
    const files = fs.readdirSync(dir);

    files.forEach(file => {
        if (fs.statSync(path.join(dir, file)).isDirectory()) {
            fileList = findBladeFiles(path.join(dir, file), fileList);
        } else if (file.endsWith('.blade.php')) {
            fileList.push(path.join(dir, file));
        }
    });

    return fileList;
}

const bladeFiles = findBladeFiles(directoryPath);

bladeFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');

    // Make sure we only target type="date" and replace it with x-date-picker. 
    // Example: <input type="date" wire:model.live="startDate" class="...">
    const regex = /<input\s+type=(["'])date\1([^>]*)>/gi;

    let modified = false;
    let newContent = content.replace(regex, (match, p1, attributes) => {
        modified = true;
        return `<x-date-picker${attributes}></x-date-picker>`;
    });

    if (modified) {
        fs.writeFileSync(file, newContent, 'utf8');
        console.log(`Replaced in: ${file}`);
    }
});

console.log("Done.");
