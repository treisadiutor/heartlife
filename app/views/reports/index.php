<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - HeartLife</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>
<body class="bg-darkbg font-dunkin text-ghost relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    <main class="flex-1 p-8 h-screen overflow-y-auto">
        <!-- Top Bar: Title & Date Filter -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="font-dunkin text-3xl text-accent mb-4 md:mb-0">Reports</h1>
            <div class="font-dunkin flex items-center gap-2">
                <label class="text-ghost">Date:</label>
                <select id="dateRange" class="rounded-lg px-3 py-2 bg-primary text-darkbg font-semibold shadow focus:outline-none" onchange="changeDateRange()">
                    <option value="last_30_days" <?= ($data['dateRange']['selected'] == 'last_30_days') ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="last_7_days" <?= ($data['dateRange']['selected'] == 'last_7_days') ? 'selected' : '' ?>>Last 7 Days</option>
                    <option value="this_month" <?= ($data['dateRange']['selected'] == 'this_month') ? 'selected' : '' ?>>This Month</option>
                    <option value="custom" <?= ($data['dateRange']['selected'] == 'custom') ? 'selected' : '' ?>>Custom</option>
                </select>
                <?php if ($data['dateRange']['selected'] == 'custom'): ?>
                <div class="flex gap-2 ml-2">
                    <input type="date" id="startDate" value="<?= $data['dateRange']['start'] ?>" class="rounded px-2 py-1 bg-primary text-darkbg text-sm">
                    <input type="date" id="endDate" value="<?= $data['dateRange']['end'] ?>" class="rounded px-2 py-1 bg-primary text-darkbg text-sm">
                    <button onclick="applyCustomRange()" class="px-3 py-1 bg-accent text-white rounded text-sm hover:bg-pink-400">Apply</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Mood Section -->
            <div class="bg-black/20 bg-opacity-80 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col border border-white/10">
                <h2 class="font-dunkin text-xl text-accent mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M12 18a9 9 0 100-18 9 9 0 000 18z" /></svg>
                    Mood
                </h2>
                <?php if (isset($data['moodStats']) && !empty($data['moodStats']['average']) && !empty($data['moodStats']['summary'])): ?>
                    <div class="font-dunkin mb-2 font-semibold text-ghost">Average Mood: <span class="text-accent"><?= htmlspecialchars($data['moodStats']['average']) ?></span></div>
                    <div class="font-dunkin mb-2 text-sm">Summary:</div>
                    <?php if (!empty($data['moodStats']['summary'])): ?>
                        <ul class="mb-2 ml-2">
                            <?php foreach ($data['moodStats']['summary'] as $type => $percent): ?>
                                <li class="font-dunkin text-xs"><?= htmlspecialchars($type) ?>: <span class="font-bold"><?= $percent ?>%</span></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-6">
                        <p class="font-dunkin text-sm text-white/50 mb-2">No mood data found</p>
                        <p class="font-dunkin text-xs text-gray-500 mb-3">Log your mood to see insights!</p>
                        <a href="<?= BASE_URL ?>/profile" class="inline-block bg-accent/80 hover:bg-purple-600 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            Log Your Mood
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            
            <!-- Sleep Section -->
            <div class="bg-black/20 bg-opacity-80 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col border border-white/10">
                <h2 class="font-dunkin text-xl text-blue-400 mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    Sleep
                </h2>
                <?php if (isset($data['sleepStats']) && $data['sleepStats']['daysLogged'] > 0): ?>
                    <div class="font-dunkin mb-2 font-semibold text-ghost">Average Sleep: <span class="text-blue-400"><?= number_format($data['sleepStats']['averageHours'], 1) ?>h</span></div>
                    <div class="font-dunkin mb-2 text-sm">Days Logged: <span class="font-bold"><?= $data['sleepStats']['daysLogged'] ?>/<?= $data['sleepStats']['totalDaysInPeriod'] ?></span></div>
                    <div class="font-dunkin mb-2 text-sm">Consistency: <span class="font-bold"><?= number_format($data['sleepStats']['complianceRate'], 1) ?>%</span></div>
                    <?php if ($data['sleepStats']['averageHours'] > 0): ?>
                        <div class="font-dunkin text-xs text-gray-300">
                            Range: <?= number_format($data['sleepStats']['minHours'], 1) ?>h - <?= number_format($data['sleepStats']['maxHours'], 1) ?>h
                        </div>
                    <?php endif; ?>
                    
                    <!-- Sleep Quality Indicator -->
                    <?php 
                    $userModel = new User();
                    $userAge = $userModel->calculateAge($data['userData']['date_of_birth'] ?? null);
                    ?>
                    <?php if ($userAge): ?>
                        <?php
                        $sleepModel = new SleepLog();
                        $evaluation = $sleepModel->evaluateSleepQuality($data['sleepStats']['averageHours'], $userAge, $data['bmiStats']['latest']['value'] ?? null);
                        ?>
                        <div class="mt-3 px-3 py-1 rounded-full text-center text-xs font-semibold
                            <?php
                            echo match($evaluation['color']) {
                                'green' => 'bg-green-500/20 text-green-400',
                                'yellow' => 'bg-yellow-500/20 text-yellow-400', 
                                'orange' => 'bg-orange-500/20 text-orange-400',
                                'red' => 'bg-red-500/20 text-red-400',
                                default => 'bg-gray-500/20 text-gray-400'
                            };
                            ?>">
                            Average Quality: <?= htmlspecialchars(ucfirst($evaluation['status'])) ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-6">
                        <p class="font-dunkin text-sm text-white/50 mb-2">No sleep data found</p>
                        <p class="font-dunkin text-xs text-gray-500 mb-3">Start tracking your sleep to see insights!</p>
                        <a href="<?= BASE_URL ?>/sleepTracker" class="inline-block bg-blue-500 hover:bg-purple-600 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            Track Your Sleep
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            
            <!-- BMI Section -->
            <div class="bg-black/20 bg-opacity-80 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col border border-white/10">
                <h2 class="font-dunkin text-xl text-yellow-400 mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    BMI
                </h2>
                <?php if ($data['bmiStats']['latest']['value'] > 0): ?>
                    <div class="font-dunkin mb-2 font-semibold text-ghost">Latest BMI: <span class="text-yellow-400"><?= number_format($data['bmiStats']['latest']['value'], 1) ?></span></div>
                    <div class="font-dunkin mb-2 text-sm">Classification: <span class="font-bold"><?= htmlspecialchars($data['bmiStats']['latest']['class']) ?></span></div>
                <?php else: ?>
                    <div class="text-center py-6">
                        <p class="font-dunkin text-sm text-white/50 mb-2">No BMI data found</p>
                        <p class="font-dunkin text-xs text-gray-500 mb-3">Log your BMI to get personalized recommendations!</p>
                        <a href="<?= BASE_URL ?>/profile" class="inline-block bg-yellow-500 hover:bg-purple-600 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            Log Your BMI
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Checklist Section -->
            <div class="bg-black/20 bg-opacity-80 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col border border-white/10">
                <h2 class="font-dunkin text-xl text-green-400 mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Checklist
                </h2>
                
                <?php if ($data['checklistStats']['total'] > 0): ?>
                    <!-- Overall Completion Rate -->
                    <div class="font-dunkin mb-3 font-semibold text-ghost">
                        Overall Completion: <span class="text-green-400"><?= htmlspecialchars($data['checklistStats']['overallRate']) ?>%</span>
                    </div>
                    
                    <!-- Morning vs Night Breakdown -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <div class="text-center bg-pink-500/10 rounded-lg p-2">
                            <div class="font-dunkin text-xs text-pink-300 mb-1">Morning</div>
                            <div class="font-dunkin text-lg font-bold text-pink-400"><?= htmlspecialchars($data['checklistStats']['morningRate']) ?>%</div>
                        </div>
                        <div class="text-center bg-blue-500/10 rounded-lg p-2">
                            <div class="font-dunkin text-xs text-blue-300 mb-1">Night</div>
                            <div class="font-dunkin text-lg font-bold text-blue-400"><?= htmlspecialchars($data['checklistStats']['nightRate']) ?>%</div>
                        </div>
                    </div>
                    
                    <!-- Status Breakdown -->
                    <div class="font-dunkin mb-2 text-sm">
                        <div class="flex justify-between items-center mb-1">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                Completed:
                            </span>
                            <span class="font-bold text-green-400"><?= htmlspecialchars($data['checklistStats']['completed']) ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                                Pending:
                            </span>
                            <span class="font-bold text-yellow-400"><?= htmlspecialchars($data['checklistStats']['pending']) ?></span>
                        </div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="flex items-center">
                                <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                Missed:
                            </span>
                            <span class="font-bold text-red-400"><?= htmlspecialchars($data['checklistStats']['missed']) ?></span>
                        </div>
                    </div>
                    
                    <!-- Total Tasks -->
                    <div class="font-dunkin text-xs text-gray-300 pt-2 border-t border-white/10">
                        Total Tasks: <span class="font-bold"><?= htmlspecialchars($data['checklistStats']['total']) ?></span>
                        <br>
                        Period: <?= htmlspecialchars($data['checklistStats']['totalDays']) ?> days
                    </div>
                    
                    <?php if ($data['checklistStats']['missed'] > 0): ?>
                        <div class="font-dunkin text-xs text-red-300 mt-2 bg-red-500/10 rounded p-2">
                            You have <?= $data['checklistStats']['missed'] ?> missed task<?= $data['checklistStats']['missed'] > 1 ? 's' : '' ?> in this period.
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="font-dunkin text-sm text-white/50 mb-2">No checklist items found.</p>
                        <p class="font-dunkin text-xs text-gray-500 mb-3">Create some self-care tasks to track your progress!</p>
                        <a href="<?= BASE_URL ?>/notes" class="inline-block bg-green-500 hover:bg-purple-600 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            Create First Checklist Item
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notes Section -->
            <div class="bg-black/20 bg-opacity-80 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col border border-white/10">
                <h2 class="font-dunkin text-xl text-purple-200 mb-2 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Notes
                </h2>
                
                <?php if ($data['notesStats']['count'] > 0): ?>
                    <div class="font-dunkin mb-3 font-semibold text-ghost">
                        Total Notes: <span class="text-purple-200"><?= htmlspecialchars($data['notesStats']['count']) ?></span>
                        <?php if (isset($data['notesStats']['totalAllTime']) && $data['notesStats']['totalAllTime'] != $data['notesStats']['count']): ?>
                            <span class="text-xs text-gray-400">(<?= $data['notesStats']['totalAllTime'] ?> all-time)</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status Breakdown -->
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="text-center bg-green-500/10 rounded-lg p-2">
                            <div class="font-dunkin text-xs text-green-300 mb-1">Active</div>
                            <div class="font-dunkin text-lg font-bold text-green-400"><?= $data['notesStats']['active'] ?></div>
                        </div>
                        <div class="text-center bg-blue-500/10 rounded-lg p-2">
                            <div class="font-dunkin text-xs text-blue-300 mb-1">Completed</div>
                            <div class="font-dunkin text-lg font-bold text-blue-400"><?= $data['notesStats']['completed'] ?></div>
                        </div>
                        <div class="text-center bg-purple-500/10 rounded-lg p-2">
                            <div class="font-dunkin text-xs text-purple-300 mb-1">Pinned</div>
                            <div class="font-dunkin text-lg font-bold text-purple-400"><?= $data['notesStats']['pinned'] ?></div>
                        </div>
                    </div>
                    
                    <!-- Completion Rate -->
                    <?php 
                    $totalNotes = $data['notesStats']['active'] + $data['notesStats']['completed'] + $data['notesStats']['pinned'];
                    $completionRate = $totalNotes > 0 ? round(($data['notesStats']['completed'] / $totalNotes) * 100, 1) : 0;
                    ?>
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-dunkin text-sm text-ghost">Completion Rate</span>
                            <span class="font-dunkin text-sm font-bold text-purple-200"><?= $completionRate ?>%</span>
                        </div>
                        <div class="w-full bg-black/30 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-500 to-blue-400 h-2 rounded-full" style="width: <?= $completionRate ?>%"></div>
                        </div>
                    </div>
                    
                    <!-- Period Information -->
                    <?php if (isset($data['notesStats']['inPeriod'])): ?>
                        <?php if (!$data['notesStats']['inPeriod']): ?>
                            <p class="font-dunkin text-xs text-orange-300 bg-orange-500/10 rounded p-2">
                                No notes created in selected period - showing all-time statistics
                            </p>
                        <?php else: ?>
                            <p class="font-dunkin text-xs text-gray-300">
                                Notes created: <?= date('M j', strtotime($data['notesStats']['periodStart'])) ?> - <?= date('M j, Y', strtotime($data['notesStats']['periodEnd'])) ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-6">
                        <p class="font-dunkin text-sm text-white/50 mb-2">No notes found</p>
                        <p class="font-dunkin text-xs text-gray-500 mb-3">Start creating notes to track your thoughts and ideas!</p>
                        <a href="<?= BASE_URL ?>/notes" class="inline-block bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            Create First Note
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<?php
include __DIR__ . '/../moodTracker/moodLogModal.php';
?>

<script>
function changeDateRange() {
    const range = document.getElementById('dateRange').value;
    if (range === 'custom') {
        // Show custom date inputs (handled by PHP above)
        window.location.href = '<?= BASE_URL ?>/report?range=custom';
    } else {
        window.location.href = '<?= BASE_URL ?>/report?range=' + range;
    }
}

function applyCustomRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (startDate && endDate) {
        window.location.href = '<?= BASE_URL ?>/report?range=custom&start_date=' + startDate + '&end_date=' + endDate;
    }
}
</script>
</body>
</html>