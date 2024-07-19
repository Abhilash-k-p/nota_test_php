<?php

/**
 * Script to download the Wikipedia homepage, extract headings, abstracts, pictures, and links
 * from the page sections, and save the data into the wiki_sections table in a MySQL database.
 *
 * use setup_database.sql file to setup database and table
 *
 */

$servername = "host.docker.internal:33057"; // I am using docker
$username = "root";
$password = "*****";
$dbname = "nota_test_task2";

$wikiUrl = "https://www.wikipedia.org/";

/**
 * Downloads the content of a webpage.
 *
 * @param string $url The URL of the page to download.
 * @return string The HTML content of the page.
 */
function downloadPage($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

/**
 * Parses the HTML content and extracts required data.
 *
 * @param string $html The HTML content of the page.
 * @return array An associative array containing extracted data.
 */
function parsePage($html) {
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $data = [];

    // Extract titles
    foreach ($xpath->query('//span[contains(@class, "other-project-title")]') as $title) {
        $data['title'][] = trim($title->textContent);
    }

    // Extract abstracts lines
    foreach ($xpath->query('//span[contains(@class, "other-project-tagline")]') as $paragraph) {
        $data['abstract'][] = trim($paragraph->textContent);
    }

    // Extract URLs for other projects
    foreach ($xpath->query('//a[contains(@class, "other-project-link")]') as $link) {
        $href = $link->getAttribute('href');
        if (!empty($href)) {
            $data['url'][] = "https:" . $href;
        } else {
            $data['url'][] = '';
        }
    }

    return $data;
}

/**
 * Saves the extracted data to the database.
 *
 * @param PDO $conn The PDO connection object.
 * @param array $data The data to be saved.
 * @return void
 */
function saveToDatabase($conn, $data) {
    $sql = "INSERT INTO wiki_sections (title, url, picture, abstract) VALUES (:title, :url, :picture, :abstract)";
    $stmt = $conn->prepare($sql);

    foreach ($data['title'] as $key => $title) {
        $url = $data['url'][$key] ?? '';
        // since all icons and images are supplied by one svg file, going to make it as static link here
        $picture = "https://www.wikipedia.org/portal/wikipedia.org/assets/img/sprite-de847d1a.svg";
        $abstract = $data['abstract'][$key] ?? '';

        /**
         * To avoid sql erro use max value field can accept
         */
        $title = substr($title, 0, 230);
        $url = substr($url, 0, 240);
        $picture = substr($picture, 0, 240);
        $abstract = substr($abstract, 0, 256);

        $stmt->execute([
            ':title' => $title,
            ':url' => $url,
            ':picture' => $picture,
            ':abstract' => $abstract,
        ]);
    }
}

// Main script
try {
    /**
     * Establishes a database connection using PDO.
     */
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /**
     * Downloads and parses the Wikipedia page, then saves the extracted data to the database.
     */
    $html = downloadPage($wikiUrl);
    $data = parsePage($html);
    saveToDatabase($conn, $data);

    echo "Data has been saved to the database.";
} catch (PDOException $e) {
    echo "Error occurred - " . $e->getMessage();
}