<?php
require_once 'koneksi.php';
// show message if redirected
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login Admin - ProgressKu</title>
    
    <!-- Tailwind with custom config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        },
                        secondary: {
                            500: '#8b5cf6',
                            600: '#7c3aed',
                        },
                        gradient: {
                            start: '#0ea5e9',
                            end: '#8b5cf6'
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-glow': 'pulseGlow 2s ease-in-out infinite',
                        'shake': 'shake 0.5s ease-in-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 0 0 rgba(14, 165, 233, 0.4)' },
                            '50%': { boxShadow: '0 0 0 10px rgba(14, 165, 233, 0)' }
                        },
                        shake: {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-5px)' },
                            '20%, 40%, 60%, 80%': { transform: 'translateX(5px)' }
                        }
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated background elements */
        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.3;
            z-index: 0;
        }
        
        .blob-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            top: -150px;
            right: -150px;
            animation: float 6s ease-in-out infinite;
        }
        
        .blob-2 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
            bottom: -200px;
            left: -200px;
            animation: float 8s ease-in-out infinite reverse;
        }
        
        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom checkbox */
        .checkbox-custom {
            position: relative;
            cursor: pointer;
        }
        
        .checkbox-custom input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #eee;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .checkbox-custom:hover input ~ .checkmark {
            background-color: #ccc;
        }
        
        .checkbox-custom input:checked ~ .checkmark {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
        }
        
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        
        .checkbox-custom input:checked ~ .checkmark:after {
            display: block;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        /* Input focus effects */
        .input-field {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .input-field:focus {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0ea5e9;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }
        
        .password-toggle:hover {
            color: #0ea5e9;
        }
    </style>
</head>
<body class="animate-fade-in">
    <!-- Background blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    
    <!-- Decorative elements -->
    <div class="fixed top-10 left-10 w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm animate-pulse"></div>
    <div class="fixed bottom-10 right-10 w-6 h-6 rounded-full bg-white/20 backdrop-blur-sm animate-pulse" style="animation-delay: 0.5s"></div>
    <div class="fixed top-1/2 right-1/4 w-4 h-4 rounded-full bg-white/20 backdrop-blur-sm animate-pulse" style="animation-delay: 1s"></div>
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4 relative z-10">
        <div class="w-full max-w-md animate-slide-up">
            <!-- Logo & Brand -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white text-3xl font-bold shadow-xl animate-pulse-glow">
                    <i class="fas fa-lock"></i>
                </div>
                <h1 class="text-3xl font-bold gradient-text mb-2">ProgressKu</h1>
                <p class="text-gray-600">Admin Dashboard Login</p>
            </div>
            
            <!-- Login Card -->
            <div class="glass-effect rounded-2xl p-8">
                <?php if ($error): ?>
                    <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 animate-shake">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-exclamation-circle text-red-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-red-800">Login Gagal</p>
                                <p class="text-sm text-red-600"><?= htmlspecialchars($error) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="mb-6 p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-green-800">Berhasil</p>
                                <p class="text-sm text-green-600"><?= htmlspecialchars($success) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form id="loginForm" action="do_login.php" method="POST" class="space-y-6">
                    <!-- Username Field -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-user text-primary-500"></i>
                            <span>Username</span>
                        </label>
                        <div class="relative">
                            <input id="username" name="username" type="text" required 
                                   class="input-field w-full border border-gray-200 rounded-xl px-4 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Masukkan username Anda"
                                   autocomplete="username">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 ml-1">Gunakan username admin Anda</p>
                    </div>
                    
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-key text-primary-500"></i>
                            <span>Password</span>
                        </label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required 
                                   class="input-field w-full border border-gray-200 rounded-xl px-4 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   placeholder="Masukkan password Anda"
                                   autocomplete="current-password">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 ml-1">Minimal 8 karakter</p>
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="checkbox-custom flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            <span class="text-sm text-gray-700">Ingat saya</span>
                        </label>
                        
                        <a href="#" class="text-sm text-primary-600 hover:text-primary-800 transition-colors">
                            Lupa password?
                        </a>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" id="submitBtn" 
                                class="w-full bg-gradient-to-r from-primary-500 to-secondary-500 text-white font-semibold py-3 px-4 rounded-xl hover:shadow-xl hover:shadow-primary-500/30 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <span id="btnText">Masuk ke Dashboard</span>
                            <div id="btnSpinner" class="spinner hidden mx-auto"></div>
                        </button>
                    </div>
                </form>
                
                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Atau lanjutkan dengan</span>
                    </div>
                </div>
                
                <!-- Social Login (Optional) -->
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" 
                            class="flex items-center justify-center gap-2 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-google text-red-500"></i>
                        <span class="text-sm font-medium">Google</span>
                    </button>
                    
                    <button type="button" 
                            class="flex items-center justify-center gap-2 p-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="fab fa-github text-gray-800"></i>
                        <span class="text-sm font-medium">GitHub</span>
                    </button>
                </div>
                
                <!-- Back to Portfolio -->
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <p class="text-gray-600 text-sm">
                        Kembali ke 
                        <a href="index.php" class="text-primary-600 hover:text-primary-800 font-medium transition-colors">
                            Portfolio
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="mt-6 text-center">
                <p class="text-xs text-white/80">
                    © <?= date('Y') ?> ProgressKu. Hak cipta dilindungi.
                </p>
                <p class="text-xs text-white/60 mt-1">
                    Versi 2.0 • Login aman dengan enkripsi SSL
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const eyeIcon = togglePassword.querySelector('i');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        // Form submission with loading state
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        
        loginForm.addEventListener('submit', function(e) {
            // Simple validation
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                return;
            }
            
            // Show loading state
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.remove('hover:-translate-y-0.5');
            
            // Simulate network delay for demo
            setTimeout(() => {
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.add('hover:-translate-y-0.5');
            }, 2000);
        });
        
        // Auto focus username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
            
            // Add floating animation to logo
            const logo = document.querySelector('.fa-lock').closest('div');
            logo.style.animation = 'float 4s ease-in-out infinite';
            
            // Add input validation styling
            const inputs = document.querySelectorAll('.input-field');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.add('border-primary-300');
                    } else {
                        this.classList.remove('border-primary-300');
                    }
                });
            });
        });
        
        // Add ripple effect to submit button
        submitBtn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+Enter to submit form
            if (e.ctrlKey && e.key === 'Enter') {
                loginForm.submit();
            }
            
            // Escape to clear form
            if (e.key === 'Escape') {
                loginForm.reset();
                document.getElementById('username').focus();
            }
        });
        
        // Add ripple effect styles
        const style = document.createElement('style');
        style.textContent = `
            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }
            
            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>