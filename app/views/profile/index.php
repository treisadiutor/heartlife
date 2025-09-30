<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Profile Management') ?> - HeartLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg font-dunkin text-ghost relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-8 w-full">
        <div class="w-full max-w-none">
            <div class="mb-8">
                <h1 class="text-3xl font-dunkin text-primary mb-2">Profile Management</h1>
                <p class="text-ghost/80">Manage your profile information, password, and BMI logs</p>
            </div>

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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-black/20 rounded-lg p-6">
                    <h2 class="text-xl font-dunkin text-secondary mb-4">Profile Information</h2>
                    
                    <!-- Profile Picture -->
                    <div class="mb-6 text-center">
                        <div class="w-32 h-32 mx-auto mb-4 rounded-full overflow-hidden border-2 border-secondary">
                            <?php if (!empty($user['profile_pic']) && file_exists(__DIR__ . '/../../../' . $user['profile_pic'])): ?>
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-primary/20 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-ghost/50" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <form action="<?= BASE_URL ?>/profile/update-picture" method="POST" enctype="multipart/form-data" class="space-y-3">
                            <input type="file" name="profile_pic" accept="image/*" class="block w-full text-sm text-ghost file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-secondary file:text-darkbg hover:file:bg-secondary/80">
                            <button type="submit" class="px-4 py-2 bg-secondary text-darkbg rounded-lg hover:bg-secondary/80 transition-colors">
                                Update Photo
                            </button>
                        </form>
                    </div>

                    <!-- Profile Form -->
                    <form action="<?= BASE_URL ?>/profile/update" method="POST" class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-ghost mb-2">Username</label>
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-ghost mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-ghost mb-2">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>" 
                                   max="<?= date('Y-m-d', strtotime('-13 years')) ?>"
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                            <?php if (!empty($user['date_of_birth'])): ?>
                                <?php 
                                    $userModel = new User();
                                    $calculatedAge = $userModel->calculateAge($user['date_of_birth']); 
                                ?>
                                <p class="text-xs text-secondary mt-1">Current age: <?= $calculatedAge ?> years old</p>
                            <?php endif; ?>
                            <p class="text-xs text-ghost/60 mt-1">Used for personalized health and sleep recommendations</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-primary hover:bg-primary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                            Update Profile
                        </button>
                    </form>
                </div>

                <!-- Password Change -->
                <div class="bg-black/20 rounded-lg p-6">
                    <h2 class="text-xl font-dunkin text-secondary mb-4">Change Password</h2>
                    
                    <form action="<?= BASE_URL ?>/profile/update-password" method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-ghost mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-ghost mb-2">New Password</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-ghost mb-2">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="w-full px-3 py-2 bg-darkbg border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <button type="submit" class="w-full bg-accent hover:bg-accent/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- BMI Logs Section -->
            <div class="mt-8 bg-black/20 rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-dunkin text-secondary">BMI Logs</h2>
                    <button onclick="toggleBmiForm()" class="bg-primary hover:bg-primary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                        Add New Entry
                    </button>
                </div>

                <!-- Add BMI Form -->
                <div id="bmiForm" class="hidden mb-6 p-4 bg-darkbg rounded-lg border border-ghost/30">
                    <h3 class="text-lg font-medium text-ghost mb-4">Add BMI Entry</h3>
                    <form action="<?= BASE_URL ?>/profile/add-bmi-log" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="height_cm" class="block text-sm font-medium text-ghost mb-2">Height (cm)</label>
                            <input type="number" id="height_cm" name="height_cm" step="0.1" min="1" max="300" 
                                   class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="weight_kg" class="block text-sm font-medium text-ghost mb-2">Weight (kg)</label>
                            <input type="number" id="weight_kg" name="weight_kg" step="0.1" min="1" max="1000" 
                                   class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div>
                            <label for="log_date" class="block text-sm font-medium text-ghost mb-2">Date</label>
                            <input type="date" id="log_date" name="log_date" value="<?= date('Y-m-d') ?>" 
                                   class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-secondary hover:bg-secondary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                                Add Entry
                            </button>
                        </div>
                    </form>
                </div>

                <!-- BMI Logs Table -->
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="border-b border-ghost/30">
                                <th class="text-left py-2 text-ghost font-medium">Date</th>
                                <th class="text-left py-2 text-ghost font-medium">Height (cm)</th>
                                <th class="text-left py-2 text-ghost font-medium">Weight (kg)</th>
                                <th class="text-left py-2 text-ghost font-medium">BMI</th>
                                <th class="text-left py-2 text-ghost font-medium">Category</th>
                                <th class="text-left py-2 text-ghost font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bmiLogs)): ?>
                                <?php foreach ($bmiLogs as $log): ?>
                                    <?php
                                    $bmi = $log['bmi_value'];
                                    $category = 'N/A';
                                    $categoryColor = 'text-ghost';
                                    
                                    if ($bmi > 0) {
                                        if ($bmi < 18.5) {
                                            $category = 'Underweight';
                                            $categoryColor = 'text-blue-400';
                                        } elseif ($bmi < 25) {
                                            $category = 'Normal';
                                            $categoryColor = 'text-green-400';
                                        } elseif ($bmi < 30) {
                                            $category = 'Overweight';
                                            $categoryColor = 'text-yellow-400';
                                        } else {
                                            $category = 'Obese';
                                            $categoryColor = 'text-red-400';
                                        }
                                    }
                                    ?>
                                    <tr class="border-b border-ghost/10">
                                        <td class="py-3 text-ghost"><?= htmlspecialchars(date('M j, Y', strtotime($log['log_date']))) ?></td>
                                        <td class="py-3 text-ghost"><?= htmlspecialchars(number_format($log['height_cm'], 1)) ?></td>
                                        <td class="py-3 text-ghost"><?= htmlspecialchars(number_format($log['weight_kg'], 1)) ?></td>
                                        <td class="py-3 text-ghost"><?= htmlspecialchars(number_format($bmi, 1)) ?></td>
                                        <td class="py-3 <?= $categoryColor ?>"><?= htmlspecialchars($category) ?></td>
                                        <td class="py-3">
                                            <div class="flex space-x-2">
                                                <button onclick="editBmiLog(<?= htmlspecialchars(json_encode($log)) ?>)" 
                                                        class="text-secondary hover:text-secondary/80 text-sm">
                                                    Edit
                                                </button>
                                                <form action="<?= BASE_URL ?>/profile/delete-bmi-log" method="POST" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this BMI log?')">
                                                    <input type="hidden" name="log_id" value="<?= htmlspecialchars($log['id']) ?>">
                                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-ghost/60">
                                        No BMI logs found. Add your first entry above!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit BMI Modal -->
    <div id="editBmiModal" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-darkbg rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-ghost mb-4">Edit BMI Entry</h3>
                <form id="editBmiForm" action="<?= BASE_URL ?>/profile/update-bmi-log" method="POST" class="space-y-4">
                    <input type="hidden" id="edit_log_id" name="log_id">
                    
                    <div>
                        <label for="edit_height_cm" class="block text-sm font-medium text-ghost mb-2">height_cm (cm)</label>
                        <input type="number" id="edit_height_cm" name="height_cm" step="0.1" min="1" max="300" 
                               class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                    </div>
                    
                    <div>
                        <label for="edit_weight_kg" class="block text-sm font-medium text-ghost mb-2">weight_kg (kg)</label>
                        <input type="number" id="edit_weight_kg" name="weight_kg" step="0.1" min="1" max="1000" 
                               class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                    </div>
                    
                    <div>
                        <label for="edit_log_date" class="block text-sm font-medium text-ghost mb-2">Date</label>
                        <input type="date" id="edit_log_date" name="log_date" 
                               class="w-full px-3 py-2 bg-black/20 border border-ghost/30 rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent text-ghost">
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-secondary hover:bg-secondary/80 text-darkbg font-semibold py-2 px-4 rounded-lg transition-colors">
                            Update
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 bg-ghost/20 hover:bg-ghost/30 text-ghost font-semibold py-2 px-4 rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
        include __DIR__ . '/../moodTracker/moodLogModal.php';
    ?>
    <script>
        function toggleBmiForm() {
            const form = document.getElementById('bmiForm');
            form.classList.toggle('hidden');
        }

        function editBmiLog(log) {
            document.getElementById('edit_log_id').value = log.id;
            document.getElementById('edit_height_cm').value = log.height_cm;
            document.getElementById('edit_weight_kg').value = log.weight_kg;
            document.getElementById('edit_log_date').value = log.log_date;
            document.getElementById('editBmiModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editBmiModal').classList.add('hidden');
        }

        document.getElementById('editBmiModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>