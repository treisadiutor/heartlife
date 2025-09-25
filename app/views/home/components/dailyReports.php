<!-- components/daily_reports.php -->
<div>
    <div class="flex items-center gap-6 mb-6">
        <img src="<?= BASE_URL ?>/assets/images/normalButtons/daily-report.png" class="w-36 h-auto">
        <img src="<?= BASE_URL ?>/assets/images/normalButtons/report-progress.png" class="w-36 h-auto">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        <!-- Sleep Card -->
        <div class="stat-card">
            <p class="stat-label">Today's Sleep</p>
            <?php if ($sleepHours): ?>
                <p class="stat-value">
                    <?= number_format($sleepHours, 1) ?> 
                    <span class="text-xl">hours</span>
                    <?php if ($sleepEvaluation): ?>
                        <span class="block text-sm mt-1 <?= $sleepEvaluation['color'] === 'green' ? 'text-green-400' : ($sleepEvaluation['color'] === 'yellow' ? 'text-yellow-400' : ($sleepEvaluation['color'] === 'orange' ? 'text-orange-400' : 'text-red-400')) ?>">
                            <?= htmlspecialchars($sleepEvaluation['status']) ?>
                        </span>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p class="stat-value">
                    <span class="text-lg text-gray-400">Not logged</span>
                    <a href="<?= BASE_URL ?>/sleepTracker" class="block text-sm text-secondary hover:text-primary mt-2">Log Sleep →</a>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Notes Card -->
        <div class="stat-card">
            <p class="stat-label">
                <?php if ($notesStats['inPeriod'] ?? false): ?>
                    Today's Notes
                <?php else: ?>
                    Total Notes
                <?php endif; ?>
            </p>
            <?php if ($notesCount > 0): ?>
                <p class="stat-value">
                    <?= $notesCount ?>
                    <?php if (!($notesStats['inPeriod'] ?? false) && $totalNotesAllTime > 0): ?>
                        <span class="block text-xs text-gray-400 mt-1">All-time total</span>
                    <?php endif; ?>
                </p>
                
                <?php if ($notesStats['active'] > 0 || $notesStats['completed'] > 0 || $notesStats['pinned'] > 0): ?>
                    <div class="flex gap-2 mt-2 text-xs">
                        <?php if ($notesStats['active'] > 0): ?>
                            <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded">
                                <?= $notesStats['active'] ?> Active
                            </span>
                        <?php endif; ?>
                        <?php if ($notesStats['completed'] > 0): ?>
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded">
                                <?= $notesStats['completed'] ?> Done
                            </span>
                        <?php endif; ?>
                        <?php if ($notesStats['pinned'] > 0): ?>
                            <span class="bg-purple-500/20 text-purple-400 px-2 py-1 rounded">
                                <?= $notesStats['pinned'] ?> Pinned
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <p class="stat-value">
                    <span class="text-lg text-gray-400">No notes</span>
                    <a href="<?= BASE_URL ?>/notes" class="block text-sm text-secondary hover:text-primary mt-2">Create Note →</a>
                </p>
            <?php endif; ?>
        </div>
        
        <!-- Sleep Tracker Statistics Card -->
        <div class="stat-card col-span-1 sm:col-span-2 text-dunkin">
            <div class="flex justify-between items-start mb-3">
                <p class="stat-label text-dunkin">Sleep Statistics (7 days)</p>
                <a href="<?= BASE_URL ?>/sleepTracker" class="text-secondary hover:text-primary text-sm">View Details →</a>
            </div>
            
            <?php if ($sleepWeeklyStats['daysLogged'] > 0): ?>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs mb-4">
                    <div>
                        <p class="text-gray-400">Average</p>
                        <p class="text-ghost font-semibold"><?= $sleepWeeklyStats['averageHours'] ?>h</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Days Logged</p>
                        <p class="text-ghost font-semibold"><?= $sleepWeeklyStats['daysLogged'] ?>/7</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Min/Max</p>
                        <p class="text-ghost font-semibold"><?= $sleepWeeklyStats['minHours'] ?>h / <?= $sleepWeeklyStats['maxHours'] ?>h</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Compliance</p>
                        <p class="text-ghost font-semibold"><?= $sleepWeeklyStats['complianceRate'] ?>%</p>
                    </div>
                </div>
                
                <?php if ($sleepEvaluation): ?>
                    <div class="mb-4">
                        <p class="stat-label mb-2">Sleep Quality: <span class="text-<?= $sleepEvaluation['color'] === 'green' ? 'green' : ($sleepEvaluation['color'] === 'yellow' ? 'yellow' : ($sleepEvaluation['color'] === 'orange' ? 'orange' : 'red')) ?>-400"><?= ucfirst($sleepEvaluation['status']) ?></span></p>
                        <div class="w-full bg-black/30 rounded-full h-4 mb-2">
                            <div class="h-4 rounded-full <?= $sleepEvaluation['color'] === 'green' ? 'bg-gradient-to-r from-green-500 to-green-400' : ($sleepEvaluation['color'] === 'yellow' ? 'bg-gradient-to-r from-yellow-500 to-yellow-400' : ($sleepEvaluation['color'] === 'orange' ? 'bg-gradient-to-r from-orange-500 to-orange-400' : 'bg-gradient-to-r from-red-500 to-red-400')) ?>" 
                                 style="width: <?= $sleepEvaluation['score'] ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-ghost">
                            <span>Poor</span>
                            <span>Insufficient</span>
                            <span>Good</span>
                            <span>Excellent</span>
                        </div>
                        <?php if (!empty($sleepEvaluation['feedback'])): ?>
                            <p class="text-sm text-gray-400 mt-2"><?= htmlspecialchars($sleepEvaluation['feedback']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php elseif ($sleepRecommendation && $sleepWeeklyStats['averageHours'] > 0): ?>
                    
                    <?php 
                    $weeklyEvaluation = $sleepLogModel->evaluateSleepQuality($sleepWeeklyStats['averageHours'], $userAge, $userBmi);
                    ?>

                    <div class="mb-4">
                        <p class="stat-label mb-2">Weekly Average Quality: <span class="text-<?= $weeklyEvaluation['color'] === 'green' ? 'green' : ($weeklyEvaluation['color'] === 'yellow' ? 'yellow' : ($weeklyEvaluation['color'] === 'orange' ? 'orange' : 'red')) ?>-400"><?= ucfirst($weeklyEvaluation['status']) ?></span></p>
                        <div class="w-full bg-black/30 rounded-full h-4 mb-2">
                            <div class="h-4 rounded-full <?= $weeklyEvaluation['color'] === 'green' ? 'bg-gradient-to-r from-green-500 to-green-400' : ($weeklyEvaluation['color'] === 'yellow' ? 'bg-gradient-to-r from-yellow-500 to-yellow-400' : ($weeklyEvaluation['color'] === 'orange' ? 'bg-gradient-to-r from-orange-500 to-orange-400' : 'bg-gradient-to-r from-red-500 to-red-400')) ?>" 
                                 style="width: <?= $weeklyEvaluation['score'] ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-ghost">
                            <span>Poor</span>
                            <span>Insufficient</span>
                            <span>Good</span>
                            <span>Excellent</span>
                        </div>
                    </div>

                <?php endif; ?>
                
                <?php if ($sleepRecommendation): ?>
                    <div class="p-3 bg-darkbg rounded-md border-l-4 border-secondary">
                        <p class="text-sm text-gray-400">Recommendation for your age group (<?= $sleepRecommendation['category'] ?>)</p>
                        <p class="text-ghost font-semibold"><?= $sleepRecommendation['minHours'] ?>-<?= $sleepRecommendation['maxHours'] ?> hours (optimal: <?= $sleepRecommendation['optimalHours'] ?>h)</p>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-6">
                    <p class="text-gray-400 mb-3">No sleep data recorded this week</p>
                    <a href="<?= BASE_URL ?>/sleepTracker" class="inline-block bg-secondary hover:bg-primary text-darkbg px-4 py-2 rounded-md transition-colors">
                        Start Tracking Sleep
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>