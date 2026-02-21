<?php
require_once 'koneksi.php';

$stmt = $pdo->prepare("SELECT * FROM pages WHERE `type` = ? ORDER BY id DESC LIMIT 1");
$stmt->execute(['resume']);
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM skills ORDER BY category, sort_order, name");
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM experience ORDER BY year_end DESC, year_start DESC");
$experience = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM education ORDER BY year_end DESC, year_start DESC");
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group skills by category
$skillsByCategory = [];
foreach ($skills as $s) {
    $skillsByCategory[$s['category']][] = $s;
}

// If download requested, try to generate PDF using DOMPDF if available
if (isset($_GET['download'])) {
    $html = '<h1>' . ($resume['title'] ?? 'Resume') . '</h1>';
    $html .= '<p>' . ($resume['content'] ?? 'No resume content available.') . '</p>';

    if (class_exists('\Dompdf\Dompdf')) {
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf = $dompdf->output();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="resume.pdf"');
        echo $pdf;
        exit();
    } else {
        // Fallback: send HTML file for download
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="resume.html"');
        echo '<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;padding:20px;}</style></head><body>' . $html . '</body></html>';
        exit();
    }
}

// Get identity info (name, job title, bio)
$stmt = $pdo->prepare("SELECT * FROM pages WHERE `type` = ? ORDER BY id DESC LIMIT 1");
$stmt->execute(['about']);
$aboutPage = $stmt->fetch(PDO::FETCH_ASSOC);

// Get contact info for resume
$stmt = $pdo->prepare("SELECT * FROM pages WHERE `type` = ? ORDER BY id DESC LIMIT 1");
$stmt->execute(['contact']);
$contactPage = $stmt->fetch(PDO::FETCH_ASSOC);

