<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartLife - Notes</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    
    <style>
        .note-card.selected {
            border-color: #f472b6; 
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(244, 114, 182, 0.5);
        }
    </style>

</head>

<body class="bg-darkbg font-dunkin text-ghost relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">
    
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex-1 p-8 h-screen overflow-y-auto">

        <!-- Top Bar -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <img src="<?= BASE_URL ?>/assets/images/normalButtons/report-progress.png" class="w-32 h-auto">
                <div class="flex items-center gap-2">
                    <button id="pin-btn" class="action-btn" title="Pin/Unpin Selected">
                        <img src="<?= BASE_URL ?>/assets/images/normalButtons/pin.png" class="w-32">
                    </button>
                    <button id="complete-btn" class="action-btn" title="Mark as Complete">
                        <img src="<?= BASE_URL ?>/assets/images/normalButtons/complete.svg" class="w-32">
                    </button>
                    <button id="delete-btn" class="action-btn" title="Delete Selected">
                        <img src="<?= BASE_URL ?>/assets/images/normalButtons/delete.svg" class="w-32">
                    </button>
                </div>
            </div>
            <div class="flex items-center">
                <span class="mr-4 text-ghost"><?= htmlspecialchars($data['userEmail']) ?></span>
                <a href="<?= BASE_URL ?>/profile">
                    <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="w-12 h-12 rounded-full border-2 border-accent">
                </a>
            </div>
        </div>

        <!-- Flash Messages Container -->
        <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <?php 
            include __DIR__ . '/components/notesCreate.php'; 
        ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <?php 
                $recentNotes = $data['recentNotes'];
                include __DIR__ . '/components/notesRecent.php'; 
            ?>

            <?php 
                $completedNotes = $data['completedNotes'];
                include __DIR__ . '/components/notesCompleted.php'; 
            ?>

            <?php 
                $pinnedNotes = $data['pinnedNotes'];
                include __DIR__ . '/components/notesPinned.php'; 
            ?>
        </div>
        
    </main>
</div>

<?php
include __DIR__ . '/../moodTracker/moodLogModal.php';
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const noteCards = document.querySelectorAll('.note-card');
    let selectedNoteIds = new Set();

    noteCards.forEach(card => {
        card.addEventListener('click', () => {
            const noteId = card.dataset.noteId;
            card.classList.toggle('selected');
            
            if (selectedNoteIds.has(noteId)) {
                selectedNoteIds.delete(noteId);
            } else {
                selectedNoteIds.add(noteId);
            }
        });
    });

    const handleAction = (status) => {
        const ids = Array.from(selectedNoteIds);
        if (ids.length === 0) {
            showFlashMessage('Please select one or more notes first.', 'warning');
            return;
        }

        const endpoint = status === 'delete' 
            ? '<?= BASE_URL ?>/notes/delete' 
            : '<?= BASE_URL ?>/notes/updateStatus';
        
        const body = status === 'delete'
            ? { noteIds: ids }
            : { noteIds: ids, status: status };

        if (status === 'delete' && !confirm('Are you sure you want to delete the selected notes?')) {
            return;
        }

        fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const actionMessages = {
                    'completed': 'Notes marked as complete!',
                    'delete': 'Notes deleted successfully!',
                    'pinned': 'Notes pinned successfully!',
                    'active': 'Notes unpinned successfully!'
                };
                showFlashMessage(actionMessages[status] || 'Action completed successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showFlashMessage('An error occurred. Please try again.', 'error');
            }
        });
    };

    document.getElementById('complete-btn').addEventListener('click', () => handleAction('completed'));
    document.getElementById('delete-btn').addEventListener('click', () => handleAction('delete'));
    
    document.getElementById('pin-btn').addEventListener('click', () => {
        const firstSelected = document.querySelector('.note-card.selected');
        if (!firstSelected) {
            showFlashMessage('Please select a note to pin or unpin.', 'warning');
            return;
        }
        const newStatus = firstSelected.closest('.note-column').querySelector('h3').textContent.includes('Pinned') 
            ? 'active' 
            : 'pinned';
        handleAction(newStatus);
    });

    function showFlashMessage(message, type = 'info') {
        const container = document.getElementById('flash-container');
        const messageId = 'flash-' + Date.now();
        
        const styles = {
            success: {
                bg: 'bg-green-500/90 border-green-400',
                text: 'text-green-100',
                icon: '<i class="fas fa-check-circle"></i>'
            },
            error: {
                bg: 'bg-red-500/90 border-red-400', 
                text: 'text-red-100',
                icon: '<i class="fas fa-times-circle"></i>'
            },
            warning: {
                bg: 'bg-yellow-500/90 border-yellow-400',
                text: 'text-yellow-100', 
                icon: '<i class="fas fa-exclamation-triangle"></i>'
            },
            info: {
                bg: 'bg-primary/90 border-secondary',
                text: 'text-ghost',
                icon: '<i class="fas fa-info-circle"></i>'
            }
        };
        
        const style = styles[type] || styles.info;
        
        const flashDiv = document.createElement('div');
        flashDiv.id = messageId;
        flashDiv.className = `${style.bg} ${style.text} px-6 py-4 rounded-lg border-2 shadow-lg backdrop-blur-sm transform translate-x-full transition-all duration-300 ease-out max-w-sm`;
        flashDiv.innerHTML = `
            <div class="flex items-center space-x-3">
                <span class="text-xl">${style.icon}</span>
                <div class="flex-1">
                    <p class="font-dunkin font-semibold">${message}</p>
                </div>
                <button onclick="removeFlashMessage('${messageId}')" class="${style.text} hover:opacity-70 ml-2 font-bold text-lg">
                    ×
                </button>
            </div>
        `;
        
        container.appendChild(flashDiv);
        
        setTimeout(() => {
            flashDiv.classList.remove('translate-x-full');
            flashDiv.classList.add('translate-x-0');
        }, 100);
        
        setTimeout(() => {
            removeFlashMessage(messageId);
        }, 4000);
    }
    
    function removeFlashMessage(messageId) {
        const message = document.getElementById(messageId);
        if (message) {
            message.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 300);
        }
    }

});
</script>

</body>
</html>