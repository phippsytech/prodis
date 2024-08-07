import { readFile, writeFile, existsSync } from 'fs';
import { join } from 'path';

// Function to format the date in Sydney time
function formatDate(date) {
    const options = {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true,
        timeZone: 'Australia/Sydney'
    };
    return new Intl.DateTimeFormat('en-AU', options).format(date);
}
// Get the current timestamp and format it
const timestamp = formatDate(new Date());

// Path to the built index.html file
const indexPath = join(process.cwd(), 'dist', 'index.html');

// Check if the file exists
if (existsSync(indexPath)) {
    // Read the index.html file
    readFile(indexPath, 'utf8', (err, data) => {
        if (err) {
            console.error('Error reading index.html:', err);
            return;
        }

        // Check if the meta tag already exists
        if (data.includes('name="version"')) {
            console.log('Version meta tag already exists.');
            return;
        }

        // Insert the meta tag before the closing </head> tag
        const result = data.replace(
            '</head>',
            `  <meta name="version" content="${timestamp}">\n</head>`
        );

        // Write the modified content back to index.html
        writeFile(indexPath, result, 'utf8', err => {
            if (err) {
                console.error('Error writing to index.html:', err);
                return;
            }
            console.log('Version meta tag added successfully.');
        });
    });
} else {
    console.error('index.html file does not exist.');
}
