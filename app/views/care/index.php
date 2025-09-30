<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self Care - HeartLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg font-dunkin relative" style="background-image: url('<?= BASE_URL ?>/assets/images/background.png'); background-size: cover; background-attachment: fixed; backdrop-filter: blur(15px);">

<div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">

    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <main class="flex-1 p-8 h-screen overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="font-dunkin text-3xl text-accent">Self Care Checklist</h1>
            <div class="flex items-center">
            <span class="mr-4 text-ghost"><?= htmlspecialchars($userEmail) ?></span>
            <a href="<?= BASE_URL ?>/profile">
                <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="w-12 h-12 rounded-full border-2 border-accent">
            </a>
            </div>
        </div>

        <!-- Flash Messages Container -->
        <div id="flash-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

        <!-- Mental Health Assessment Button -->
        <div class="mb-8 flex justify-between items-center">
            <button id="start-assessment" class="btn-pixel text-white px-6 py-3 rounded-lg transition-colors">
                Start Daily Assessment
            </button>
            <?php if (isset($latestAssessment)): ?>
                <span class="text-ghost">Last assessment: <?= (new DateTime($latestAssessment['created_at']))->format('M j, Y g:i A') ?></span>
            <?php endif; ?>
        </div>

        <!-- Mental Health Assessment Modal -->
        <div id="assessment-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm flex justify-center items-center hidden z-[50]">
            <div class="bg-darkbg border border-primary/50 rounded-lg p-8 w-full max-w-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto">
                <!-- Close Button -->
                <button id="close-assessment-modal" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <h2 class="text-2xl text-accent mb-6">Daily Mental Health Assessment</h2>
                
                <!-- Progress Bar -->
                <div class="w-full bg-white/10 rounded-full h-2 mb-6">
                    <div id="assessment-progress" class="bg-accent h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>

                <!-- Section Title -->
                <h3 id="section-title" class="text-xl text-primary mb-4"></h3>

                <!-- Cards Container -->
                <div id="assessment-sections" class="relative">
                    <?php foreach ($assessmentSections as $section => $questions): ?>
                        <div class="section bg-white/5 p-6 rounded-xl hidden" data-section="<?= htmlspecialchars($section) ?>">
                            <div class="space-y-6">
                                <?php foreach ($questions as $number => $question): ?>
                                    <div class="flex items-center space-x-4">
                                        <div class="answer-buttons flex space-x-2">
                                            <button class="answer-btn yes-btn w-8 h-8 rounded-full border-2 border-green-500 hover:bg-green-500 hover:text-white transition-colors <?= isset($todayAssessment['answers'][$section][$number]) && $todayAssessment['answers'][$section][$number] ? 'bg-green-500 text-white' : 'text-green-500' ?>"
                                                    data-section="<?= htmlspecialchars($section) ?>"
                                                    data-question="<?= $number ?>"
                                                    data-value="1">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="answer-btn no-btn w-8 h-8 rounded-full border-2 border-red-500 hover:bg-red-500 hover:text-white transition-colors <?= isset($todayAssessment['answers'][$section][$number]) && !$todayAssessment['answers'][$section][$number] ? 'bg-red-500 text-white' : 'text-red-500' ?>"
                                                    data-section="<?= htmlspecialchars($section) ?>"
                                                    data-question="<?= $number ?>"
                                                    data-value="0">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <span class="text-ghost"><?= htmlspecialchars($question) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex justify-between mt-8">
                                <button class="nav-btn prev-section btn-pixel text-white px-6 py-2 rounded-lg transition-colors <?= $section === array_key_first($assessmentSections) ? 'invisible' : '' ?>">
                                    <i class="fas fa-chevron-left mr-2"></i> Previous
                                </button>
                                <?php if ($section === array_key_last($assessmentSections)): ?>
                                    <button id="submit-assessment" class="btn-pixel text-white px-6 py-2 rounded-lg transition-colors">
                                        Submit Assessment
                                    </button>
                                <?php else: ?>
                                    <button class="nav-btn next-section btn-pixel text-white px-6 py-2 rounded-lg transition-colors">
                                        Next <i class="fas fa-chevron-right ml-2"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Feedback Display -->
        <div id="assessment-feedback" class="mt-8 mb-4 p-4 rounded-xl bg-white/5 <?= isset($todayAssessment) ? '' : 'hidden' ?>">
            <h3 class="text-xl text-primary mb-4">Today's Assessment</h3>
            <?php if (isset($todayAssessment)): ?>
                <p class="text-white text-xl mb-4"><?= htmlspecialchars($todayAssessment['feedback']) ?></p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-white">
                    <?php 
                    $sections = ['sleep_rest', 'body_energy', 'emotions_mood', 'social_support', 
                            'mind_focus', 'self_care', 'red_flags'];
                    foreach ($sections as $section):
                        $score = $todayAssessment["{$section}_score"];
                        $color = $score >= 80 ? 'text-green-400' : 
                                ($score >= 60 ? 'text-yellow-400' : 'text-red-400');
                    ?>
                        <div class="text-center p-3 bg-white/5 rounded-lg">
                            <div class="font-bold mb-1"><?= ucwords(str_replace('_', ' ', $section)) ?></div>
                            <div class="<?= $color ?> text-2xl font-bold"><?= $score ?>%</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Morning Checklist -->
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col items-center border border-white/10">
                <h2 class="font-dunkin text-xl text-yellow-400 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0,0,256,256" class="mr-2"> 
                        <g fill="currentColor" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(5.12,5.12)"><path d="M24.90625,3.96875c-0.04297,0.00781 -0.08594,0.01953 -0.125,0.03125c-0.46484,0.10547 -0.79297,0.52344 -0.78125,1v6c-0.00391,0.35938 0.18359,0.69531 0.49609,0.87891c0.3125,0.17969 0.69531,0.17969 1.00781,0c0.3125,-0.18359 0.5,-0.51953 0.49609,-0.87891v-6c0.01172,-0.28906 -0.10547,-0.56641 -0.3125,-0.76172c-0.21094,-0.19922 -0.49609,-0.29687 -0.78125,-0.26953zM10.65625,9.84375c-0.375,0.06641 -0.67578,0.33984 -0.78125,0.70313c-0.10547,0.36719 0.00391,0.75781 0.28125,1.01563l4.25,4.25c0.24219,0.29688 0.62891,0.43359 1.00391,0.34766c0.37109,-0.08594 0.66406,-0.37891 0.75,-0.75c0.08594,-0.375 -0.05078,-0.76172 -0.34766,-1.00391l-4.25,-4.25c-0.20703,-0.22266 -0.50781,-0.33594 -0.8125,-0.3125c-0.03125,0 -0.0625,0 -0.09375,0zM39.03125,9.84375c-0.22656,0.03125 -0.4375,0.14453 -0.59375,0.3125l-4.25,4.25c-0.29687,0.24219 -0.43359,0.62891 -0.34766,1.00391c0.08594,0.37109 0.37891,0.66406 0.75,0.75c0.375,0.08594 0.76172,-0.05078 1.00391,-0.34766l4.25,-4.25c0.3125,-0.29687 0.40234,-0.76172 0.21875,-1.15234c-0.1875,-0.39453 -0.60156,-0.62109 -1.03125,-0.56641zM24.90625,15c-0.03125,0.00781 -0.0625,0.01953 -0.09375,0.03125c-0.0625,0.00391 -0.125,0.01563 -0.1875,0.03125c-0.01172,0.01172 -0.01953,0.01953 -0.03125,0.03125c-5.30469,0.22656 -9.59375,4.54688 -9.59375,9.90625c0,5.50391 4.49609,10 10,10c5.50391,0 10,-4.49609 10,-10c0,-5.33984 -4.25391,-9.64453 -9.53125,-9.90625c-0.03516,0 -0.05859,-0.03125 -0.09375,-0.03125c-0.10156,-0.03906 -0.20703,-0.05859 -0.3125,-0.0625c-0.01953,0 -0.04297,0 -0.0625,0c-0.03125,0 -0.0625,0 -0.09375,0zM24.9375,17c0.01953,0 0.04297,0 0.0625,0c0.03125,0 0.0625,0 0.09375,0c4.375,0.05078 7.90625,3.61328 7.90625,8c0,4.42188 -3.57812,8 -8,8c-4.41797,0 -8,-3.57812 -8,-8c0,-4.39844 3.54688,-7.96484 7.9375,-8zM4.71875,24c-0.55078,0.07813 -0.9375,0.58984 -0.85937,1.14063c0.07813,0.55078 0.58984,0.9375 1.14063,0.85938h6c0.35938,0.00391 0.69531,-0.18359 0.87891,-0.49609c0.17969,-0.3125 0.17969,-0.69531 0,-1.00781c-0.18359,-0.3125 -0.51953,-0.5 -0.87891,-0.49609h-6c-0.03125,0 -0.0625,0 -0.09375,0c-0.03125,0 -0.0625,0 -0.09375,0c-0.03125,0 -0.0625,0 -0.09375,0zM38.71875,24c-0.55078,0.07813 -0.9375,0.58984 -0.85937,1.14063c0.07813,0.55078 0.58984,0.9375 1.14063,0.85938h6c0.35938,0.00391 0.69531,-0.18359 0.87891,-0.49609c0.17969,-0.3125 0.17969,-0.69531 0,-1.00781c-0.18359,-0.3125 -0.51953,-0.5 -0.87891,-0.49609h-6c-0.03125,0 -0.0625,0 -0.09375,0c-0.03125,0 -0.0625,0 -0.09375,0c-0.03125,0 -0.0625,0 -0.09375,0zM15,33.875c-0.22656,0.03125 -0.4375,0.14453 -0.59375,0.3125l-4.25,4.25c-0.29687,0.24219 -0.43359,0.62891 -0.34766,1.00391c0.08594,0.37109 0.37891,0.66406 0.75,0.75c0.375,0.08594 0.76172,-0.05078 1.00391,-0.34766l4.25,-4.25c0.29688,-0.28516 0.38672,-0.72656 0.22656,-1.10547c-0.15625,-0.37891 -0.53516,-0.62109 -0.94531,-0.61328c-0.03125,0 -0.0625,0 -0.09375,0zM34.6875,33.875c-0.375,0.06641 -0.67578,0.33984 -0.78125,0.70313c-0.10547,0.36719 0.00391,0.75781 0.28125,1.01563l4.25,4.25c0.24219,0.29688 0.62891,0.43359 1.00391,0.34766c0.37109,-0.08594 0.66406,-0.37891 0.75,-0.75c0.08594,-0.375 -0.05078,-0.76172 -0.34766,-1.00391l-4.25,-4.25c-0.1875,-0.19922 -0.44531,-0.30859 -0.71875,-0.3125c-0.03125,0 -0.0625,0 -0.09375,0c-0.03125,0 -0.0625,0 -0.09375,0zM24.90625,37.96875c-0.04297,0.00781 -0.08594,0.01953 -0.125,0.03125c-0.46484,0.10547 -0.79297,0.52344 -0.78125,1v6c-0.00391,0.35938 0.18359,0.69531 0.49609,0.87891c0.3125,0.17969 0.69531,0.17969 1.00781,0c0.3125,-0.18359 0.5,-0.51953 0.49609,-0.87891v-6c0.01172,-0.28906 -0.10547,-0.56641 -0.3125,-0.76172c-0.21094,-0.19922 -0.49609,-0.29687 -0.78125,-0.26953z"></path></g></g>
                    </svg>
                    Morning
                </h2>
                <ul class="w-full">
                    <?php if (!empty($data['morningChecklist'])): ?>
                        <?php foreach ($data['morningChecklist'] as $item): ?>
                            <?php 
                            $statusClass = '';
                            $statusIcon = '';
                            $statusText = '';
                            
                            if ($item['is_missed']) {
                                $statusClass = 'bg-red-100/10 hover:bg-red-200/20 border-l-4 border-red-400';
                                $statusIcon = '<svg class="w-4 h-4 text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $statusText = 'MISSED';
                            } elseif ($item['is_completed']) {
                                $statusClass = 'bg-green-100/10 hover:bg-green-200/20 border-l-4 border-green-400';
                                $statusIcon = '<svg class="w-4 h-4 text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                $statusText = 'COMPLETED';
                            } else {
                                $statusClass = 'bg-pink-100/10 hover:bg-pink-200/20';
                                $statusIcon = '<svg class="w-4 h-4 text-yellow-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $statusText = 'PENDING';
                            }
                            ?>
                            <li class="flex items-center justify-between mb-3 p-3 rounded-xl <?= $statusClass ?> transition">
                                <div class="flex-grow mr-4">
                                    <div class="flex items-center mb-1">
                                        <?= $statusIcon ?>
                                        <span class="text-xs font-bold <?= $item['is_missed'] ? 'text-red-300' : ($item['is_completed'] ? 'text-green-300' : 'text-yellow-300') ?> mr-2">
                                            <?= $statusText ?>
                                        </span>
                                    </div>
                                    <span class="font-dunkin font-semibold text-pink-100 cursor-pointer hover:underline edit-task-btn block" 
                                        data-item-id="<?= $item['id'] ?>"
                                        data-task-text="<?= htmlspecialchars($item['task']) ?>"
                                        data-due-date="<?= $item['due_date'] ?>">
                                        <?= htmlspecialchars($item['task']) ?>
                                    </span>
                                    <small class="text-pink-200/70 font-dunkin">
                                        <?= date('M j, g:i A', strtotime($item['due_date'])) ?>
                                        <?php if ($item['is_completed'] && $item['completed_at']): ?>
                                            <span class="text-green-300 ml-2">✓ Done: <?= date('g:i A', strtotime($item['completed_at'])) ?></span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <input type="checkbox" 
                                       class="checklist-checkbox form-checkbox h-5 w-5 text-pink-500 rounded-full bg-gray-700 border-gray-600 focus:ring-pink-600"
                                       data-item-id="<?= $item['id'] ?>"
                                       <?= $item['done'] ? 'checked' : '' ?>
                                       <?= $item['is_missed'] ? 'disabled title="Cannot complete missed items"' : '' ?>>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center text-gray-400 p-4">No morning checklist items found.</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Night Checklist -->
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl shadow-lg p-6 flex flex-col items-center border border-white/10">
                <h2 class="font-dunkin text-xl text-blue-400 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z" /></svg>
                    Night
                </h2>
                <ul class="w-full">
                    <?php if (!empty($data['nightChecklist'])): ?>
                        <?php foreach ($data['nightChecklist'] as $item): ?>
                            <?php 
                            $statusClass = '';
                            $statusIcon = '';
                            $statusText = '';
                            
                            if ($item['is_missed']) {
                                $statusClass = 'bg-red-100/10 hover:bg-red-200/20 border-l-4 border-red-400';
                                $statusIcon = '<svg class="w-4 h-4 text-red-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $statusText = 'MISSED';
                            } elseif ($item['is_completed']) {
                                $statusClass = 'bg-green-100/10 hover:bg-green-200/20 border-l-4 border-green-400';
                                $statusIcon = '<svg class="w-4 h-4 text-green-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                                $statusText = 'COMPLETED';
                            } else {
                                $statusClass = 'bg-blue-100/10 hover:bg-blue-200/20';
                                $statusIcon = '<svg class="w-4 h-4 text-yellow-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                $statusText = 'PENDING';
                            }
                            ?>
                            <li class="flex items-center justify-between mb-3 p-3 rounded-xl <?= $statusClass ?> transition">
                                <div class="flex-grow mr-4">
                                    <div class="flex items-center mb-1">
                                        <?= $statusIcon ?>
                                        <span class="text-xs font-bold <?= $item['is_missed'] ? 'text-red-300' : ($item['is_completed'] ? 'text-green-300' : 'text-yellow-300') ?> mr-2">
                                            <?= $statusText ?>
                                        </span>
                                    </div>
                                    <span class="font-dunkin font-semibold text-blue-100 cursor-pointer hover:underline edit-task-btn block" 
                                        data-item-id="<?= $item['id'] ?>"
                                        data-task-text="<?= htmlspecialchars($item['task']) ?>"
                                        data-due-date="<?= $item['due_date'] ?>">
                                        <?= htmlspecialchars($item['task']) ?>
                                    </span>
                                    <small class="text-blue-200/70 font-porky">
                                        <?= date('M j, g:i A', strtotime($item['due_date'])) ?>
                                        <?php if ($item['is_completed'] && $item['completed_at']): ?>
                                            <span class="text-green-300 ml-2">✓ Done: <?= date('g:i A', strtotime($item['completed_at'])) ?></span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <input type="checkbox"
                                       class="checklist-checkbox form-checkbox h-5 w-5 text-blue-500 rounded-full bg-gray-700 border-gray-600 focus:ring-blue-600"
                                       data-item-id="<?= $item['id'] ?>"
                                       <?= $item['done'] ? 'checked' : '' ?>
                                       <?= $item['is_missed'] ? 'disabled title="Cannot complete missed items"' : '' ?>>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center text-gray-400 p-4">No night checklist items found.</li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </main>
