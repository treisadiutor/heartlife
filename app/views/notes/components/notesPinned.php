<!-- components/notes_pinned.php -->
<div class="note-column">
    
    <div class="flex items-center gap-3 mb-6">
        <img src="<?= BASE_URL ?>/assets/images/notes/pin-notes.svg" alt="Pinned" class="w-8 h-8">
        <h3 class="font-dunkin text-lg text-ghost">Pinned Notes</h3>
    </div>
    
    <div class="space-y-4">
        <?php foreach($pinnedNotes as $note): ?>
        <div class="note-card" data-note-id="<?= $note['id'] ?>">
            <h4 class="font-dunkin text-primary text-lg truncate"><?= htmlspecialchars($note['title']) ?></h4>
            <p class="text-ghost/80 text-sm h-10 overflow-hidden"><?= htmlspecialchars($note['excerpt']) ?></p>
            <p class="text-accent text-xs text-right mt-2"><?= $note['date'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>