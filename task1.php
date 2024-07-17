<?php
/**
 * Script to find and display all files in the /datafiles folder with names consisting of numbers and letters
 * of the Latin alphabet, having the .ixt extension. The file names are displayed ordered by name.
 * 
 * Based on the files I have created in folder datafiles. we will get following output:
 * 
 * 123.ixt
 * 12index.ixt
 * abhi.ixt
 * index.ixt
 * sample.ixt
 * 
 */

// Define the directory to search in
$directory = __DIR__ . '/datafiles';

// Initialize an array to hold the matched file names
$matchedFiles = [];

// Create a regular expression pattern to match files with names consisting of numbers and letters of the Latin alphabet and having the .ixt extension
$pattern = '/^[a-zA-Z0-9]+\.ixt$/';

// Open the directory
if ($handle = opendir($directory)) {
    // Loop through the directory
    while (false !== ($file = readdir($handle))) {
        // Check if the file name matches the pattern
        if (preg_match($pattern, $file)) {
            // Add the matched file name to the array
            $matchedFiles[] = $file;
        }
    }
    // Close the directory
    closedir($handle);
}

// Sort the matched file names in ascending order
sort($matchedFiles);

// Display the matched file names
foreach ($matchedFiles as $file) {
    echo $file . PHP_EOL;
}

