<?php
require_once 'koneksi.php';
require_once 'auth.php';
require_login();

// Fetch Real Stats
$totalProjects = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
$activeProjects = $pdo->query("SELECT COUNT(*) FROM cards WHERE progress < 100")->fetchColumn();
$totalMessages = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$unreadMessages = 3; // Placeholder if no status column, or check if column exists
$totalSkills = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
$mainSkills = $pdo->query("SELECT COUNT(*) FROM skills WHERE proficiency >= 80")->fetchColumn();
$totalExp = $pdo->query("SELECT COUNT(*) FROM experience")->fetchColumn();

// Calculate years of experience (example logic)
$yearsExpStmt = $pdo->query("SELECT SUM(year_end - year_start) FROM experience WHERE year_end IS NOT NULL AND year_start IS NOT NULL");
$totalYears = (int)$yearsExpStmt->fetchColumn() ?: 3;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Konten - ProgressKu</title>

    <!-- Tailwind CDN + Config -->
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
                        success: {
                            500: '#10b981',
                            600: '#059669',
                        },
                        warning: {
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        danger: {
                            500: '#ef4444',
                            600: '#dc2626',
                        }
                    },
                    keyframes: {
                        fadeZoomIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.95) translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1) translateY(0)'
                            }
                        },
                        fadeZoomOut: {
                            '0%': {
                                opacity: '1',
                                transform: 'scale(1) translateY(0)'
                            },
                            '100%': {
                                opacity: '0',
                                transform: 'scale(0.95) translateY(10px)'
                            }
                        },
                        slideInRight: {
                            '0%': {
                                transform: 'translateX(20px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            }
                        },
                        pulseGlow: {
                            '0%, 100%': {
                                boxShadow: '0 0 0 0 rgba(14, 165, 233, 0.4)'
                            },
                            '50%': {
                                boxShadow: '0 0 0 10px rgba(14, 165, 233, 0)'
                            }
                        },
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        }
                    },
                    animation: {
                        'modal-in': 'fadeZoomIn 0.2s ease-out forwards',
                        'modal-out': 'fadeZoomOut 0.15s ease-in forwards',
                        'slide-in': 'slideInRight 0.3s ease-out forwards',
                        'pulse-glow': 'pulseGlow 2s infinite',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #0ea5e9, #8b5cf6);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #0284c7, #7c3aed);
        }
        
        /* Card hover effect */
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Stats cards */
        .stats-card {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Menu button glow */
        .menu-btn.active {
            box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);
        }
        
        /* Table row hover */
        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">

<!-- Sidebar Navigation (Desktop) -->
<div class="hidden lg:flex fixed left-0 top-0 h-full w-64 bg-white/80 backdrop-blur-md shadow-xl border-r border-gray-100 flex-col z-40">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold text-lg">
                P
            </div>
            <div>
                <h2 class="font-bold text-xl gradient-text">ProgressKu</h2>
                <p class="text-xs text-gray-500">Admin Dashboard</p>
            </div>
        </div>
    </div>
    
    <!-- User Info -->
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-secondary-400 flex items-center justify-center text-white font-bold">
                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
            </div>
            <div>
                <p class="font-semibold text-gray-800"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </div>
    </div>
    
    <!-- Menu Items -->
    <div class="flex-1 p-4 space-y-2">
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="identity">
            <i class="fas fa-id-card w-5 text-center"></i>
            <span>Identitas</span>
        </button>

        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="pages">
            <i class="fas fa-file-alt w-5 text-center"></i>
            <span>Konten</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="projects">
            <i class="fas fa-project-diagram w-5 text-center"></i>
            <span>Projects</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="contacts">
            <i class="fas fa-envelope w-5 text-center"></i>
            <span>Pesan</span>
            <?php if ($totalMessages > 0): ?>
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?= $totalMessages ?></span>
            <?php endif; ?>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="users">
            <i class="fas fa-users w-5 text-center"></i>
            <span>Users</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="education">
            <i class="fas fa-graduation-cap w-5 text-center"></i>
            <span>Pendidikan</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="skills">
            <i class="fas fa-code w-5 text-center"></i>
            <span>Skills</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700 hover:text-primary-600 hover:bg-primary-50" data-section="experience">
            <i class="fas fa-briefcase w-5 text-center"></i>
            <span>Pengalaman</span>
        </button>
    </div>
    
    <!-- Bottom Links -->
    <div class="p-4 border-t border-gray-100 space-y-2">
        <a href="index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-primary-50 transition-all duration-300">
            <i class="fas fa-external-link-alt w-5 text-center"></i>
            <span>Lihat Portfolio</span>
        </a>
        
        <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:text-white hover:bg-red-500 transition-all duration-300">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- Mobile Header -->