</div>

<?php
include __DIR__ . '/../moodTracker/moodLogModal.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.checklist-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const isChecked = this.checked;

                this.disabled = true;

                fetch('<?= BASE_URL ?>/checklist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        itemId: itemId,
                        isChecked: isChecked
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        this.checked = !isChecked; 
                        console.error('Failed to update checklist item.');
                        showFlashMessage('Oops! Something went wrong. Please try again.', 'error');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Checklist item updated successfully.');
                    }
                })
                .catch(error => {
                    this.checked = !isChecked;
                    console.error('Error:', error);
                    showFlashMessage('A network error occurred. Please check your connection.');
                })
                .finally(() => {
                    this.disabled = false;
                });
            });
        });
    });
</script>

<button id="open-add-modal-btn" class="fixed bottom-8 right-8 bg-accent hover:bg-pink-400 text-white rounded-full p-4 shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 focus:ring-offset-darkbg transition-transform hover:scale-110">
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
</button>

<!-- Add Checklist Item Modal -->
<div id="add-checklist-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm flex justify-center items-center hidden z-[50]">
    <div class="bg-darkbg border border-primary/50 rounded-lg p-8 w-full max-w-md shadow-2xl relative">
        
        <button id="close-add-modal-btn" class="absolute top-4 right-4 text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h2 class="font-dunkin text-2xl text-accent mb-6">Add New Self Care Item</h2>
        
        <form id="add-checklist-form">
            <!-- Task Text Input -->
            <div class="mb-5">
                <label for="task-text" class="block mb-1 text-secondary font-dunkin text-lg">Task Description</label>
                <input type="text" id="task-text" name="task_text" class="input-pixel" placeholder="e.g., Meditate for 10 minutes" required>
            </div>

            <!-- Due Date & Time Selector -->
            <div class="mb-8">
                <label class="block mb-1 text-secondary font-dunkin text-lg">Due Date & Time</label>
                <input type="datetime-local" id="due-date" name="due_date" class="input-pixel" required>
                <small class="text-gray-400 font-porky mt-1 block">
                    Times before 5:00 PM will be categorized as Morning, after 5:00 PM as Night
                </small>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-pixel w-full">
                [ Add Item ]
            </button>
        </form>
    </div>
