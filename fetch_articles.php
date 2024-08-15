<?php
include 'includes/db.php';
include 'includes/functions.php';

// Ambil semua sumber RSS dari database
$sources = $conn->query("SELECT * FROM rss_sources");

while ($row = $sources->fetch_assoc()) {
    fetchRSS($row['url'], $row['id']);
}

echo "RSS feeds fetched successfully.";
?>