<div class="lg:hidden fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md shadow-md z-30 py-4 px-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white font-bold">
                P
            </div>
            <div>
                <h2 class="font-bold gradient-text">ProgressKu</h2>
                <p class="text-xs text-gray-500">Admin</p>
            </div>
        </div>
        
        <button id="mobileMenuBtn" class="text-gray-700">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>
</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="lg:hidden fixed top-0 left-0 right-0 bottom-0 bg-white z-40 transform -translate-x-full transition-transform duration-300 pt-20 px-4">
    <div class="space-y-2">
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="identity">
            <i class="fas fa-id-card w-5"></i>
            <span>Identitas</span>
        </button>

        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="pages">
            <i class="fas fa-file-alt w-5"></i>
            <span>Konten</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="projects">
            <i class="fas fa-project-diagram w-5"></i>
            <span>Projects</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="contacts">
            <i class="fas fa-envelope w-5"></i>
            <span>Pesan</span>
            <?php if ($totalMessages > 0): ?>
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full"><?= $totalMessages ?></span>
            <?php endif; ?>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="users">
            <i class="fas fa-users w-5"></i>
            <span>Users</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="education">
            <i class="fas fa-graduation-cap w-5"></i>
            <span>Pendidikan</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="skills">
            <i class="fas fa-code w-5"></i>
            <span>Skills</span>
        </button>
        
        <button class="menu-btn w-full text-left px-4 py-3 rounded-xl transition-all duration-300 flex items-center gap-3 text-gray-700" data-section="experience">
            <i class="fas fa-briefcase w-5"></i>
            <span>Pengalaman</span>
        </button>
        
        <div class="pt-4 border-t border-gray-200 space-y-2">
            <a href="index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-700">
                <i class="fas fa-external-link-alt w-5"></i>
                <span>Lihat Portfolio</span>
            </a>
            
            <a href="logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-600">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="lg:ml-64 pt-16 lg:pt-0">
    <div class="max-w-6xl mx-auto px-4 py-6 lg:py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl lg:text-4xl font-bold gradient-text mb-2">Dashboard Konten</h1>
            <p class="text-gray-600">Kelola semua konten portfolio Anda di satu tempat</p>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stats-card rounded-2xl p-6 glass-effect card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Projects</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $totalProjects ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-project-diagram text-primary-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-primary-500 rounded-full w-3/4"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><?= $activeProjects ?> projects aktif</p>
                </div>
            </div>
            
            <div class="stats-card rounded-2xl p-6 glass-effect card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pesan Baru</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $totalMessages ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-success-100 flex items-center justify-center">
                        <i class="fas fa-envelope text-success-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-success-500 rounded-full w-full"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Semua pesan terbaca</p>
                </div>
            </div>
            
            <div class="stats-card rounded-2xl p-6 glass-effect card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Skills</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $totalSkills ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-warning-100 flex items-center justify-center">
                        <i class="fas fa-code text-warning-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-warning-500 rounded-full w-2/3"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><?= $mainSkills ?> skills utama</p>
                </div>
            </div>
            
            <div class="stats-card rounded-2xl p-6 glass-effect card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pengalaman</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $totalExp ?></p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-secondary-100 flex items-center justify-center">
                        <i class="fas fa-briefcase text-secondary-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-secondary-500 rounded-full w-1/2"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><?= $totalYears ?> tahun pengalaman</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3 mb-8">
            <button class="px-5 py-3 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 text-white font-medium hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300 flex items-center gap-2" onclick="openForm('pages')">
                <i class="fas fa-plus"></i>
                <span>Tambah Konten</span>
            </button>
            
            <button class="px-5 py-3 rounded-xl bg-gradient-to-r from-success-500 to-emerald-500 text-white font-medium hover:shadow-lg hover:shadow-success-500/30 transition-all duration-300 flex items-center gap-2" onclick="openForm('projects')">
                <i class="fas fa-plus"></i>
                <span>Tambah Project</span>
            </button>
            
            <button class="px-5 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 font-medium hover:border-primary-500 hover:text-primary-600 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Data</span>
            </button>
        </div>
        
        <!-- SPA CONTENT -->
        <div id="spa-content" class="glass-effect rounded-2xl p-6 min-h-[400px]">
            <!-- Content will be loaded here -->
            <div class="flex flex-col items-center justify-center h-64">
                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-primary-100 to-secondary-100 flex items-center justify-center mb-4">
                    <i class="fas fa-spinner fa-spin text-2xl gradient-text"></i>
                </div>
                <p class="text-gray-600">Memuat data...</p>
            </div>
        </div>
    </div>
