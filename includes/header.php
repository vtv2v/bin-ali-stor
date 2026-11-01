<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME ?></title>
    
    <!-- Meta Tags -->
    <meta name="description" content="Bin Ali Store - متجرك الشامل للإلكترونيات والهواتف والأجهزة الكهربائية وحلول الطاقة الشمسية. Order online or via WhatsApp.">
    <meta name="keywords" content="إلكترونيات, هواتف ذكية, أجهزة كهربائية, طاقة شمسية, اليمن, توصيل, طلب واتساب">
    <meta name="author" content="Bin Ali Store">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <meta property="og:title" content="<?= isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME ?>">
    <meta property="og:description" content="متجرك الشامل للإلكترونيات والهواتف والأجهزة الكهربائية وحلول الطاقة الشمسية.">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/logo.png">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= SITE_URL ?>">
    <meta property="twitter:title" content="<?= isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME ?>">
    <meta property="twitter:description" content="متجرك الشامل للإلكترونيات والهواتف والأجهزة الكهربائية وحلول الطاقة الشمسية.">
    <meta property="twitter:image" content="<?= SITE_URL ?>/assets/images/logo.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="apple-touch-icon" href="assets/images/logo.png">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .container {
            max-width: 1200px;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
        }
        
        .category-card:hover {
            transform: translateY(-2px);
        }
        
        .navbar-sticky {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .whatsapp-float:hover {
            transform: scale(1.1);
            background-color: #128c7e;
        }
        
        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #10b981, #3b82f6);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }
        
        .loading-screen.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .loading-logo {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @media screen and (max-width: 767px) {
            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
                font-size: 24px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
        <img src="assets/images/logo.png" alt="Bin Ali Store" class="loading-logo h-24 w-auto mb-4">
        <h2 class="text-white text-2xl font-bold mb-2">Bin Ali Store</h2>
        <p class="text-white/80">جارٍ تحميل المتجر...</p>
        <div class="mt-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?= SITE_URL ?>" class="flex items-center">
                        <img src="assets/images/logo.png" alt="Bin Ali Store" class="h-10 w-auto me-2">
                        <span class="hidden sm:block text-2xl font-bold text-green-600">Bin Ali Store</span>
                        <span class="sm:hidden text-lg font-bold text-green-600">Hi5ve</span>
                    </a>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-lg mx-8">
                    <form action="products.php" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   placeholder="ابحث عن المنتجات..." 
                                   value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="submit" class="absolute left-2 top-2 text-gray-500 hover:text-green-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-6">
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center gap-6">
                        <a href="<?= SITE_URL ?>" class="text-gray-700 hover:text-green-600 transition duration-300">الرئيسية</a>
                        <a href="products.php" class="text-gray-700 hover:text-green-600 transition duration-300">المنتجات</a>
                        <a href="blog.php" class="text-gray-700 hover:text-green-600 transition duration-300">المدونة</a>
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="relative group">
                                <button class="text-gray-700 hover:text-green-600 transition duration-300 flex items-center">
                                    <i class="fas fa-user ms-1"></i>
                                    <?= htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username'] ?? 'User') ?>
                                    <i class="fas fa-chevron-down me-1 text-xs"></i>
                                </button>
                                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                                    <div class="py-1">
                                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user ms-2"></i>الملف الشخصي
                                        </a>
                                        <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-shopping-bag ms-2"></i>طلباتي
                                        </a>
                                        <a href="wishlist.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-heart ms-2"></i>قائمة المفضلة
                                        </a>
                                        <?php if (isAdmin()): ?>
                                        <a href="admin/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-cog ms-2"></i>لوحة الإدارة
                                        </a>
                                        <?php endif; ?>
                                        <hr class="my-1">
                                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt ms-2"></i>تسجيل الخروج
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="text-gray-700 hover:text-green-600 transition duration-300">تسجيل الدخول</a>
                            <a href="register.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">إنشاء حساب</a>
                        <?php endif; ?>
                    </div>

                    <!-- Wishlist (for logged in users) -->
                    <?php if (isLoggedIn()): ?>
                    <a href="wishlist.php" class="relative text-gray-700 hover:text-green-600 transition duration-300" title="قائمة المفضلة">
                        <i class="fas fa-heart text-xl"></i>
                        <span id="wishlist-count" class="absolute -top-2 -left-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </a>
                    <?php endif; ?>

                    <!-- Comparison -->
                    <a href="compare.php" class="relative text-gray-700 hover:text-green-600 transition duration-300" title="مقارنة المنتجات">
                        <i class="fas fa-balance-scale text-xl"></i>
                        <span id="comparison-count" class="absolute -top-2 -left-2 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </a>

                    <!-- Cart -->
                    <a href="cart.php" class="relative text-gray-700 hover:text-green-600 transition duration-300" title="سلة التسوق">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="absolute -top-2 -left-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?= $cart_count ?? 0 ?>
                        </span>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-green-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Search -->
            <div class="md:hidden pb-4">
                <form action="products.php" method="GET">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="ابحث عن المنتجات..." 
                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="submit" class="absolute left-2 top-2 text-gray-500 hover:text-green-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden border-t border-gray-200 pt-4 pb-4">
                <div class="flex flex-col space-y-4">
                    <a href="<?= SITE_URL ?>" class="text-gray-700 hover:text-green-600 transition duration-300">الرئيسية</a>
                    <a href="products.php" class="text-gray-700 hover:text-green-600 transition duration-300">المنتجات</a>
                    <a href="blog.php" class="text-gray-700 hover:text-green-600 transition duration-300">المدونة</a>
                    <a href="compare.php" class="text-gray-700 hover:text-green-600 transition duration-300 flex items-center">
                        <i class="fas fa-balance-scale ms-2"></i>مقارنة المنتجات
                        <span id="mobile-comparison-count" class="ms-auto bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-500 mb-2">مرحباً، <?= htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username'] ?? 'User') ?>!</p>
                            <a href="profile.php" class="block text-gray-700 hover:text-green-600 transition duration-300 mb-2">
                                <i class="fas fa-user ms-2"></i>الملف الشخصي
                            </a>
                            <a href="orders.php" class="block text-gray-700 hover:text-green-600 transition duration-300 mb-2">
                                <i class="fas fa-shopping-bag ms-2"></i>طلباتي
                            </a>
                            <a href="wishlist.php" class="block text-gray-700 hover:text-green-600 transition duration-300 mb-2 flex items-center">
                                <i class="fas fa-heart ms-2"></i>قائمة المفضلة
                                <span id="mobile-wishlist-count" class="ms-auto bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="admin/" class="block text-gray-700 hover:text-green-600 transition duration-300 mb-2">
                                <i class="fas fa-cog ms-2"></i>لوحة الإدارة
                            </a>
                            <?php endif; ?>
                            <a href="logout.php" class="block text-red-600 hover:text-red-700 transition duration-300">
                                <i class="fas fa-sign-out-alt ms-2"></i>تسجيل الخروج
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <a href="login.php" class="block text-gray-700 hover:text-green-600 transition duration-300">تسجيل الدخول</a>
                            <a href="register.php" class="block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 text-center">إنشاء حساب</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- WhatsApp Float Button -->
    <a href="<?= getWhatsAppLink('Hello! I need assistance with Bin Ali Store.') ?>" 
       target="_blank" 
       class="whatsapp-float">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Update all counters on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
            updateComparisonCount();
            
            // Hide loading screen after a short delay
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.classList.add('fade-out');
                    setTimeout(() => loadingScreen.remove(), 500);
                }
            }, 1000);
        });

        function updateCartCount() {
            fetch('ajax/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                document.getElementById('cart-count').textContent = count;
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
        }

        function updateWishlistCount() {
            <?php if (isLoggedIn()): ?>
            fetch('ajax/phase3.php?action=get_wishlist_count')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                const wishlistCount = document.getElementById('wishlist-count');
                const mobileWishlistCount = document.getElementById('mobile-wishlist-count');
                
                if (wishlistCount) wishlistCount.textContent = count;
                if (mobileWishlistCount) mobileWishlistCount.textContent = count;
                
                // Hide counter if zero
                if (count === 0) {
                    if (wishlistCount) wishlistCount.style.display = 'none';
                    if (mobileWishlistCount) mobileWishlistCount.style.display = 'none';
                } else {
                    if (wishlistCount) wishlistCount.style.display = 'flex';
                    if (mobileWishlistCount) mobileWishlistCount.style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error updating wishlist count:', error);
            });
            <?php endif; ?>
        }

        function updateComparisonCount() {
            fetch('ajax/phase3.php?action=get_comparison_list')
            .then(response => response.json())
            .then(data => {
                const count = data.count || 0;
                const comparisonCount = document.getElementById('comparison-count');
                const mobileComparisonCount = document.getElementById('mobile-comparison-count');
                
                if (comparisonCount) comparisonCount.textContent = count;
                if (mobileComparisonCount) mobileComparisonCount.textContent = count;
                
                // Hide counter if zero
                if (count === 0) {
                    if (comparisonCount) comparisonCount.style.display = 'none';
                    if (mobileComparisonCount) mobileComparisonCount.style.display = 'none';
                } else {
                    if (comparisonCount) comparisonCount.style.display = 'flex';
                    if (mobileComparisonCount) mobileComparisonCount.style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error updating comparison count:', error);
            });
        }

        // Global functions for other pages to use
        window.updateCartCount = updateCartCount;
        window.updateWishlistCount = updateWishlistCount;
        window.updateComparisonCount = updateComparisonCount;
    </script>
</body>
</html> 