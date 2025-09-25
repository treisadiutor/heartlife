<!-- components/notes_recent.php -->
<div class="note-column">
    
    <div class="flex items-center gap-3 mb-6">
        <img src="<?= BASE_URL ?>/assets/images/notes/recent-note.svg" alt="Recent" class="w-8 h-8">
        <h3 class="font-dunkin text-lg text-ghost">Recently Added</h3>
    </div>
    
    <div class="space-y-4">
        <?php foreach($recentNotes as $note): ?>
        <div class="note-card" data-note-id="<?= $note['id'] ?>">
            <h4 class="font-dunkin text-primary text-lg truncate"><?= htmlspecialchars($note['title']) ?></h4>
            <p class="text-ghost/80 text-sm h-10 overflow-hidden"><?= htmlspecialchars($note['excerpt']) ?></p>
            <p class="text-accent text-xs text-right mt-2"><?= $note['date'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>  