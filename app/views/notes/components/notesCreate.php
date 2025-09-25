<!-- components/notes_create.php -->
<div class="bg-black/20 p-6 rounded-lg mb-8">
    <h2 class="font-dunkin text-xl text-secondary mb-4">Create a New Note</h2>
    <form action="<?= BASE_URL ?>/notes/create" method="POST">
        <div class="mb-4">
            <label for="note_title" class="block font-dunkin text-ghost mb-1">Title</label>
            <input type="text" id="note_title" name="title" class="input-pixel" placeholder="Today's reflections...">
        </div>
        <div class="mb-4">
            <label for="note_content" class="block font-dunkin text-ghost mb-1">Content</label>
            <textarea id="note_content" name="content" class="input-pixel h-24" placeholder="Log your thoughts, feelings, or progress here..."></textarea>
        </div>
        <div class="text-right">
            <button type="submit" class="btn-pixel">[ Save Note ]</button>
        </div>
    </form>
</div>