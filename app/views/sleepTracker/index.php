<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Tracker - HeartLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-darkbg font-dunkin text-ghost relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.png'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

    <div class="flex min-h-screen">
        <?php include __DIR__ . '/../components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-8 w-full">
            <div class="w-full max-w-none">
                
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-dunkin text-primary mb-2">Sleep Tracker</h1>
                        <p class="text-ghost/80">Monitor your sleep patterns and get personalized recommendations</p>
                    </div>
                    
                    <div class="flex items-center">
                        <span class="mr-4 text-ghost"><?= htmlspecialchars($data['userEmail']) ?></span>
                        <a href="<?= BASE_URL ?>/profile">
                            <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="w-12 h-12 rounded-full border-2 border-accent">
                        </a>
                    </div>
                </div>

                <?php if (!$age): ?>
                    <div class="bg-yellow-500/20 border border-yellow-500 text-yellow-400 px-6 py-4 rounded-lg mb-6">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div>
                                <h3 class="font-semibold">Date of Birth Not Set</h3>
                                <p class="text-sm text-yellow-300">Please <a href="<?= BASE_URL ?>/profile" class="underline hover:text-yellow-200">update your profile</a> with your date of birth to get personalized sleep recommendations.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Success/Error Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded mb-6">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Flash Messages Container -->
                <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    
                    <div class="xl:col-span-2 space-y-6">
                    
                        <div class="bg-black/20 rounded-lg p-6">
                            <h2 class="text-xl font-dunkin text-secondary mb-4">Log Your Sleep</h2>
                            
                            <?php if ($todaysLog && date('Y-m-d', strtotime($todaysLog['log_date'])) === date('Y-m-d')): ?>
                                <!-- Already logged today -->
                                <div id="todayLoggedMessage" class="text-center py-8">
                                    <div class="flex items-center justify-center mb-4">
                                        <svg class="w-12 h-12 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-dunkin text-green-400 mb-2">You have logged your sleep today!</p>
                                    <p class="text-ghost/70 mb-6">You logged <span class="text-primary font-semibold"><?= htmlspecialchars($todaysLog['hours']) ?> hours</span> of sleep for today.</p>
                                    <button onclick="showEditForm()" class="bg-secondary hover:bg-secondary/80 text-darkbg font-semibold py-2 px-6 rounded-lg transition-colors">
                                        Edit Today's Sleep
                                    </button>
                                </div>
                                
                                <!-- Hidden edit form -->
                                <form id="sleepLogForm" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                                    <div>
                                        <label for="sleep_hours" class="block text-sm font-medium text-ghost mb-2">Hours of Sleep</label>
                                        <input type="number" id="sleep_hours" name="hours" step="0.5" min="0.5" max="24" 
                                               value="<?= htmlspecialchars($todaysLog['hours']) ?>"
                                               class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost"
                                               placeholder="e.g., 8.0" required>
                                    </div>
                                    
                                    <div>
                                        <label for="log_date" class="block text-sm font-medium text-ghost mb-2">Date</label>
                                        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" 
                                               class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                                    </div>
                                    
                                    <div class="flex items-end gap-2">
                                        <button type="submit" class="flex-1 bg-primary hover:bg-primary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                                            Update Sleep
                                        </button>
                                        <button type="button" onclick="hideEditForm()" class="bg-ghost/20 hover:bg-ghost/30 text-ghost font-semibold py-2 px-4 rounded-lg transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                                
                            <?php else: ?>
                                <!-- Normal logging form for when no log exists today -->
                                <form id="sleepLogForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="sleep_hours" class="block text-sm font-medium text-ghost mb-2">Hours of Sleep</label>
                                        <input type="number" id="sleep_hours" name="hours" step="0.5" min="0.5" max="24" 
                                               class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost"
                                               placeholder="e.g., 8.0" required>
                                    </div>
                                    
                                    <div>
                                        <label for="log_date" class="block text-sm font-medium text-ghost mb-2">Date</label>
                                        <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" 
                                               class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                                    </div>
                                    
                                    <div class="flex items-end">
                                        <button type="submit" class="w-full bg-primary hover:bg-primary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                                            Log Sleep
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Today's Sleep Status -->
                        <?php if ($todaysLog): ?>
                            <div class="bg-black/20 rounded-lg p-6">
                                <h2 class="text-xl font-dunkin text-secondary mb-4">Today's Sleep</h2>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-primary"><?= htmlspecialchars($todaysLog['hours']) ?> hours</p>
                                        <p class="text-ghost/70">Logged for <?= htmlspecialchars(date('M j, Y', strtotime($todaysLog['log_date']))) ?></p>
                                    </div>
                                    
                                    <?php if ($todaysEvaluation): ?>
                                        <div class="text-right">
                                            <div class="px-3 py-1 rounded-full text-sm font-semibold
                                                <?php
                                                echo match($todaysEvaluation['color']) {
                                                    'green' => 'bg-green-500/20 text-green-400',
                                                    'yellow' => 'bg-yellow-500/20 text-yellow-400', 
                                                    'orange' => 'bg-orange-500/20 text-orange-400',
                                                    'red' => 'bg-red-500/20 text-red-400',
                                                    default => 'bg-ghost/20 text-ghost'
                                                };
                                                ?>">
                                                <?= htmlspecialchars(ucfirst($todaysEvaluation['status'])) ?>
                                            </div>
                                            <p class="text-xs text-ghost/70 mt-1">Score: <?= htmlspecialchars($todaysEvaluation['score']) ?>/100</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($todaysEvaluation && $todaysEvaluation['feedback']): ?>
                                    <div class="mt-4 p-3 bg-darkbg/50 rounded-lg">
                                        <p class="text-sm text-ghost"><?= htmlspecialchars($todaysEvaluation['feedback']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Sleep Recommendations -->
                        <?php if ($recommendation): ?>
                            <div class="bg-black/20 rounded-lg p-6">
                                <h2 class="text-xl font-dunkin text-secondary mb-4">Your Sleep Recommendations</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="text-center">
                                        <p class="text-sm text-ghost/70">Minimum</p>
                                        <p class="text-2xl font-bold text-blue-400"><?= htmlspecialchars($recommendation['minHours']) ?>h</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-ghost/70">Optimal</p>
                                        <p class="text-2xl font-bold text-green-400"><?= htmlspecialchars($recommendation['optimalHours']) ?>h</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-ghost/70">Maximum</p>
                                        <p class="text-2xl font-bold text-orange-400"><?= htmlspecialchars($recommendation['maxHours']) ?>h</p>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-ghost/70 mb-2">Category: <span class="text-secondary font-semibold"><?= htmlspecialchars($recommendation['category']) ?></span></p>
                                    <?php if ($age): ?>
                                        <p class="text-sm text-ghost/70">Based on your age: <span class="text-secondary font-semibold"><?= htmlspecialchars($age) ?> years</span></p>
                                    <?php endif; ?>
                                    <?php if ($bmi): ?>
                                        <p class="text-sm text-ghost/70">BMI considered: <span class="text-secondary font-semibold"><?= htmlspecialchars(number_format($bmi, 1)) ?></span></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Sleep Tips -->
                                <div>
                                    <h3 class="text-lg font-medium text-ghost mb-3">Personalized Tips</h3>
                                    <ul class="space-y-2">
                                        <?php foreach (array_slice($recommendation['tips'], 0, 5) as $tip): ?>
                                            <li class="flex items-start space-x-2">
                                                <svg class="w-4 h-4 text-accent mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-sm text-ghost"><?= htmlspecialchars($tip) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Recent Sleep Logs -->
                        <div class="bg-black/20 rounded-lg p-6">
                            <h2 class="text-xl font-dunkin text-secondary mb-4">Recent Sleep Logs</h2>
                            
                            <?php if (!empty($recentLogs)): ?>
                                <div class="overflow-x-auto">
                                    <table class="w-full table-auto">
                                        <thead>
                                            <tr class="border-b border-ghost/30">
                                                <th class="text-left py-2 text-ghost font-medium">Date</th>
                                                <th class="text-left py-2 text-ghost font-medium">Hours</th>
                                                <th class="text-left py-2 text-ghost font-medium">Quality</th>
                                                <th class="text-left py-2 text-ghost font-medium">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentLogs as $log): ?>
                                                <?php 
                                                $evaluation = null;
                                                if ($age) {
                                                    $evaluation = $sleepLogModel = new SleepLog();
                                                    $evaluation = $sleepLogModel->evaluateSleepQuality((float)$log['hours'], $age, $bmi);
                                                }
                                                ?>
                                                <tr class="border-b border-ghost/10">
                                                    <td class="py-3 text-ghost"><?= htmlspecialchars(date('M j, Y', strtotime($log['log_date']))) ?></td>
                                                    <td class="py-3 text-ghost"><?= htmlspecialchars($log['hours']) ?>h</td>
                                                    <td class="py-3">
                                                        <?php if ($evaluation): ?>
                                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                <?php
                                                                echo match($evaluation['color']) {
                                                                    'green' => 'bg-green-500/20 text-green-400',
                                                                    'yellow' => 'bg-yellow-500/20 text-yellow-400', 
                                                                    'orange' => 'bg-orange-500/20 text-orange-400',
                                                                    'red' => 'bg-red-500/20 text-red-400',
                                                                    default => 'bg-ghost/20 text-ghost'
                                                                };
                                                                ?>">
                                                                <?= htmlspecialchars(ucfirst($evaluation['status'])) ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-ghost/50 text-xs">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="py-3">
                                                        <button onclick="deleteSleepLog('<?= htmlspecialchars($log['log_date']) ?>')" 
                                                                class="text-red-400 hover:text-red-300 text-sm">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <svg class="w-16 h-16 text-ghost/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                    <p class="text-ghost/50">No sleep logs found. Start tracking your sleep above!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Statistics and Insights -->
                    <div class="space-y-6">
                        <!-- Weekly Stats -->
                        <?php if ($weeklyStats): ?>
                            <div class="bg-black/20 rounded-lg p-6">
                                <h2 class="text-xl font-dunkin text-secondary mb-4">This Week</h2>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-ghost/70">Average Sleep</span>
                                        <span class="text-primary font-semibold"><?= htmlspecialchars($weeklyStats['averageHours']) ?>h</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-ghost/70">Days Logged</span>
                                        <span class="text-secondary font-semibold"><?= htmlspecialchars($weeklyStats['daysLogged']) ?>/7</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-ghost/70">Consistency</span>
                                        <span class="text-accent font-semibold"><?= htmlspecialchars($weeklyStats['complianceRate']) ?>%</span>
                                    </div>
                                    
                                    <?php if ($weeklyStats['daysLogged'] > 0): ?>
                                        <div class="flex justify-between items-center">
                                            <span class="text-ghost/70">Min/Max</span>
                                            <span class="text-ghost font-semibold"><?= htmlspecialchars($weeklyStats['minHours']) ?>h - <?= htmlspecialchars($weeklyStats['maxHours']) ?>h</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-6">
                                    <div class="flex justify-between text-sm text-ghost/70 mb-2">
                                        <span>Weekly Goal Progress</span>
                                        <span><?= htmlspecialchars($weeklyStats['complianceRate']) ?>%</span>
                                    </div>
                                    <div class="w-full bg-ghost/10 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full" 
                                             style="width: <?= htmlspecialchars($weeklyStats['complianceRate']) ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Sleep Quality Insights -->
                        <?php if ($recommendation && !empty($recentLogs)): ?>
                            <div class="bg-black/20 rounded-lg p-6">
                                <h2 class="text-xl font-dunkin text-secondary mb-4">Sleep Quality</h2>
                                
                                <div class="space-y-3">
                                    <?php
                                    $qualityCount = ['excellent' => 0, 'good' => 0, 'poor' => 0];
                                    if ($age) {
                                        $sleepModel = new SleepLog();
                                        foreach ($recentLogs as $log) {
                                            $eval = $sleepModel->evaluateSleepQuality((float)$log['hours'], $age, $bmi);
                                            if ($eval['status'] === 'excellent') $qualityCount['excellent']++;
                                            elseif ($eval['status'] === 'good') $qualityCount['good']++;
                                            else $qualityCount['poor']++;
                                        }
                                    }
                                    ?>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                            <span class="text-ghost/70">Excellent</span>
                                        </span>
                                        <span class="text-green-400 font-semibold"><?= htmlspecialchars($qualityCount['excellent']) ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                            <span class="text-ghost/70">Good</span>
                                        </span>
                                        <span class="text-yellow-400 font-semibold"><?= htmlspecialchars($qualityCount['good']) ?></span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                            <span class="text-ghost/70">Needs Improvement</span>
                                        </span>
                                        <span class="text-red-400 font-semibold"><?= htmlspecialchars($qualityCount['poor']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Quick Actions -->
                        <div class="bg-black/20 rounded-lg p-6">
                            <h2 class="text-xl font-dunkin text-secondary mb-4">Quick Actions</h2>
                            
                            <div class="space-y-3">
                                <button onclick="fillTodaysOptimalSleep()" class="w-full bg-secondary/20 hover:bg-secondary/30 border border-secondary text-secondary font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                                    Log Optimal Sleep (<?= $recommendation ? $recommendation['optimalHours'] . 'h' : '8h' ?>)
                                </button>
                                
                                <a href="<?= BASE_URL ?>/reports" class="block w-full bg-accent/20 hover:bg-accent/30 border border-accent text-accent font-semibold py-2 px-4 rounded-lg transition-colors text-sm text-center">
                                    View Sleep Reports
                                </a>
                                
                                <?php if (!$age): ?>
                                    <a href="<?= BASE_URL ?>/profile" class="block w-full bg-primary/20 hover:bg-primary/30 border border-primary text-primary font-semibold py-2 px-4 rounded-lg transition-colors text-sm text-center">
                                        Update Age for Recommendations
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        include __DIR__ . '/../moodTracker/moodlogmodal.php';
    ?>

    <script>
        // Show/hide edit form functions
        function showEditForm() {
            document.getElementById('todayLoggedMessage').classList.add('hidden');
            document.getElementById('sleepLogForm').classList.remove('hidden');
        }
        
        function hideEditForm() {
            document.getElementById('todayLoggedMessage').classList.remove('hidden');
            document.getElementById('sleepLogForm').classList.add('hidden');
        }
        
        // Sleep logging form handler
        document.getElementById('sleepLogForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = submitBtn.textContent.includes('Update') ? 'Updating...' : 'Logging...';
            
            try {
                const response = await fetch('<?= BASE_URL ?>/sleep/log', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const isUpdate = originalText.includes('Update');
                    showFlashMessage(isUpdate ? 'Sleep updated successfully!' : 'Sleep logged successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showFlashMessage('Error: ' + result.error, 'error');
                }
            } catch (error) {
                showFlashMessage('Error logging sleep: ' + error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
        
        // Delete sleep log
        async function deleteSleepLog(logDate) {
            if (!confirm('Are you sure you want to delete this sleep log?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('log_date', logDate);
            
            try {
                const response = await fetch('<?= BASE_URL ?>/sleep/delete', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showFlashMessage('Sleep log deleted successfully!', 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showFlashMessage('Error: ' + result.error, 'error');
                }
            } catch (error) {
                showFlashMessage('Error deleting sleep log: ' + error.message, 'error');
            }
        }
        
        // Quick action: fill optimal sleep
        function fillTodaysOptimalSleep() {
            const optimalHours = <?= $recommendation ? $recommendation['optimalHours'] : 8 ?>;
            
            <?php if ($todaysLog && date('Y-m-d', strtotime($todaysLog['log_date'])) === date('Y-m-d')): ?>
                // If today's log exists, show edit form first
                showEditForm();
            <?php endif; ?>
            
            document.getElementById('sleep_hours').value = optimalHours;
            showFlashMessage(`Pre-filled with optimal sleep time (${optimalHours}h) ✨`, 'info');
        }
        
        // Flash message system
        function showFlashMessage(message, type = 'info') {
            const container = document.getElementById('flash-container');
            const messageId = 'flash-' + Date.now();
            
            // Define styles for different message types
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
            
            // Create flash message element
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
            
            // Animate in
            setTimeout(() => {
                flashDiv.classList.remove('translate-x-full');
                flashDiv.classList.add('translate-x-0');
            }, 100);
            
            // Auto remove after 4 seconds
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
        
        // Auto-fill today's date on page load
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('log_date').value = today;
        });
    </script>
</body>
</html>