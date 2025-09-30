<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HeartLife - Sign Up</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <?php include __DIR__ . '/../components/tailwind.php'; ?>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/styles.css">
</head>

<body class="bg-darkbg h-screen w-screen overflow-hidden text-ghost" style="background-image: url('<?= BASE_URL ?>/assets/images/background.jpg'); background-size: cover; background-position: center; backdrop-filter: blur(15px);">
<div class="absolute inset-0 bg-black bg-opacity-50 z-[-1]"></div>

  <!-- Flash Message Container -->
  <div id="flash-message" class="fixed top-4 right-4 z-50 hidden max-w-sm w-full bg-primary/90 border-2 border-primary text-ghost px-6 py-4 rounded-lg shadow-2xl backdrop-blur-sm transform translate-x-full transition-all duration-300 ease-out">
    <div class="flex items-start">
      <div class="flex-1">
        <div id="flash-title" class="font-dunkin text-lg mb-1"></div>
        <div id="flash-text" class="font-porky text-sm opacity-90"></div>
      </div>
      <button onclick="removeFlashMessage()" class="ml-4 text-ghost hover:text-accent transition-colors duration-200 font-bold text-xl leading-none">&times;</button>
    </div>
  </div>

  <div class="flex justify-center items-center w-full h-full p-5">

    <!-- Signup Form Card -->
    <form 
      class="w-full max-w-lg bg-black/40 backdrop-blur-sm p-8 rounded-lg border-2 border-primary/30 shadow-2xl"
      action="<?= BASE_URL ?>/handle_signup" 
      method="POST"
    >

      <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
      ?>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-500/80 border border-red-700 text-white px-4 py-3 rounded-md relative mb-6" role="alert">
          <strong class="font-bold font-dunkin">Error!</strong>
          <span class="block sm:inline font-porky"><?= htmlspecialchars($_SESSION['error']); ?></span>
        </div>
        <?php unset($_SESSION['error']);?>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-500/80 border border-green-700 text-white px-4 py-3 rounded-md relative mb-6" role="alert">
          <strong class="font-bold font-dunkin">Success!</strong>
          <span class="block sm:inline font-porky"><?= htmlspecialchars($_SESSION['success']); ?></span>
        </div>
        <?php unset($_SESSION['success']);?>
      <?php endif; ?>

      <!-- Form Header -->
      <div class="text-center mb-8">
        <h1 class="font-cloudyday text-3xl text-primary drop-shadow-[2px_2px_0_#4a5588]">
          CREATE AN ACCOUNT
        </h1>
        <p class="font-cloudyday text-lg text-ghost mt-2">
          Start your wellness journey
        </p>
      </div>

      <!-- Username Input -->
      <div class="mb-5">
        <label for="username" class="block mb-1 text-secondary font-dunkin text-lg">Username</label>
        <input 
          type="text" 
          id="username" 
          name="username"
          class="input-pixel" 
          placeholder="Your user name..."
          required
        >
      </div>

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
      <div class="mb-5">
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

      <!-- Confirm Password Input -->
      <div class="mb-5">
        <label for="confirm_password" class="block mb-1 text-secondary font-dunkin text-lg">Confirm Password</label>
        <div class="relative">
          <input 
            type="password" 
            id="confirm_password" 
            name="confirm_password"
            class="input-pixel" 
            placeholder="************"
            required
          >

          <span id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer">
              <!-- The eye icon will be injected here by JS -->
          </span>

        </div>
      </div>

      <!-- Date of Birth Input -->
      <div class="mb-8">
        <label for="date_of_birth" class="block mb-1 text-secondary font-dunkin text-lg">Date of Birth (Optional)</label>
        <input 
          type="date" 
          id="date_of_birth" 
          name="date_of_birth"
          class="input-pixel" 
          max="<?= date('Y-m-d', strtotime('-13 years')) ?>"
        >
        <p class="text-sm text-secondary/70 mt-1 font-dunkin">This helps us provide personalized health and sleep recommendations</p>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn-pixel w-full">
        [ Create Account ]
      </button>

      <!-- Link to Login Page -->
      <p class="font-dunkin mt-4 text-center">Already have an account? <span class="text-accent hover:underline"><a href="<?= BASE_URL ?>/login">Sign in</a></span></p>

    </form>
      
  </div>

  <script>
    function showFlashMessage(title, message, type = 'default') {
      const flashContainer = document.getElementById('flash-message');
      const flashTitle = document.getElementById('flash-title');
      const flashText = document.getElementById('flash-text');
      
      flashTitle.textContent = title;
      flashText.textContent = message;
      flashContainer.classList.remove('bg-primary/90', 'border-primary', 'bg-green-500/90', 'border-green-400', 'bg-red-500/90', 'border-red-400', 'bg-yellow-500/90', 'border-yellow-400');
      
      switch(type) {
        case 'success':
          flashContainer.classList.add('bg-green-500/90', 'border-green-400');
          break;
        case 'error':
          flashContainer.classList.add('bg-red-500/90', 'border-red-400');
          break;
        case 'warning':
          flashContainer.classList.add('bg-yellow-500/90', 'border-yellow-400');
          break;
        default:
          flashContainer.classList.add('bg-primary/90', 'border-primary');
      }
      
      flashContainer.classList.remove('hidden');
      setTimeout(() => {
        flashContainer.classList.remove('translate-x-full');
        flashContainer.classList.add('translate-x-0');
      }, 10);
      
      setTimeout(() => {
        removeFlashMessage();
      }, 4000);
    }
    
    function removeFlashMessage() {
      const flashContainer = document.getElementById('flash-message');
      flashContainer.classList.remove('translate-x-0');
      flashContainer.classList.add('translate-x-full');
      
      setTimeout(() => {
        flashContainer.classList.add('hidden');
      }, 300);
    }

    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    
    const confirmPasswordInput = document.getElementById('confirm_password');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

    const eyeIconSVG = `<svg class="w-6 h-6 text-accent hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
    const eyeSlashIconSVG = `<svg class="w-6 h-6 text-accent hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.111 2.458.322M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18"></path></svg>`;

    togglePassword.innerHTML = eyeIconSVG;
    toggleConfirmPassword.innerHTML = eyeIconSVG;

    function setupToggler(toggleElement, inputElement) {
      toggleElement.addEventListener('click', function () {
        const type = inputElement.getAttribute('type') === 'password' ? 'text' : 'password';
        inputElement.setAttribute('type', type);
        this.innerHTML = type === 'password' ? eyeIconSVG : eyeSlashIconSVG;
      });
    }

    setupToggler(togglePassword, passwordInput);
    setupToggler(toggleConfirmPassword, confirmPasswordInput);

  </script>
</body>
</html>