</main>

<!-- MODAL -->
<div id="modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">

    <!-- BACKDROP -->
    <div id="modal-backdrop"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- MODAL BOX -->
    <div id="modal-box"
         class="relative bg-white rounded-2xl shadow-2xl
                w-full max-w-3xl mx-auto
                max-h-[90vh] overflow-y-auto animate-modal-in">
        
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl z-10">
            <h3 class="text-xl font-bold gradient-text" id="modal-title">Form</h3>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div id="modal-body" class="p-6"></div>
        
        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
            <button id="modalCancelBtn" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button id="modalSubmitBtn" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-primary-500 to-secondary-500 text-white hover:shadow-lg transition-all duration-300">
                Simpan
            </button>
        </div>
    </div>
</div>

<script>
/* =====================
   ELEMENTS
===================== */
const spaContent    = document.getElementById('spa-content');
const menuBtns      = document.querySelectorAll('.menu-btn');
const modal         = document.getElementById('modal');
const modalBox      = document.getElementById('modal-box');
const modalBody     = document.getElementById('modal-body');
const modalBackdrop = document.getElementById('modal-backdrop');
const modalClose    = document.getElementById('modal-close');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu    = document.getElementById('mobileMenu');
const modalTitle    = document.getElementById('modal-title');
const modalSubmitBtn = document.getElementById('modalSubmitBtn');

/* =====================
   MOBILE MENU
===================== */
let mobileMenuOpen = false;

mobileMenuBtn?.addEventListener('click', () => {
    mobileMenuOpen = !mobileMenuOpen;
    if (mobileMenuOpen) {
        mobileMenu.classList.remove('-translate-x-full');
        document.body.style.overflow = 'hidden';
    } else {
        mobileMenu.classList.add('-translate-x-full');
        document.body.style.overflow = 'auto';
    }
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (mobileMenuOpen && !mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
        mobileMenuOpen = false;
        mobileMenu.classList.add('-translate-x-full');
        document.body.style.overflow = 'auto';
    }
});

// Close mobile menu when clicking a menu item
menuBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
            mobileMenuOpen = false;
            mobileMenu.classList.add('-translate-x-full');
            document.body.style.overflow = 'auto';
        }
    });
});

/* =====================
   SPA LOADER
===================== */
async function loadSection(section, page = 1) {
    // Update active menu
    menuBtns.forEach(btn => {
        // Remove active state classes
        btn.classList.remove('active', 'bg-gradient-to-r', 'from-primary-500', 'to-secondary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/20');
        // Restore default text color
        btn.classList.add('text-gray-700');
        
        if (btn.dataset.section === section) {
            btn.classList.add('active');
            btn.classList.remove('text-gray-700');
            btn.classList.add('bg-gradient-to-r', 'from-primary-500', 'to-secondary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/20');
        }
    });

    // Update page title
    const sectionTitles = {
        'identity': 'Identitas & Kontak',
        'pages': 'Konten',
        'projects': 'Projects',
        'contacts': 'Pesan',
        'users': 'Users',
        'education': 'Pendidikan',
        'skills': 'Skills',
        'experience': 'Pengalaman'
    };
    
    // Show loading state
    spaContent.innerHTML = `
        <div class="flex flex-col items-center justify-center h-64">
            <div class="w-16 h-16 rounded-full bg-gradient-to-r from-primary-100 to-secondary-100 flex items-center justify-center mb-4">
                <i class="fas fa-spinner fa-spin text-2xl gradient-text"></i>
            </div>
            <p class="text-gray-600">Memuat data ${sectionTitles[section] || section}...</p>
        </div>
    `;
    
    try {
        const res = await fetch(
            `admin_ajax.php?action=${section}&page=${page}`,
            { credentials: 'same-origin' }
        );
        
        if (!res.ok) throw new Error('Network response was not ok');
        
        const html = await res.text();
        spaContent.innerHTML = html;
        
        document.title = `${sectionTitles[section] || 'Dashboard'} - ProgressKu`;
        
        // Attach event listeners
        document.getElementById('addBtn')?.addEventListener('click', () => openForm(section));
        
        spaContent.querySelectorAll('.editBtn').forEach(btn => {
            btn.onclick = e => {
                e.preventDefault();
                openEditForm(btn.dataset.type, btn.dataset.id);
            };
        });
        
        spaContent.querySelectorAll('[data-page]').forEach(link => {
            link.onclick = e => {
                e.preventDefault();
                loadSection(section, link.dataset.page);
            };
        });
        
        // Add animations to table rows
        setTimeout(() => {
            const rows = spaContent.querySelectorAll('tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate-slide-in');
            });
        }, 100);
        
    } catch (error) {
        console.error('Error loading section:', error);
        spaContent.innerHTML = `
            <div class="flex flex-col items-center justify-center h-64">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <p class="text-gray-800 font-medium mb-2">Gagal memuat data</p>
                <p class="text-gray-600 text-sm text-center">Terjadi kesalahan saat memuat data. Silakan coba lagi.</p>
                <button class="mt-4 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors" onclick="loadSection('${section}')">
                    <i class="fas fa-redo mr-2"></i> Coba Lagi
                </button>
            </div>
        `;
    }
}

