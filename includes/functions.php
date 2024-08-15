<?php
include 'db.php';

function createSlug($string)
{
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return $slug;
}

function isArticleExists($title, $categoryId)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM articles WHERE title = ? AND category_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('si', $title, $categoryId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

function fetchRSS($url, $categoryId)
{
    global $conn;
    $rss = @simplexml_load_file($url);
    if ($rss === false) {
        die("Error: Unable to load RSS feed from $url");
    }

    foreach ($rss->channel->item as $item) {
        $title = (string) $item->title;
        $slug = createSlug($title);
        $description = (string) $item->description;
        $content = isset($item->children('content', true)->encoded) ? (string) $item->children('content', true)->encoded : $description;
        $image = '';

        // Cek jika ada tag enclosure
        if (isset($item->enclosure['url'])) {
            $image = (string) $item->enclosure['url'];
        } else {
            // Cek jika ada gambar dalam konten dengan berbagai format tag img
            $matches = [];
            preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches);
            if (isset($matches[1])) {
                $image = $matches[1];
            }
        }

        // Tambahkan lebih banyak strategi pencarian gambar
        if (empty($image) && isset($item->{'media:thumbnail'})) {
            $image = (string) $item->{'media:thumbnail'}->attributes()->url;
        }
        if (empty($image) && isset($item->{'media:content'})) {
            $attributes = $item->{'media:content'}->attributes();
            if (isset($attributes->url)) {
                $image = (string) $attributes->url;
            }
        }
        if (empty($image) && isset($item->image)) {
            $image = (string) $item->image;
        }

        // Ambil tanggal publikasi
        $pubDate = date('Y-m-d H:i:s', strtotime((string) $item->pubDate));

        $link = (string) $item->link;
        $excerpt = substr(strip_tags($content), 0, 100) . '...';

        // Cek apakah artikel sudah ada
        if (!isArticleExists($title, $categoryId)) {
            // Memasukkan artikel ke dalam database jika belum ada
            $stmt = $conn->prepare("INSERT INTO articles (title, slug, content, image, link, excerpt, category_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ssssssis", $title, $slug, $content, $image, $link, $excerpt, $categoryId, $pubDate);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function deleteRSSSource($id)
{
    global $conn;
    // Hapus artikel yang terkait dengan category_id
    $stmt = $conn->prepare("DELETE FROM articles WHERE category_id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Hapus sumber RSS
    $stmt = $conn->prepare("DELETE FROM rss_sources WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>
