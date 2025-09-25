<div class="mb-8">
    <h2 class="font-dunkin text-2xl text-ghost mb-4">This Week's Moods</h2>
    
    <div class="flex justify-start gap-2 sm:gap-4 flex-wrap w-full">
        
        <?php foreach ($latestMoods as $log): ?>
            <div class="flex flex-col items-center flex-1 min-w-[100px] max-w-[150px]">
                
                <?php if ($log['tracked']): ?>
                    <img src="<?= BASE_URL ?>/assets/images/moodtracker/moods/<?= htmlspecialchars($log['mood']) ?>.svg" 
                         alt="<?= htmlspecialchars($log['mood']) ?>" 
                         class="w-32 h-32 md:w-32 md:h-32">
                    
                    <!-- Colored Banner -->
                    <div class="text-center rounded-b-lg -mt-2 w-14 md:w-16" 
                         style="background-color: <?= $moodColors[$log['mood']] ?? '#4a5588' ?>;">
                        <p class="text-xs font-bold text-darkbg">Day</p>
                        <p class="text-lg font-dunkin text-white leading-none"><?= $log['day'] ?></p>
                    </div>
                
                <?php else: ?>
                    <div class="w-32 h-32 flex justify-center items-center bg-black/20 rounded-full border-2 border-dashed border-gray-600">
                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4c0-1.165.549-2.21 1.402-2.919m-1.402 2.919a2 2 0 11-2.804-2.804 2 2 0 012.804 2.804z"></path></svg>
                    </div>
                    <div class="text-center rounded-b-lg -mt-2 w-14 md:w-16 bg-gray-700">
                        <p class="text-xs font-bold text-darkbg">Day</p>
                        <p class="text-lg font-dunkin text-white leading-none"><?= $log['day'] ?></p>
                    </div>
                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>
</div>