/* =====================
   MODAL HANDLER
===================== */
async function openEditForm(type, id) {
    const sectionTitles = {
        'page': 'Edit Konten',
        'user': 'Edit User',
        'education': 'Edit Pendidikan',
        'skill': 'Edit Skill',
        'experience': 'Edit Pengalaman',
        'project': 'Edit Project'
    };
    
    try {
        const res = await fetch(
            `admin_ajax.php?action=form&type=${type}&isEdit=1&id=${id}`,
            { credentials: 'same-origin' }
        );
        if (!res.ok) throw new Error('Gagal mengambil form');
        openModal(await res.text(), sectionTitles[type] || 'Edit');
    } catch (err) {
        alert(err.message);
    }
}

async function openForm(section) {
    const map = {
        pages: 'page',
        users: 'user',
        education: 'education',
        skills: 'skill',
        experience: 'experience',
        projects: 'project'
    };
    
    const formTitles = {
        'pages': 'Tambah Konten',
        'projects': 'Tambah Project',
        'users': 'Tambah User',
        'education': 'Tambah Pendidikan',
        'skills': 'Tambah Skill',
        'experience': 'Tambah Pengalaman'
    };
    
    try {
        const url = `admin_ajax.php?action=form&type=${map[section] || 'page'}`;
        const res = await fetch(url, { credentials: 'same-origin' });
        if (!res.ok) throw new Error('Gagal mengambil form');
        openModal(await res.text(), formTitles[section] || 'Tambah Data');
    } catch (err) {
        alert(err.message);
    }
}

/* =====================
   MODAL HANDLER
===================== */
function openModal(html, title = 'Form') {
    modalTitle.textContent = title;
    modalBody.innerHTML = html;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    const form = modalBody.querySelector('form');
    if (form) {
        modalSubmitBtn.onclick = async (e) => {
            e.preventDefault();
            
            // Show loading state
            const originalBtnText = modalSubmitBtn.innerHTML;
            modalSubmitBtn.disabled = true;
            modalSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            
            try {
                const formData = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                // Form submissions often redirect. If so, just reload the section.
                // If the response is OK, we assume success.
                if (res.ok) {
                    closeModal();
                    // Identify current section
                    const activeBtn = document.querySelector('.menu-btn.active');
                    if (activeBtn) loadSection(activeBtn.dataset.section);
                } else {
                    throw new Error('Gagal menyimpan data');
                }
            } catch (err) {
                alert(err.message);
            } finally {
                modalSubmitBtn.disabled = false;
                modalSubmitBtn.innerHTML = originalBtnText;
            }
        };
        modalSubmitBtn.style.display = 'block';
    } else {
        modalSubmitBtn.style.display = 'none';
    }
    
    modalClose.onclick = closeModal;
    document.getElementById('modalCancelBtn').onclick = closeModal;
}

function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modalBody.innerHTML = '';
    modalSubmitBtn.style.display = 'block';
}

modalBackdrop.onclick = closeModal;
document.addEventListener('keydown', e => e.key === 'Escape' && closeModal());

/* =====================
   INIT
===================== */
// Load initial section
loadSection('pages');

// Activate first menu item
if (menuBtns[0]) {
    menuBtns[0].classList.remove('text-gray-700');
    menuBtns[0].classList.add('active', 'bg-gradient-to-r', 'from-primary-500', 'to-secondary-500', 'text-white');
}

// Add click handlers to all menu buttons
menuBtns.forEach(btn => {
    btn.onclick = () => {
        const section = btn.dataset.section;
        if (section) {
            loadSection(section);
        }
    };
});

// Add floating animation to stats cards
setTimeout(() => {
    document.querySelectorAll('.stats-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
        card.classList.add('animate-float');
    });
}, 1000);
</script>

</body>
</html>