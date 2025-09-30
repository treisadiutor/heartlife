
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartLife - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg font-dunkin text-ghost relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">
<div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">

    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex-1 p-8 h-screen overflow-y-auto">

        <!-- User Info -->
        <div class="flex justify-end items-center mb-8">
            <span class="mr-4 text-ghost"><?= htmlspecialchars($userEmail) ?></span>
            <a href="<?= BASE_URL ?>/profile">
                <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="w-12 h-12 rounded-full border-2 border-accent">
            </a>
        </div>

        <!-- Flash Messages Container -->
        <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <!-- Mood Tracker Row -->
        <?php include __DIR__ . '/components/moodTrackerRow.php'; ?>
        
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <?php include __DIR__ . '/components/dailyInsight.php'; ?>
            </div>

            <div>
                <?php include __DIR__ . '/components/dailyReports.php'; ?>
            </div>
            
        </div>
    </main>
</div>


<?php
include __DIR__ . '/../moodTracker/moodLogModal.php';
?>

<script>
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
</script>