</div>

<!-- Edit Checklist Item Modal -->
<div id="edit-checklist-modal" class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm flex justify-center items-center hidden z-[50]">
    <div class="bg-darkbg border border-primary/50 rounded-lg p-8 w-full max-w-md shadow-2xl relative">
        
        <button id="close-edit-modal-btn" class="absolute top-4 right-4 text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h2 class="font-dunkin text-2xl text-accent mb-6">Edit Self Care Item</h2>
        
        <form id="edit-checklist-form">
            <input type="hidden" id="edit-item-id" name="item_id">

            <!-- Task Text Input -->
            <div class="mb-5">
                <label for="edit-task-text" class="block mb-1 text-secondary font-dunkin text-lg">Task Description</label>
                <input type="text" id="edit-task-text" name="task_text" class="input-pixel" required>
            </div>

            <!-- Due Date & Time Selector -->
            <div class="mb-8">
                <label class="block mb-1 text-secondary font-dunkin text-lg">Due Date & Time</label>
                <input type="datetime-local" id="edit-due-date" name="due_date" class="input-pixel" required>
                <small class="text-gray-400 font-porky mt-1 block">
                    Times before 5:00 PM will be categorized as Morning, after 5:00 PM as Night
                </small>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4">
                <!-- Delete Button -->
                <button type="button" id="delete-item-btn" class="btn-pixel !bg-red-600/80 !border-red-800 hover:!bg-red-600 w-1/3">
                    Delete 
                </button>
                <!-- Update Button -->
                <button type="submit" class="btn-pixel w-2/3">
                    Save Changes 
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openModalBtn = document.getElementById('open-add-modal-btn');
        const closeModalBtn = document.getElementById('close-add-modal-btn');
        const modal = document.getElementById('add-checklist-modal');
        const form = document.getElementById('add-checklist-form');
        
        const openModal = () => {
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            tomorrow.setHours(8, 0, 0, 0);
            
            const localDateTime = tomorrow.getFullYear() + '-' + 
                String(tomorrow.getMonth() + 1).padStart(2, '0') + '-' + 
                String(tomorrow.getDate()).padStart(2, '0') + 'T' + 
                String(tomorrow.getHours()).padStart(2, '0') + ':' + 
                String(tomorrow.getMinutes()).padStart(2, '0');
            
            document.getElementById('due-date').value = localDateTime;
            modal.classList.remove('hidden');
        };
        const closeModal = () => modal.classList.add('hidden');

        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        form.addEventListener('submit', function(event) {
            event.preventDefault(); 

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Adding...';

            fetch('<?= BASE_URL ?>/checklist/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlashMessage('Self-care item added successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showFlashMessage('Error: ' + (data.message || 'Could not add item.'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('A network error occurred.', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = '[ Add Item ]';
            });
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const editModal = document.getElementById('edit-checklist-modal');
        const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
        const editForm = document.getElementById('edit-checklist-form');
        const deleteBtn = document.getElementById('delete-item-btn');

        const editItemIdInput = document.getElementById('edit-item-id');
        const editTaskTextInput = document.getElementById('edit-task-text');
        const editDueDateInput = document.getElementById('edit-due-date');

        const openEditModal = (id, text, dueDate) => {
            editItemIdInput.value = id;
            editTaskTextInput.value = text;
            
            const date = new Date(dueDate);
            const localDateTime = date.getFullYear() + '-' + 
                String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                String(date.getDate()).padStart(2, '0') + 'T' + 
                String(date.getHours()).padStart(2, '0') + ':' + 
                String(date.getMinutes()).padStart(2, '0');
            editDueDateInput.value = localDateTime;
            editModal.classList.remove('hidden');
        };

        const closeEditModal = () => editModal.classList.add('hidden');

        document.querySelectorAll('.edit-task-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); 
                const id = this.dataset.itemId;
                const text = this.dataset.taskText;
                const dueDate = this.dataset.dueDate;
                openEditModal(id, text, dueDate);
            });
        });

        closeEditModalBtn.addEventListener('click', closeEditModal);
        editModal.addEventListener('click', e => (e.target === editModal) && closeEditModal());

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('<?= BASE_URL ?>/checklist/update', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFlashMessage('Item updated successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showFlashMessage('Failed to save changes.', 'error');
                }
            });
        });

        deleteBtn.addEventListener('click', function() {
            const itemId = editItemIdInput.value;
            if (!confirm('Are you sure you want to permanently delete this item?')) {
                return;
            }

            fetch('<?= BASE_URL ?>/checklist/delete', {
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ itemId: itemId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showFlashMessage('Item deleted successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showFlashMessage('Failed to delete item.', 'error');
                }
            });
        });
    });

    
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
</script>

