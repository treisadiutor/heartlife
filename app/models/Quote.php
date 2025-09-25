<?php
require_once __DIR__ . '/Model.php';

class Quote extends Model {
    /**
     * Fetches a random quote from the database.
     * @return array An associative array with 'quote' and 'author'.
     */
    public function getDailyQuote(): array {
        $stmt = $this->db->query("SELECT quote, author FROM quotes ORDER BY RAND() LIMIT 1");
        $quote = $stmt->fetch();

        return $quote ?: ['quote' => 'No quotes found in the database. Please add some!', 'author' => 'System'];
    }
}