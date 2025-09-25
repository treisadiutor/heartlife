<!-- components/notes_completed.php -->
<div class="note-column">
    
    <div class="flex items-center gap-3 mb-6">
        <img src="<?= BASE_URL ?>/assets/images/notes/completed-notes.svg" alt="Completed" class="w-8 h-8">
        <h3 class="font-dunkin text-lg text-ghost">Completed Notes</h3>
    </div>
    
    <div class="space-y-4">
        <?php foreach($completedNotes as $note): ?>
        <div class="note-card" data-note-id="<?= $note['id'] ?>">
            <h4 class="font-dunkin text-primary/70 text-lg truncate line-through"><?= htmlspecialchars($note['title']) ?></h4>
            <p class="text-ghost/60 text-sm h-10 overflow-hidden"><?= htmlspecialchars($note['excerpt']) ?></p>
            <p class="text-accent/70 text-xs text-right mt-2"><?= $note['date'] ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>