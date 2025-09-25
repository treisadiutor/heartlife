<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartLife - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include __DIR__ . '/../components/tailwind.php'; ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg h-screen w-screen overflow-hidden text-ghost" style="background-image: url('<?= BASE_URL ?>/assets/images/background.png'); background-size: cover; background-position: center; backdrop-filter: blur(15px);">
<div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

    <div class="flex justify-center items-center w-full h-full p-5">

        <form 
            class="w-full max-w-lg bg-black/40 backdrop-blur-sm p-8 rounded-lg border-2 border-primary/30 shadow-2xl"
            action="<?= BASE_URL ?>/handle_login" 
            method="POST"
        >
            <div class="text-center mb-8">
                <h1 class="font-cloudyday text-3xl text-primary drop-shadow-[2px_2px_0_#4a5588]">
                    SIGN IN WITH EMAIL
                </h1>
                <p class="font-cloudyday text-lg text-ghost mt-2">
                    Create a new tracker
                </p>
            </div>

            
            <?php 
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
            ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-500/80 border border-red-700 text-white px-4 py-3 rounded-md relative mb-6" role="alert">
                <strong class="font-bold font-dunkin">Error!</strong>
                <span class="block sm:inline font-dunkin"><?= htmlspecialchars($_SESSION['error']); ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-500/80 border border-green-700 text-white px-4 py-3 rounded-md relative mb-6" role="alert">
                <strong class="font-bold font-dunkin">Success!</strong>
                <span class="block sm:inline font-dunkin"><?= htmlspecialchars($_SESSION['success']); ?></span>
                </div>
                <?php unset($_SESSION['success']);  ?>
            <?php endif; ?>
            
            <!-- Email Input -->
            <div class="mb-5">
                <label for="email" class="block mb-1 text-secondary font-dunkin text-lg">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email"
                    class="input-pixel" 
                    placeholder="example@gmail.com"
                    required
                >
            </div>

            <!-- Password Input -->
            <div class="mb-8">
                <label for="password" class="block mb-1 text-secondary font-dunkin text-lg">Password</label>
                <div class="relative">
                    <input 
                    type="password" 
                    id="password" 
                    name="password"
                    class="input-pixel" 
                    placeholder="************"
                    required
                    >
                    
                    <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
                    <!-- The eye icon will be injected here by JS -->
                    </span>
                </div>
            </div>

            <!-- Forgot Password Link -->
            <!-- Forgot Password Link (currently disabled)
            <div class="text-right mb-8">
                <a href="#" class="text-sm font-dunkin text-accent hover:text-primary hover:underline">
                    Forgot password?
                </a>
            </div>
            -->

            <!-- Submit Button -->
            <button type="submit" class="btn-pixel w-full">
                [ Get Started ]
            </button>

            <p class="font-dunkin mt-2 text-center">Don't have an account? <span class="text-accent"><a href="<?= BASE_URL ?>/signup">Sign up</a></span></p>

        </form>
        
        <!-- Flash Messages Container -->
        <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        const eyeIconSVG = `<svg class="w-6 h-6 text-accent hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        const eyeSlashIconSVG = `<svg class="w-6 h-6 text-accent hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.111 2.458.322M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>`;

        togglePassword.innerHTML = eyeIconSVG;

        togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        this.innerHTML = type === 'password' ? eyeIconSVG : eyeSlashIconSVG;
        });

        // Flash message system
        function showFlashMessage(message, type = 'info') {
            const container = document.getElementById('flash-container');
            const messageId = 'flash-' + Date.now();
            
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
            
            setTimeout(() => {
                flashDiv.classList.remove('translate-x-full');
                flashDiv.classList.add('translate-x-0');
            }, 100);
            
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
</body>
</html>