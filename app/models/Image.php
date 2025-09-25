<?php
require_once __DIR__ . '/Model.php';

class Image extends Model {
    /**
     * Fetches a random inspirational image from the database.
     * @return array An associative array with 'url', 'alt_text', and 'category'.
     */
    public function getDailyImage(): array {
        $stmt = $this->db->query("SELECT url, alt_text, category FROM images WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
        $image = $stmt->fetch();

        return $image ?: [
            'url' => 'https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            'alt_text' => 'Random inspirational photo',
            'category' => 'default'
        ];
    }

    /**
     * Get all active images
     * @return array
     */
    public function getAllActiveImages(): array {
        $stmt = $this->db->query("SELECT * FROM images WHERE is_active = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    /**
     * Add a new image to the database
     * @param string $url
     * @param string $alt_text
     * @param string $category
     * @return bool
     */
    public function addImage(string $url, string $alt_text, string $category = 'inspirational'): bool {
        $stmt = $this->db->prepare("
            INSERT INTO images (url, alt_text, category, is_active, created_at) 
            VALUES (?, ?, ?, 1, NOW())
        ");
        return $stmt->execute([$url, $alt_text, $category]);
    }
}