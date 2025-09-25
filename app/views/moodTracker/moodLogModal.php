<div id="mood-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 p-4">

    <div class="bg-darkbg rounded-lg border-2 border-primary/30 shadow-2xl w-full max-w-5xl relative 
                flex flex-col h-[90vh]">
        
        <button id="close-modal-btn" class="absolute -top-3 -right-3 bg-tertiary rounded-full p-1 shadow-lg hover:scale-110 transition-transform">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div id="modal-spinner" class="hidden absolute inset-0 bg-darkbg/80 flex justify-center items-center z-10">
            <svg class="animate-spin h-10 w-10 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div class="flex flex-col h-full p-6 overflow-hidden">
            
            <div class="flex flex-col sm:flex-row justify-between items-center border-b-2 border-primary/20 pb-4 mb-4">
                <div class="text-center sm:text-left">
                    <h2 class="font-dunkin text-xl text-ghost">Hello, <span class="text-secondary"><?= htmlspecialchars($username) ?></span>!</h2>           
                </div>
                
                <div class="flex items-center gap-4 mt-4 sm:mt-0">
                    
                    <div class="relative flex items-center gap-4">
                        <div class="relative flex flex-col items-center">
                            <img src="<?= BASE_URL ?>/assets/images/moodtracker/header/Day Counter2.svg" alt="Day Counter" class="h-32">
                            <span class="absolute font-dunkin text-accent text-4xl drop-shadow-[2px_2px_0_#4a5588]" style="top: 25%; right: 27%;"><?= $dayCount ?></span>
                        </div>
                    </div>
                    <img src="<?= BASE_URL ?>/assets/images/moodtracker/header/How is your heart.svg" ...>
                    <img src="<?= BASE_URL ?>/assets/images/moodtracker/header/Mood Check.svg" ...>
                </div>
            </div>

            <div class="flex-grow overflow-y-auto pr-4 -mr-4">
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 my-4">
                    <?php
                        $moods = ['Angry', 'Disgust', 'Fear', 'Lovely', 'Trust', 'Unwell', 'Anticipation', 'Joy', 'Nonchalant', 'Sad'];
                        foreach ($moods as $mood):
                    ?>
                    
                    <button type="button" 
                            class="mood-selector flex flex-col items-center p-2 rounded-md border-2 border-transparent cursor-pointer transition-all duration-200 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-secondary"
                            data-mood="<?= htmlspecialchars($mood) ?>">
                        <img src="<?= BASE_URL ?>/assets/images/moodtracker/moods/<?= $mood ?>.svg" alt="<?= $mood ?>" class="w-full h-auto max-w-[200px] pointer-events-none">
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openModalBtn = document.getElementById('open-modal-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const moodModal = document.getElementById('mood-modal');
        const spinner = document.getElementById('modal-spinner');

        const moodSelectors = document.querySelectorAll('.mood-selector');

        const openModal = () => moodModal.classList.remove('hidden');
        const closeModal = () => moodModal.classList.add('hidden');

        if (openModalBtn) openModalBtn.addEventListener('click', openModal);
        if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);

        moodModal.addEventListener('click', (event) => {
            if (event.target === moodModal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !moodModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        moodSelectors.forEach(button => {
            button.addEventListener('click', function() {
                const selectedMood = this.dataset.mood;
                
                spinner.classList.remove('hidden');

                fetch('<?= BASE_URL ?>/moodlog/log', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mood: selectedMood
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Server error') });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeModal();
                        showFlashMessage('Mood logged successfully!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Failed to save mood.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFlashMessage('Oops! Something went wrong: ' + error.message, 'error');
                    spinner.classList.add('hidden');
                });
            });
        });
    });
</script>