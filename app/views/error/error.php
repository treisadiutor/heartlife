<?php
$error_code = $error_code ?? 404;

http_response_code($error_code);

$title = "An Unexpected Error Occurred";
$message = "Something went wrong on our end. We've been notified and are looking into it.";
$icon_class = "fa-solid fa-triangle-exclamation";

switch ($error_code) {
    case 400: 
        $title = "Mixed Signals";
        $message = "The request didn’t come through clearly, like a fuzzy mood log entry. Try refreshing or going back to adjust and submit again.";
        $icon_class = "fa-solid fa-heart-crack";
        break;
    case 401:
        $title = "Sign-In Needed";
        $message = "You’ll need to log in to continue tracking your wellness journey. Please sign in to your Heartlife account to keep your progress safe and synced.";
        $icon_class = "fa-solid fa-user-lock";
        break;
    case 403:
        $title = "Access Blocked";
        $message = "This area isn’t part of your current Heartlife path. If you believe this is an error, feel free to reach out to our support team.";
        $icon_class = "fa-solid fa-shield-heart";
        break;
    case 404:
        $title = "Lost in the Journey";
        $message = "We couldn’t find the page you’re looking for. Like a missing checklist item, it may have been moved or no longer exists. Try heading back to your dashboard.";
        $icon_class = "fa-solid fa-location-crosshairs";
        break;
    case 429: 
        $title = "Slow Down, Take a Breath";
        $message = "You’ve made too many requests in a short time. Just like self-care, pacing yourself is key. Please pause for a moment before trying again.";
        $icon_class = "fa-solid fa-spa";
        break;
    case 500:
        $title = "Heartbeat Glitch";
        $message = "Our system is feeling a little off-balance. We’re working to restore its rhythm. Please try again shortly.";
        $icon_class = "fa-solid fa-heart-pulse";
        break;
    case 503: 
        $title = "Taking a Wellness Break";
        $message = "Heartlife is currently in maintenance mode, like a self-care reset. We’ll be back online soon — thanks for your patience!";
        $icon_class = "fa-solid fa-bed";
        break;
    default:
        $title = "Unexpected Bump";
        $message = "Something went wrong along the way. Don’t worry — we’ve logged it and will get things back on track.";
        $icon_class = "fa-solid fa-circle-exclamation";
        break;
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= $error_code ?> | Heartlife</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="font-dunkin bg-darkbg h-screen w-screen text-ghost flex flex-col justify-between overflow-hidden" style="background-image: url('<?= BASE_URL ?>/assets/images/background.png'); background-size: cover; background-position: center; background-attachment: fixed; backdrop-filter: blur(15px);" x-data="{ openModal: null }">
    
    <main class="flex items-center justify-center flex-1 w-full px-4 py-8">
<div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

<div class="flex">

    <main class="flex items-center justify-center flex-1 w-full px-4 py-12">
        <div class="w-full max-w-2xl mx-auto bg-opacity-80 rounded-3xl shadow-2xl p-8 flex flex-col items-center border-4 border-accent">
            
            <!-- Error icon -->
            <i class="<?= htmlspecialchars($icon_class) ?> text-6xl md:text-8xl text-accent mb-6 drop-shadow"></i>

            <!-- Error code -->
            <h1 class="text-7xl md:text-9xl font-dunkin font-extrabold text-accent mb-2 leading-none">
                <?= $error_code ?>
            </h1>

            <!-- Error heading -->
            <h2 class="mt-2 text-3xl md:text-4xl font-dunkin font-bold text-primary mb-2">
                <?= htmlspecialchars($title) ?>
            </h2>

            <!-- Error message -->
            <p class="mt-2 text-lg text-white max-w-lg mx-auto font-dunkin">
                <?= htmlspecialchars($message) ?>
            </p>

            <!-- Action Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 w-full">
                <?php if ($error_code == 401): ?>
                    <button 
                        @click="openModal = 'login'"
                        class="w-full sm:w-auto bg-accent text-white font-bold px-8 py-3 rounded-full hover:bg-accent/90 transition-opacity shadow-lg">
                        Login / Sign Up
                    </button>

                    <a href="<?= BASE_URL ?>" class="w-full sm:w-auto border-2 border-accent text-accent font-bold px-8 py-3 rounded-full hover:bg-accent/10 transition-colors">
                        Go to Homepage
                    </a>

                <?php else: ?>
                    <!-- Default buttons for all other errors -->
                    <a href="<?= BASE_URL ?>" class="w-full sm:w-auto bg-accent text-darkbg font-bold px-8 py-3 rounded-full hover:bg-accent/90 transition-opacity shadow-lg">
                        Return to Homepage
                    </a>
                <?php endif; ?>
                
            </div>
            
            <footer class="text-center py-6 text-white/60 font-dunkin">
                &copy; <?= date("Y") ?> HeartLife. All Rights Reserved.
            </footer>
        </div>
    </main>


</body>
</html>