?><!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($aboutPage['title'] ?? 'Resume'); ?> - Professional Portfolio</title>
  
  <!-- Tailwind CSS with custom config -->
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
            },
            dark: {
              700: '#1f2937',
              800: '#111827',
            }
          },
          animation: {
            'fade-in': 'fadeIn 0.6s ease-out',
            'slide-up': 'slideUp 0.5s ease-out',
            'progress-bar-anim': 'progressBar 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards',
            'float': 'float 6s ease-in-out infinite',
            'pulse-slow': 'pulse 3s ease-in-out infinite',
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
            progressBar: {
              '0%': { width: '0' },
              '100%': { width: 'var(--target-width)' }
            },
            float: {
              '0%, 100%': { transform: 'translateY(0)' },
              '50%': { transform: 'translateY(-10px)' }
            }
          },
          backgroundImage: {
            'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
            'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
          }
        }
      }
    }

    <?php
    function getSkillColor($p) {
        if ($p >= 80) return "from-primary-500 to-secondary-500";
        if ($p >= 60) return "from-success-500 to-primary-500";
        if ($p >= 40) return "from-warning-500 to-orange-500";
        return "from-danger-500 to-red-400";
    }
    ?>
  </script>
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    * {
      font-family: 'Inter', sans-serif;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Poppins', sans-serif;
    }
    
    .glass-effect {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }
    
    .gradient-text {
      background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
    }
    
    .hover-lift {
      transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(14, 165, 233, 0.15);
    }
    
    .timeline-item::before {
      content: '';
      position: absolute;
      left: -20px;
      top: 0;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
      border: 3px solid white;
      box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
    }
    
    .progress-container {
      --progress: 0%;
    }
    
    .progress-bar {
      width: 0%;
      animation: progressBar 1.5s ease-out forwards;
      animation-delay: calc(var(--index) * 0.1s);
    }
    
    .print-only {
      display: none;
    }
    
    @media print {
      .no-print {
        display: none;
      }
      
      .print-only {
        display: block;
      }
      
      body {
        background: white !important;
        color: black !important;
      }
      
      .glass-effect {
        background: white !important;
        backdrop-filter: none !important;
        border: 1px solid #ddd !important;
        box-shadow: none !important;
      }
      
      .gradient-text {
        background: none !important;
        -webkit-text-fill-color: black !important;
        color: black !important;
      }
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
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
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen print:bg-white print:text-black animate-fade-in">
  
  <!-- Floating Navigation -->
  <nav class="fixed top-6 right-6 z-50 no-print">
    <div class="flex items-center gap-3 bg-white/80 backdrop-blur-md rounded-full shadow-lg px-4 py-3">
      <a href="index.php" class="text-gray-700 hover:text-primary-600 transition-colors" title="Back to Portfolio">
        <i class="fas fa-home"></i>
      </a>
      <span class="h-4 w-px bg-gray-300"></span>
      <button onclick="window.print()" class="text-gray-700 hover:text-primary-600 transition-colors" title="Print Resume">
        <i class="fas fa-print"></i>
      </button>
      <a href="?download=1" class="bg-gradient-to-r from-primary-500 to-secondary-500 text-white px-4 py-2 rounded-full text-sm font-medium hover:shadow-lg hover:shadow-primary-500/30 transition-all duration-300">
        <i class="fas fa-download mr-2"></i> Download CV
      </a>
    </div>
  </nav>

  <!-- Print Header (only shows when printing) -->
  <div class="print-only p-8">
    <div class="flex justify-between items-start mb-6">
      <div>
        <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($aboutPage['title'] ?? 'Professional Resume'); ?></h1>
        <p class="text-gray-600"><?php echo date('F j, Y'); ?></p>
      </div>
      <div class="text-right">
        <p class="font-semibold"><?php echo htmlspecialchars($aboutPage['subtitle'] ?? 'Professional Portfolio'); ?></p>
      </div>
    </div>
    <hr class="my-6 border-gray-300">
  </div>

  <div class="max-w-6xl mx-auto px-4 py-8 lg:py-12">
    <!-- Main Resume Header -->
    <header class="mb-12 lg:mb-16 animate-slide-up">
      <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
        <div class="flex-1">
          <div class="mb-6">
            <h1 class="text-4xl lg:text-5xl font-bold mb-3 gradient-text"><?php echo htmlspecialchars($aboutPage['title'] ?? 'Nama Anda'); ?></h1>
            <p class="text-xl text-gray-600 mb-4"><?php echo htmlspecialchars($aboutPage['subtitle'] ?? 'Professional Developer & Designer'); ?></p>
            
            <!-- Contact Info -->
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
              <?php if ($contactPage): ?>
                <div class="flex items-center gap-2">
                  <i class="fas fa-envelope text-primary-500"></i>
                  <span><?php echo htmlspecialchars($contactPage['title'] ?? ''); ?></span>
                </div>
                <div class="flex items-center gap-2">
                  <i class="fas fa-phone text-primary-500"></i>
                  <span><?php echo htmlspecialchars($contactPage['subtitle'] ?? ''); ?></span>
                </div>
                <div class="flex items-center gap-2">
                  <i class="fas fa-map-marker-alt text-primary-500"></i>
                  <span><?php echo htmlspecialchars($contactPage['content'] ?? ''); ?></span>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Profile Image Placeholder -->
        <div class="w-32 h-32 lg:w-40 lg:h-40 rounded-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white text-4xl lg:text-5xl font-bold shadow-xl hover-lift">
          <?php echo strtoupper(substr(htmlspecialchars($aboutPage['title'] ?? 'A'), 0, 1)); ?>
        </div>
      </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Resume Summary -->
        <?php if ($resume && !empty($resume['content'])): ?>
        <section class="glass-effect rounded-2xl p-8 hover-lift animate-slide-up" style="animation-delay: 0.1s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
              <i class="fas fa-user text-xl gradient-text"></i>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Professional Summary</h2>
          </div>
          <div class="text-gray-700 leading-relaxed text-lg"><?php echo $aboutPage['content'] ?? ''; ?></div>
        </section>
        <?php endif; ?>

        <!-- Experience Section -->
        <?php if (!empty($experience)): ?>
        <section class="glass-effect rounded-2xl p-8 hover-lift animate-slide-up" style="animation-delay: 0.2s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
              <i class="fas fa-briefcase text-xl gradient-text"></i>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Work Experience</h2>
          </div>
          
          <div class="relative pl-8">
            <!-- Timeline line -->
            <div class="absolute left-7 top-0 bottom-0 w-0.5 bg-gradient-to-b from-primary-500 to-secondary-500"></div>
            
            <div class="space-y-8">
              <?php foreach ($experience as $index => $ex): ?>
              <div class="timeline-item relative">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2 mb-3">
                  <div>
                    <h4 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($ex['position']); ?></h4>
                    <div class="flex items-center gap-2 text-gray-600 mt-1">
                      <i class="fas fa-building text-primary-500 text-sm"></i>
                      <span><?php echo htmlspecialchars($ex['company']); ?></span>
                    </div>
                  </div>
                  <div class="inline-flex items-center gap-2 bg-primary-50 text-primary-700 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo $ex['year_start']; ?> – <?php echo $ex['year_end'] ? $ex['year_end'] : 'Present'; ?></span>
                  </div>
                </div>
                
                <?php if (!empty($ex['description'])): ?>
                <div class="bg-gray-50 rounded-xl p-4 mt-3">
                  <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($ex['description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Skills tags -->
                <?php if (!empty($ex['skills'])): ?>
                <div class="flex flex-wrap gap-2 mt-4">
                  <?php 
                  $skillList = explode(',', $ex['skills']);
                  foreach ($skillList as $skill):
                    if (trim($skill)):
                  ?>
                  <span class="px-3 py-1 bg-gradient-to-r from-primary-50 to-secondary-50 text-primary-700 text-xs rounded-full border border-primary-100">
                    <?php echo htmlspecialchars(trim($skill)); ?>
                  </span>
                  <?php 
                    endif;
                  endforeach; 
                  ?>
                </div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
        <?php endif; ?>

        <!-- Education Section -->
        <?php if (!empty($education)): ?>
        <section class="glass-effect rounded-2xl p-8 hover-lift animate-slide-up" style="animation-delay: 0.3s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
              <i class="fas fa-graduation-cap text-xl gradient-text"></i>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Education</h2>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($education as $edu): ?>
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl p-6 border border-gray-100 hover:border-primary-200 transition-all duration-300">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <h4 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($edu['degree']); ?></h4>
                  <p class="text-gray-600"><?php echo htmlspecialchars($edu['field']); ?></p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-primary-100 flex items-center justify-center">
                  <i class="fas fa-university text-primary-600"></i>
                </div>
              </div>
              
              <div class="space-y-3">
                <div class="flex items-center gap-2 text-gray-600">
                  <i class="fas fa-school text-primary-500"></i>
                  <span class="font-medium"><?php echo htmlspecialchars($edu['school']); ?></span>
                </div>
                
                <div class="flex items-center gap-2 text-gray-600">
                  <i class="fas fa-calendar text-primary-500"></i>
                  <span><?php echo $edu['year_start']; ?> – <?php echo $edu['year_end'] ? $edu['year_end'] : 'Present'; ?></span>
                </div>
                
                <?php if (!empty($edu['description'])): ?>
                <div class="pt-3 border-t border-gray-100">
                  <p class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($edu['description'])); ?></p>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>
      </div>

      <!-- Right Column -->
      <div class="space-y-8">
        <!-- Skills Section -->
        <?php if (!empty($skills)): ?>
        <section class="glass-effect rounded-2xl p-8 hover-lift animate-slide-up" style="animation-delay: 0.4s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
              <i class="fas fa-code text-xl gradient-text"></i>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Skills</h2>
          </div>
          
          <?php $skillIndex = 0; ?>
          <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
          <div class="mb-8 last:mb-0">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
              <span class="w-2 h-2 rounded-full bg-primary-500"></span>
              <?php echo htmlspecialchars($category); ?>
            </h3>
            
            <div class="space-y-5">
              <?php foreach ($categorySkills as $skill): ?>
              <?php $skillIndex++; ?>
              <div>
                <div class="flex justify-between items-center mb-2">
                  <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($skill['name']); ?></span>
                  <span class="font-bold text-gray-800"><?php echo (int)$skill['proficiency']; ?>%</span>
                </div>
                <div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden shadow-inner">
                  <div class="h-full rounded-full bg-gradient-to-r <?php echo getSkillColor($skill['proficiency']); ?> animate-progress-bar-anim" 
                       style="--target-width: <?php echo (int)$skill['proficiency']; ?>%; animation-delay: <?php echo ($skillIndex * 0.1); ?>s; width: 0;"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </section>
        <?php endif; ?>

        <!-- Contact Info Card -->
        <div class="glass-effect rounded-2xl p-8 bg-gradient-to-br from-primary-500 to-secondary-500 text-white hover-lift animate-slide-up" style="animation-delay: 0.5s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
              <i class="fas fa-address-card text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold">Contact Info</h2>
          </div>
          
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <i class="fas fa-envelope"></i>
              </div>
              <div>
                <p class="text-sm opacity-90">Email</p>
                <p class="font-medium"><?php echo htmlspecialchars($contactPage['title'] ?? ''); ?></p>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <i class="fas fa-phone"></i>
              </div>
              <div>
                <p class="text-sm opacity-90">Phone</p>
                <p class="font-medium"><?php echo htmlspecialchars($contactPage['subtitle'] ?? ''); ?></p>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <i class="fas fa-map-marker-alt"></i>
              </div>
              <div>
                <p class="text-sm opacity-90">Location</p>
                <p class="font-medium"><?php echo htmlspecialchars($contactPage['content'] ?? ''); ?></p>
              </div>
            </div>
          </div>
          
          <div class="mt-8 pt-6 border-t border-white/20">
            <p class="text-center text-sm opacity-90">Available for opportunities</p>
            <div class="flex justify-center gap-3 mt-4">
              <a href="index.php#contact" class="bg-white text-primary-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                Contact Me
              </a>
            </div>
          </div>
        </div>

        <!-- Languages (Optional - you can add this to database later) -->
        <div class="glass-effect rounded-2xl p-8 hover-lift animate-slide-up" style="animation-delay: 0.6s">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-secondary-100 flex items-center justify-center">
              <i class="fas fa-language text-xl gradient-text"></i>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Languages</h2>
          </div>
          
          <div class="space-y-4">
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-gray-700 font-medium">Indonesian</span>
                <span class="font-bold text-gray-800">Native</span>
              </div>
              <div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden shadow-inner">
                <div class="h-full rounded-full bg-gradient-to-r from-primary-500 to-secondary-500 animate-progress-bar-anim" style="--target-width: 100%; width: 0;"></div>
              </div>
            </div>
            
            <div>
              <div class="flex justify-between mb-2">
                <span class="text-gray-700 font-medium">English</span>
                <span class="font-bold text-gray-800">Professional</span>
              </div>
              <div class="w-full bg-gray-200 h-2.5 rounded-full overflow-hidden shadow-inner">
                <div class="h-full rounded-full bg-gradient-to-r from-success-500 to-primary-500 animate-progress-bar-anim" style="--target-width: 85%; width: 0; animation-delay: 0.2s;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="mt-16 pt-8 border-t border-gray-200 text-center text-gray-600 no-print">
      <p class="mb-2">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($aboutPage['title'] ?? 'Professional Portfolio'); ?>. All rights reserved.</p>
      <p class="text-sm">Last updated: <?php echo date('F j, Y'); ?></p>
    </footer>
  </div>

  <!-- Floating Action Button -->
  <div class="fixed bottom-6 left-6 z-50 no-print animate-float">
    <div class="bg-gradient-to-br from-primary-500 to-secondary-500 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-xl hover:shadow-2xl hover:scale-110 transition-all duration-300 cursor-pointer" onclick="window.print()">
      <i class="fas fa-print text-xl"></i>
    </div>
  </div>

  <script>
    // Animate elements on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
      section.style.opacity = '0';
      section.style.transform = 'translateY(20px)';
      section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(section);
    });

    // Handle print button
    document.addEventListener('keydown', (e) => {
      if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
      }
    });

    // Add confetti effect on download (optional)
    const downloadBtn = document.querySelector('a[href*="download"]');
    if (downloadBtn) {
      downloadBtn.addEventListener('click', () => {
        // Add loading effect
        const originalText = downloadBtn.innerHTML;
        downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating PDF...';
        downloadBtn.disabled = true;
        
        // Reset after 3 seconds if still on page
        setTimeout(() => {
          downloadBtn.innerHTML = originalText;
          downloadBtn.disabled = false;
        }, 3000);
      });
    }
  </script>
</body>
</html>