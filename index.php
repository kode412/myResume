<?php
require_once 'koneksi.php';

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch pages content
$about = null;
$services = [];
$contactPage = null;
$stmt = $pdo->prepare("SELECT * FROM pages WHERE `type` = ? ORDER BY id DESC");
$stmt->execute(['about']);
$about = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->execute(['service']);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->execute(['contact']);
$contactPage = $stmt->fetch(PDO::FETCH_ASSOC);

// Build query for cards based on filter
$query = "SELECT c.* FROM cards c";
$params = [];
if ($filter !== 'all') {
    $query .= " WHERE c.project_type = ?";
    $params[] = $filter;
}
$query .= " ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all tags for the form
$allTags = getAllTags($pdo);

// Get project types for tabs
$projectTypes = ['all' => 'All Projects', 'Web' => 'Web', 'Mobile' => 'Mobile', 'Arduino' => 'Arduino', 'Desktop' => 'Desktop', 'IoT' => 'IoT'];

// Define service icons (you can update these as needed)
$serviceIcons = [
    'Web Development' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>',
    'Mobile Apps' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>',
    'UI/UX Design' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>',
    'IoT Solutions' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
    'API Integration' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>',
    'Consultation' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>',
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Ku</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="font-poppins bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Navigation with active section detection -->
        <nav id="mainNav" class="fixed top-0 left-0 right-0 bg-white/90 backdrop-blur-md z-40 shadow-md py-4 transition-all duration-300">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-lg font-bold text-green-600">Portfolio</div>
                
                <div class="flex flex-wrap justify-center gap-2 sm:gap-4">
                    <a href="#about" class="nav-link text-gray-600 hover:text-green-600 px-4 py-2 rounded-lg transition-all duration-300 text-sm sm:text-base relative after:absolute after:bottom-0 after:left-1/2 after:w-0 after:h-0.5 after:bg-green-600 after:transition-all after:duration-300 hover:after:w-full hover:after:left-0" data-section="about">About</a>
                    <a href="#services" class="nav-link text-gray-600 hover:text-green-600 px-4 py-2 rounded-lg transition-all duration-300 text-sm sm:text-base relative after:absolute after:bottom-0 after:left-1/2 after:w-0 after:h-0.5 after:bg-green-600 after:transition-all after:duration-300 hover:after:w-full hover:after:left-0" data-section="services">Services</a>
                    <a href="#projects" class="nav-link text-gray-600 hover:text-green-600 px-4 py-2 rounded-lg transition-all duration-300 text-sm sm:text-base relative after:absolute after:bottom-0 after:left-1/2 after:w-0 after:h-0.5 after:bg-green-600 after:transition-all after:duration-300 hover:after:w-full hover:after:left-0" data-section="projects">Projects</a>
                    <a href="#contact" class="nav-link text-gray-600 hover:text-green-600 px-4 py-2 rounded-lg transition-all duration-300 text-sm sm:text-base relative after:absolute after:bottom-0 after:left-1/2 after:w-0 after:h-0.5 after:bg-green-600 after:transition-all after:duration-300 hover:after:w-full hover:after:left-0" data-section="contact">Contact</a>
                    <a href="resume.php" class="nav-link text-gray-600 hover:text-green-600 px-4 py-2 rounded-lg transition-all duration-300 text-sm sm:text-base relative after:absolute after:bottom-0 after:left-1/2 after:w-0 after:h-0.5 after:bg-green-600 after:transition-all after:duration-300 hover:after:w-full hover:after:left-0">Resume</a>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="https://github.com/kode412" target="_blank" class="text-gray-600 hover:text-gray-800 transition-colors duration-300" aria-label="GitHub">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 .5C5.73.5.5 5.73.5 12.02c0 5.09 3.29 9.41 7.86 10.94.58.11.79-.25.79-.56 0-.28-.01-1.02-.02-2-3.2.69-3.88-1.54-3.88-1.54-.53-1.36-1.3-1.72-1.3-1.72-1.06-.73.08-.72.08-.72 1.17.08 1.79 1.2 1.79 1.2 1.04 1.78 2.72 1.27 3.38.97.11-.76.41-1.27.75-1.56-2.55-.29-5.23-1.28-5.23-5.71 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.04 0 0 .97-.31 3.18 1.18a11.03 11.03 0 0 1 2.9-.39c.99 0 1.99.13 2.92.39 2.2-1.49 3.17-1.18 3.17-1.18.63 1.58.23 2.75.11 3.04.74.81 1.19 1.84 1.19 3.1 0 4.44-2.69 5.41-5.25 5.69.42.36.79 1.07.79 2.15 0 1.55-.01 2.8-.01 3.18 0 .31.2.68.8.56A10.52 10.52 0 0 0 23.5 12c0-6.29-5.23-11.52-11.5-11.5z" />
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/purwanto04" target="_blank" class="text-blue-700 hover:text-blue-800 transition-colors duration-300" aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4.98 3.5C4.98 5 3.73 6.25 2.23 6.25S-.52 5 .48 3.5 1.73.75 3.23.75 4.98 2 4.98 3.5zM.5 8.5h4.9V24H.5V8.5zM8.5 8.5h4.7v2.1h.1c.7-1.3 2.4-2.7 4.9-2.7 5.2 0 6.1 3.4 6.1 7.8V24h-4.9v-6.6c0-1.6-.03-3.6-2.2-3.6-2.2 0-2.6 1.7-2.6 3.5V24H8.5V8.5z" />
                        </svg>
                    </a>
                    <a href="https://twitter.com/" target="_blank" class="text-sky-500 hover:text-sky-700 transition-colors duration-300" aria-label="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 4.6c-.9.4-1.8.6-2.8.8.99-.6 1.7-1.6 2-2.8-.9.6-2 .9-3.1 1.1C19 2 17.8 1.5 16.5 1.5c-2.6 0-4.6 2.3-3.9 4.8C9.9 6 6.7 4.1 4.5 1.6c-.8 1.3-.4 3 .9 3.8-.8 0-1.5-.2-2.1-.6v.1c0 2 1.4 3.8 3.4 4.1-.7.2-1.4.2-2.1.1.6 1.9 2.4 3.3 4.5 3.3C9 15.1 6 16 2.9 16c-.7 0-1.4 0-2.1-.1C1.9 18.8 4.5 20 7.4 20c8.9 0 13.8-7.5 13.8-14 0-.2 0-.4 0-.6 1-.8 1.7-1.8 2.2-3z" />
                        </svg>
                    </a>
                    <a href="login.php" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg transition-all duration-300 text-sm">Login</a>
                </div>
            </div>
        </nav>

        <div class="h-20"></div>

        <!-- About Section (hero) -->
        <section id="about" class="content-block mt-6 scroll-mt-20">
            <div class="relative hero bg-gradient-to-r from-sky-400 via-blue-500 to-blue-700 text-white rounded-2xl p-8 md:p-12 overflow-hidden">
                <!-- Background pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
            </div>
            <div class="hero-inner max-w-4xl mx-auto text-center relative z-10">
                <h2 class="text-4xl md:text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-100">
                    <?= htmlspecialchars($about['title'] ?? 'Tentang Saya') ?>
                </h2>
                <div class="text-lg md:text-xl opacity-90 leading-relaxed mb-8 px-4">
                    <?= $about ? $about['content'] : 'Deskripsi singkat belum tersedia.' ?>
                </div>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="#projects" class="bg-white text-green-700 font-semibold px-6 py-3 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 transform">
                        Lihat Proyek
                    </a>
                    <a href="#contact" class="bg-transparent border-2 border-white text-white font-semibold px-6 py-3 rounded-xl hover:bg-white hover:text-green-700 transition-all duration-300">
                        Hubungi Saya
                    </a>
                </div>
            </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="content-block mt-20 scroll-mt-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Apa yang Bisa Saya Kerjakan ???</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Berbagai layanan yang saya tawarkan dengan dedikasi dan keahlian terbaik</p>
            </div>
            
            <div class="services-container overflow-x-auto pb-6">
                <div class="flex gap-6 min-w-min px-2">
                    <?php if (!empty($services)): ?>
                    <?php foreach ($services as $index => $s): 
                        $icon = $serviceIcons[$s['title']] ?? '<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                    ?>
                    <div class="service-card bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 min-w-[280px] border border-gray-100/50 flex-shrink-0">
                        <div class="text-green-600 mb-6 transform hover:scale-110 transition-transform duration-300">
                            <?= $icon ?>
                        </div>
                        <h4 class="text-xl font-semibold text-gray-800 mb-4"><?= htmlspecialchars($s['title']) ?></h4>
                        <div class="service-content text-gray-600 leading-relaxed"><?= $s['content'] ?></div>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <span class="text-sm text-green-600 font-medium">Learn more →</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Belum ada layanan yang ditambahkan.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="flex justify-center gap-2 mt-8">
                <?php for($i = 0; $i < min(count($services), 5); $i++): ?>
                <button class="service-indicator w-2 h-2 rounded-full bg-gray-300 transition-all duration-300 <?= $i === 0 ? 'bg-green-600 w-8' : '' ?>"></button>
                <?php endfor; ?>
            </div>
        </section>

        <!-- Projects Section -->
        <section id="projects" class="content-block mt-20 scroll-mt-20">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Projects Portfolio</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Koleksi proyek yang telah saya kerjakan dengan berbagai teknologi</p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <?php foreach ($projectTypes as $type => $label): ?>
                <button onclick="filterProjects('<?= $type ?>')" 
                        class="tab px-6 py-3 rounded-full border-2 transition-all duration-300 font-medium text-sm sm:text-base
                               <?= $filter === $type 
                                 ? 'border-green-600 bg-green-600 text-white shadow-lg' 
                                 : 'border-gray-300 text-gray-700 hover:border-green-600 hover:text-green-600 hover:shadow-md' ?>">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Projects Grid -->
            <div class="cards-container grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (empty($cards)): ?>
                <div class="no-projects col-span-full text-center py-16 bg-white/50 backdrop-blur-sm rounded-2xl border border-gray-100/50">
                    <div class="text-6xl mb-4">📁</div>
                    <p class="text-gray-500 text-lg">Tidak ada project yang ditemukan untuk kategori ini.</p>
                </div>
                <?php else: ?>
                <?php $i = 0; foreach ($cards as $card): 
                        $cardTags = getCardTags($pdo, $card['id']);
                        $i++;
                    ?>
                <div class="card group bg-white/60 backdrop-blur-sm rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100/50"
                    style="--order: <?= $i ?>">
                    <!-- Image Container with Overlay -->
                    <div class="relative overflow-hidden">
                        <img src="<?= htmlspecialchars($card['image_path']) ?>" 
                             alt="<?= htmlspecialchars($card['title']) ?>" 
                             class="w-full h-56 object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-green-700 font-semibold px-3 py-1.5 rounded-full text-sm shadow-md">
                            <?= htmlspecialchars($card['project_type']) ?>
                        </span>
                    </div>
                    
                    <!-- Card Content -->
                    <div class="p-6">
                        <h5 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-green-700 transition-colors duration-300">
                            <?= htmlspecialchars($card['title']) ?>
                        </h5>
                        
                        <!-- Tags Container -->
                        <div class="tags-container flex flex-wrap gap-2 mb-4">
                            <?php foreach ($cardTags as $tag): ?>
                            <span class="tag text-xs font-medium px-3 py-1.5 rounded-full backdrop-blur-sm border border-white/30 shadow-sm"
                                style="background-color: <?= $tag['color'] ?>20; color: <?= $tag['color'] ?>">
                                <?= htmlspecialchars($tag['name']) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Progress Section -->
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                <span class="text-sm font-bold text-green-600"><?= $card['progress'] ?>%</span>
                            </div>
                            <div class="progress-container h-2.5 rounded-full bg-gray-100/50 overflow-hidden backdrop-blur-sm">
                                <div class="progress-bar bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full transition-all duration-1000 ease-out" 
                                     style="width: <?= $card['progress'] ?>%">
                                </div>
                            </div>
                        </div>
                        
                        <!-- View More Button -->
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <button class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-medium py-3 rounded-xl hover:shadow-lg hover:shadow-green-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="content-block mt-20 scroll-mt-20">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Kontak</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Jangan ragu untuk menghubungi saya untuk diskusi proyek Anda</p>
                </div>
                
                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Contact Information -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-gray-100/50">
                        <?php if ($contactPage): ?>
                        <div class="rich-content text-gray-700 space-y-6"><?php echo $contactPage['content']; ?></div>
                        <?php endif; ?>
                        
                        <!-- Contact Details -->
                        <div class="mt-8 space-y-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89-4.26a2 2 0 012.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium"><?= htmlspecialchars($contactPage['title'] ?? 'email@example.com') ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone</p>
                                    <p class="font-medium"><?= htmlspecialchars($contactPage['subtitle'] ?? '+62 8xx...') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg border border-gray-100/50">
                        <form action="add_contact.php" method="POST" class="space-y-6">
                            <div class="form-group">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300 outline-none bg-white/50 backdrop-blur-sm" 
                                    placeholder="Masukkan nama Anda" />
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300 outline-none bg-white/50 backdrop-blur-sm"
                                    placeholder="email@example.com" />
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                                <textarea id="message" name="message" rows="5" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300 outline-none resize-none bg-white/50 backdrop-blur-sm"
                                    placeholder="Tulis pesan Anda di sini..."></textarea>
                            </div>
                            
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold px-6 py-4 rounded-xl hover:shadow-xl hover:shadow-green-500/30 transition-all duration-300 transform hover:-translate-y-0.5">
                                <span class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    Kirim Pesan
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-20 pt-12 pb-8 border-t border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Brand Info -->
                    <div>
                        <h3 class="text-xl font-bold text-green-600 mb-4">Portfolio</h3>
                        <p class="text-gray-600 mb-6">Membangun solusi digital dengan kreativitas dan teknologi terkini untuk hasil terbaik.</p>
                        <div class="flex gap-4">
                            <a href="https://github.com/kode412" target="_blank" class="text-gray-600 hover:text-green-600 transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                            </a>
                            <a href="https://www.linkedin.com/purwanto04" target="_blank" class="text-blue-700 hover:text-blue-800 transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.98 3.5C4.98 5 3.73 6.25 2.23 6.25S-.52 5 .48 3.5 1.73.75 3.23.75 4.98 2 4.98 3.5zM.5 8.5h4.9V24H.5V8.5zM8.5 8.5h4.7v2.1h.1c.7-1.3 2.4-2.7 4.9-2.7 5.2 0 6.1 3.4 6.1 7.8V24h-4.9v-6.6c0-1.6-.03-3.6-2.2-3.6-2.2 0-2.6 1.7-2.6 3.5V24H8.5V8.5z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/" target="_blank" class="text-sky-500 hover:text-sky-700 transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.6c-.9.4-1.8.6-2.8.8.99-.6 1.7-1.6 2-2.8-.9.6-2 .9-3.1 1.1C19 2 17.8 1.5 16.5 1.5c-2.6 0-4.6 2.3-3.9 4.8C9.9 6 6.7 4.1 4.5 1.6c-.8 1.3-.4 3 .9 3.8-.8 0-1.5-.2-2.1-.6v.1c0 2 1.4 3.8 3.4 4.1-.7.2-1.4.2-2.1.1.6 1.9 2.4 3.3 4.5 3.3C9 15.1 6 16 2.9 16c-.7 0-1.4 0-2.1-.1C1.9 18.8 4.5 20 7.4 20c8.9 0 13.8-7.5 13.8-14 0-.2 0-.4 0-.6 1-.8 1.7-1.8 2.2-3z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h4>
                        <ul class="space-y-3">
                            <li><a href="#about" class="text-gray-600 hover:text-green-600 transition-colors duration-300">About</a></li>
                            <li><a href="#services" class="text-gray-600 hover:text-green-600 transition-colors duration-300">Services</a></li>
                            <li><a href="#projects" class="text-gray-600 hover:text-green-600 transition-colors duration-300">Projects</a></li>
                            <li><a href="#contact" class="text-gray-600 hover:text-green-600 transition-colors duration-300">Contact</a></li>
                            <li><a href="resume.php" class="text-gray-600 hover:text-green-600 transition-colors duration-300">Resume</a></li>
                        </ul>
                    </div>
                    
                    <!-- Newsletter -->
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Stay Updated</h4>
                        <p class="text-gray-600 mb-4">Subscribe to get updates on new projects and articles.</p>
                        <form class="flex gap-2">
                            <input type="email" placeholder="Your email" 
                                   class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none transition-all duration-300">
                            <button type="submit" 
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-300">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Copyright -->
                <div class="mt-12 pt-8 border-t border-gray-200 text-center">
                    <p class="text-gray-600">© <?= date('Y') ?> Portfolio. All rights reserved.</p>
                    <p class="text-gray-500 text-sm mt-2">Made with ❤️ by Purwanto</p>
                </div>
            </div>
        </footer>
    </div>

    <style>
    :root {
        --nav-height: 80px;
    }
    
    .content-block {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease-out, transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .content-block.in-view {
        opacity: 1;
        transform: translateY(0);
    }
    
    .nav-link.active {
        color: #059669;
        font-weight: 600;
    }
    
    .nav-link.active::after {
        width: 100% !important;
        left: 0 !important;
    }
    
    /* Custom scrollbar for services */
    .services-container::-webkit-scrollbar {
        height: 6px;
    }
    
    .services-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .services-container::-webkit-scrollbar-thumb {
        background: #059669;
        border-radius: 10px;
    }
    
    .services-container::-webkit-scrollbar-thumb:hover {
        background: #047857;
    }
    
    /* Card animation */
    @keyframes cardEnter {
        0% {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .card {
        animation: cardEnter 0.6s forwards;
        animation-delay: calc(var(--order) * 0.1s);
        opacity: 0;
    }
    
    /* Glass effect */
    .glass-effect {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, #059669 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize animations
        initializeAnimations();
        
        // Initialize navigation
        initializeNavigation();
        
        // Initialize services carousel
        initializeServicesCarousel();
        
        // Initialize back to top button
        initializeBackToTop();
        
        // Initialize intersection observer for sections
        initializeIntersectionObserver();
    });
    
    function initializeAnimations() {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const targetWidth = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = targetWidth;
            }, 100);
        });
        
        // Animate cards on load
        animateCards();
    }
    
    function initializeNavigation() {
        const navLinks = document.querySelectorAll('.nav-link[data-section]');
        const sections = {};
        
        // Store all sections and their positions
        document.querySelectorAll('.content-block').forEach(section => {
            if (section.id) {
                sections[section.id] = section;
            }
        });
        
        // Update active nav link based on scroll position
        function updateActiveNav() {
            const scrollPosition = window.scrollY + 100;
            let currentSection = 'about';
            
            // Find which section is currently in view
            for (const [id, section] of Object.entries(sections)) {
                const sectionTop = section.offsetTop;
                const sectionBottom = sectionTop + section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    currentSection = id;
                    break;
                }
            }
            
            // Update nav links
            navLinks.forEach(link => {
                if (link.getAttribute('data-section') === currentSection) {
                    link.classList.add('text-green-600', 'font-semibold');
                    link.classList.remove('text-gray-600');
                } else {
                    link.classList.remove('text-green-600', 'font-semibold');
                    link.classList.add('text-gray-600');
                }
            });
            
            // Update navbar background on scroll
            const nav = document.getElementById('mainNav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-lg', 'py-3');
                nav.classList.remove('py-4');
            } else {
                nav.classList.remove('shadow-lg', 'py-3');
                nav.classList.add('py-4');
            }
        }
        
        // Smooth scroll for nav links
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.hasAttribute('href') && this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    
                    if (targetSection) {
                        window.scrollTo({
                            top: targetSection.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
        
        // Listen for scroll events
        window.addEventListener('scroll', updateActiveNav);
        updateActiveNav(); // Initialize on load
    }
    
    function initializeServicesCarousel() {
        const servicesContainer = document.querySelector('.services-container');
        const serviceCards = document.querySelectorAll('.service-card');
        const indicators = document.querySelectorAll('.service-indicator');
        
        if (!servicesContainer || serviceCards.length === 0) return;
        
        let currentIndex = 0;
        const cardWidth = serviceCards[0].offsetWidth + 24; // width + gap
        
        // Auto scroll services
        setInterval(() => {
            currentIndex = (currentIndex + 1) % Math.ceil(serviceCards.length / 2);
            scrollToService(currentIndex);
        }, 5000);
        
        function scrollToService(index) {
            servicesContainer.scrollTo({
                left: index * cardWidth,
                behavior: 'smooth'
            });
            
            // Update indicators
            indicators.forEach((indicator, i) => {
                if (i === index) {
                    indicator.classList.add('bg-green-600', 'w-8');
                    indicator.classList.remove('bg-gray-300');
                } else {
                    indicator.classList.remove('bg-green-600', 'w-8');
                    indicator.classList.add('bg-gray-300');
                }
            });
        }
        
        // Click handlers for indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                currentIndex = index;
                scrollToService(index);
            });
        });
    }
    
    function initializeBackToTop() {
        const backBtn = document.createElement('button');
        backBtn.id = 'backToTop';
        backBtn.className = 'fixed bottom-8 right-8 bg-gradient-to-br from-green-500 to-green-600 text-white p-3 rounded-full shadow-lg z-50 hover:shadow-xl hover:shadow-green-500/30 transition-all duration-300 transform hover:-translate-y-1 opacity-0';
        backBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        `;
        document.body.appendChild(backBtn);
        
        function checkScroll() {
            if (window.scrollY > 300) {
                backBtn.style.opacity = '1';
                backBtn.style.pointerEvents = 'auto';
            } else {
                backBtn.style.opacity = '0';
                backBtn.style.pointerEvents = 'none';
            }
        }
        
        backBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        window.addEventListener('scroll', checkScroll);
        checkScroll();
    }
    
    function initializeIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: '-50px 0px -50px 0px'
        });
        
        document.querySelectorAll('.content-block').forEach(section => observer.observe(section));
    }
    
    function animateCards() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.setProperty('--order', index);
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.animation = 'cardEnter 0.6s forwards';
                card.style.animationDelay = `calc(${index} * 0.1s)`;
            }, 10);
        });
    }
    
    // AJAX filter function
    function filterProjects(type) {
        // Update active tab
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('border-green-600', 'bg-green-600', 'text-white', 'shadow-lg');
            tab.classList.add('border-gray-300', 'text-gray-700');
        });
        
        const activeTab = document.querySelector(`.tab[onclick*="${type}"]`);
        if (activeTab) {
            activeTab.classList.add('border-green-600', 'bg-green-600', 'text-white', 'shadow-lg');
            activeTab.classList.remove('border-gray-300', 'text-gray-700');
        }
        
        // Show loading state
        const container = document.querySelector('.cards-container');
        container.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500 mb-4"></div>
                <p class="text-gray-600">Loading projects...</p>
            </div>
        `;
        
        // Fetch filtered projects
        fetch(`filter.php?type=${type}`)
            .then(response => response.text())
            .then(data => {
                container.innerHTML = data;
                animateCards();
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <div class="text-6xl mb-4">⚠️</div>
                        <p class="text-gray-600">Error loading projects. Please try again.</p>
                    </div>
                `;
            });
    }
    
    // Add hover effects for cards
    document.addEventListener('mouseover', function(e) {
        if (e.target.closest('.card')) {
            const card = e.target.closest('.card');
            card.style.transform = 'translateY(-12px)';
        }
    }, true);
    
    document.addEventListener('mouseout', function(e) {
        if (e.target.closest('.card')) {
            const card = e.target.closest('.card');
            card.style.transform = 'translateY(-3px)';
        }
    }, true);
    </script>
</body>
</html>