<script>
// Mental Health Assessment
document.addEventListener('DOMContentLoaded', () => {
    const answers = {};
    const modal = document.getElementById('assessment-modal');
    const startButton = document.getElementById('start-assessment');
    const closeButton = document.getElementById('close-assessment-modal');
    const answerButtons = document.querySelectorAll('.answer-btn');
    const submitButton = document.getElementById('submit-assessment');
    const feedbackDisplay = document.getElementById('assessment-feedback');
    const progressBar = document.getElementById('assessment-progress');
    const sectionTitle = document.getElementById('section-title');
    let currentSectionIndex = 0;
    const sections = Array.from(document.querySelectorAll('.section'));

    // Check if assessment is needed today
    const checkAssessmentNeeded = () => {
        <?php if ($assessmentNeeded): ?>
            setTimeout(() => {
                modal.classList.remove('hidden');
                showCurrentSection();
            }, 1000); // Show modal after 1 second delay
        <?php endif; ?>
    };

    // Show current section
    const showCurrentSection = () => {
        sections.forEach((section, index) => {
            section.classList.toggle('hidden', index !== currentSectionIndex);
        });

        // Update progress bar
        const progress = ((currentSectionIndex + 1) / sections.length) * 100;
        progressBar.style.width = `${progress}%`;

        // Update section title
        const currentSection = sections[currentSectionIndex];
        const sectionName = currentSection.dataset.section;
        sectionTitle.textContent = sectionName.replace('_', ' ').split(' ').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    };

    // Navigation functions
    const nextSection = () => {
        if (currentSectionIndex < sections.length - 1) {
            currentSectionIndex++;
            showCurrentSection();
        }
    };

    const prevSection = () => {
        if (currentSectionIndex > 0) {
            currentSectionIndex--;
            showCurrentSection();
        }
    };

    // Modal controls
    startButton.addEventListener('click', () => {
        modal.classList.remove('hidden');
        currentSectionIndex = 0;
        showCurrentSection();
    });

    closeButton.addEventListener('click', () => {
        if (Object.keys(answers).length === 0 || confirm('Are you sure you want to close? Your progress will be lost.')) {
            modal.classList.add('hidden');
            // Reset answers and progress
            Object.keys(answers).forEach(key => delete answers[key]);
            currentSectionIndex = 0;
            showCurrentSection();
        }
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            if (confirm('Are you sure you want to close? Your progress will be lost.')) {
                modal.classList.add('hidden');
            }
        }
    });

    // Navigation button listeners
    document.querySelectorAll('.nav-btn.next-section').forEach(btn => {
        btn.addEventListener('click', () => {
            const currentSection = sections[currentSectionIndex];
            const sectionName = currentSection.dataset.section;
            const questionsInSection = currentSection.querySelectorAll('.answer-btn.yes-btn').length;
            const answeredQuestions = Object.keys(answers[sectionName] || {}).length;

            if (answeredQuestions < questionsInSection) {
                showFlashMessage('Please answer all questions in this section before proceeding.', 'error');
                return;
            }
            nextSection();
        });
    });

    document.querySelectorAll('.nav-btn.prev-section').forEach(btn => {
        btn.addEventListener('click', prevSection);
    });

    // Initialize the assessment if needed
    checkAssessmentNeeded();

    // Initialize answers object with existing answers if any (only from today)
    document.querySelectorAll('.section').forEach(section => {
        const sectionName = section.dataset.section;
        answers[sectionName] = {};

        // Only initialize with today's answers, not previous days
        section.querySelectorAll('.answer-btn.bg-green-500, .answer-btn.bg-red-500').forEach(activeBtn => {
            const questionNum = parseInt(activeBtn.dataset.question);
            answers[sectionName][questionNum] = activeBtn.classList.contains('yes-btn');
        });
    });

    answerButtons.forEach(button => {
        button.addEventListener('click', function() {
            const section = this.dataset.section;
            const question = parseInt(this.dataset.question);
            const value = this.dataset.value === '1';
            const siblingButton = this.classList.contains('yes-btn') ? 
                this.nextElementSibling : 
                this.previousElementSibling;

            // Initialize section in answers if it doesn't exist
            answers[section] = answers[section] || {};

            // Handle button toggling
            const wasActive = this.classList.contains('active');

            // Reset both buttons first
            [this, siblingButton].forEach(btn => {
                btn.classList.remove('bg-green-500', 'bg-red-500', 'text-white', 'active');
            });

            if (!wasActive) {
                // Activate this button
                this.classList.add(value ? 'bg-green-500' : 'bg-red-500', 'text-white', 'active');
                answers[section][question] = value;
            } else {
                // If clicking active button, clear answer
                delete answers[section][question];
            }

            // Log current state for debugging
            console.log('Updated answers:', {
                section: section,
                question: question,
                value: value,
                currentAnswers: JSON.parse(JSON.stringify(answers))
            });
        });
    });

    submitButton.addEventListener('click', function() {
        // Disable submit button to prevent double submission
        submitButton.disabled = true;
        submitButton.innerHTML = 'Submitting...';

        // Validate that all questions in all sections are answered
        let unansweredSections = [];
        sections.forEach(section => {
            const sectionName = section.dataset.section;
            const questionCount = section.querySelectorAll('.answer-btn.yes-btn').length;
            const answeredCount = Object.keys(answers[sectionName] || {}).length;
            
            if (answeredCount < questionCount) {
                unansweredSections.push(sectionName.replace('_', ' '));
            }
        });

        if (unansweredSections.length > 0) {
            showFlashMessage(
                'Please answer all questions in sections: ' + 
                unansweredSections.map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(', '),
                'error'
            );
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit Assessment';
            return;
        }

        // Validate answers object is not empty
        if (Object.keys(answers).length === 0) {
            showFlashMessage('Please answer at least one question before submitting.', 'error');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit Assessment';
            return;
        }

        // Submit assessment
        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';

        fetch('<?= BASE_URL ?>/care/saveAssessment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ answers: answers })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close the modal and show success message
                modal.classList.add('hidden');
                showFlashMessage('Assessment saved successfully!', 'success');
                
                // Update the feedback section
                feedbackDisplay.classList.remove('hidden');
                const assessment = data.assessment;
                
                // Update scores and feedback display
                setTimeout(() => {
                    location.reload(); // Reload to show updated assessment
                }, 1500);
            } else {
                showFlashMessage('Failed to save assessment: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            showFlashMessage('An error occurred while saving the assessment', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit Assessment';
        });
    });
});
</script>

</body>
</html>