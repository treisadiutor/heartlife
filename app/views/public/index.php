<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartLife - Your Daily Wellness Companion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg h-screen w-screen overflow-hidden font-porky text-ghost pixel-bg" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-position: center; backdrop-filter: blur(15px);">
    <div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex"></div>
    <div class="flex flex-col justify-between items-center w-full h-full p-10">

        <header class="text-center mt-10">
            <div class="flex items-center justify-center gap-4 mb-2">
                <img src="<?= BASE_URL ?>/assets/images/logo.svg" alt="Heart Icon" class="w-16 h-16">
                <h1 class="font-dunkin text-5xl md:text-6xl text-accent drop-shadow-[4px_4px_0_#4a5588] transition-transform duration-200 hover:-translate-y-1">
                    HeartLife
                </h1>
            </div>
            <p class="font-cloudyday text-2xl md:text-3xl text-ghost drop-shadow-[2px_2px_0_#4a5588]">
                Your All-in-One Daily Wellness Companion
            </p>
        </header>

        <main class="w-full max-w-4xl">
            <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 md:gap-6">
                <div class="feature-item">
                    <div class="pixel-icon icon-sleep"></div>
                    <span class="feature-label">Sleep Monitor</span>
                </div>

                <div class="feature-item">
                    <div class="pixel-icon icon-notes"></div>
                    <span class="feature-label">Notes</span>
                </div>

                <div class="feature-item">
                    <div class="pixel-icon icon-checklist"></div>
                    <span class="feature-label">Checklist</span>
                </div>

                <div class="feature-item">
                    <div class="pixel-icon icon-mood"></div>
                    <span class="feature-label">Mood Tracker</span>
                </div>
                
                <div class="feature-item">
                    <div class="pixel-icon icon-bmi"></div>
                    <span class="feature-label">BMI Tracker</span>
                </div>

                <div class="feature-item">
                    <div class="pixel-icon icon-quotes"></div>
                    <span class="feature-label">Daily Quotes</span>
                </div>

            </div>
        </main>

        <footer class="mt-6  mb-10">
            <button class="btn-pixel" onclick="window.location.href='<?= BASE_URL ?>/login';">
                [ Get Started ]
            </button>
        </footer>
    </div>

</body>
</html>