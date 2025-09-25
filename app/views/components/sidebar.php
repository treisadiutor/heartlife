<!-- components/sidebar.php -->
<aside class="w-64 bg-accent/30 backdrop-blur-sm p-6 h-screen sticky top-0 flex flex-col justify-between">
    <div>
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/dashboard" class="flex items-center mb-6">
            <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="Heart Icon" class="w-10 h-10">
            <h1 class="font-dunkin text-2xl text-accent ml-2">HeartLife</h1>
        </a>
        <!-- Navigation Links -->
        <nav class="flex flex-col gap-4">
            <a href="notes" class="flex items-center gap-3 p-3 rounded-md text-ghost hover:bg-white/10 transition-colors">
            <img src="<?= BASE_URL ?>/assets/images/scButtons/myNotes.svg" alt="My Notes" class="w-32 h-32">            
            </a>
            <a href="reports" class="flex items-center gap-3 p-3 rounded-md text-ghost hover:bg-white/10 transition-colors">
            <img src="<?= BASE_URL ?>/assets/images/scButtons/myReport.svg" alt="My Report" class="w-32 h-32">
            </a>
            <a href="self-care" class="flex items-center gap-3 p-3 rounded-md text-ghost hover:bg-white/10 transition-colors">
            <img src="<?= BASE_URL ?>/assets/images/scButtons/selfCare.svg" alt="Self Care" class="w-32 h-32">
            </a>
            <a href="sleepTracker" class="flex items-center gap-3 p-3 rounded-md text-ghost hover:bg-white/10 transition-colors">
            <img src="<?= BASE_URL ?>/assets/images/scButtons/sleepTracker.svg" alt="Sleep Tracker" class="w-32 h-32">
            </a>
            <a href="profile" class="flex items-center gap-3 p-3 rounded-md text-ghost hover:bg-white/10 transition-colors">
                <img src="<?= BASE_URL ?>/assets/images/scButtons/profile.svg" alt="Profile" class="w-36 h-36">
            </a>
        </nav>
    </div>

    <div class="space-y-3">
        <button id="open-modal-btn" class="btn-pixel w-full">Log Mood</button>
        <a href="<?= BASE_URL ?>/logout" class="block w-full bg-red-500/20 hover:bg-red-500/30 border-2 border-red-500 text-red-400 hover:text-red-300 font-dunkin font-bold py-2 px-4 rounded-lg transition-colors text-center">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
    </div>
</aside>