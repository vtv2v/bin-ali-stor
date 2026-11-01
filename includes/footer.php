<?php
// تأكد من تحميل كلاس Category قبل استخدامه
if (!class_exists('Category')) {
    require_once __DIR__ . '/../classes/Category.php';
}
?>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center mb-4">
                        <img src="assets/images/logo.png" alt="Bin Ali Store" class="h-8 w-auto ms-2">
                        <h3 class="text-xl font-bold">Bin Ali Store</h3>
                    </div>
                    <p class="text-gray-300 mb-4">
                        متجرك الشامل للإلكترونيات والهواتف والأجهزة الكهربائية وحلول الطاقة الشمسية.
                    </p>
                    <div class="flex gap-4">
                        <a href="<?= getWhatsAppLink('Hello! I would like to know more about Bin Ali Store.') ?>" 
                           target="_blank" 
                           class="text-green-400 hover:text-green-300 transition duration-300">
                            <i class="fab fa-whatsapp text-2xl"></i>
                        </a>
                        <a href="#" class="text-blue-400 hover:text-blue-300 transition duration-300">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="#" class="text-blue-400 hover:text-blue-300 transition duration-300">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="#" class="text-pink-400 hover:text-pink-300 transition duration-300">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">روابط سريعة</h4>
                    <ul class="space-y-2">
                        <li><a href="<?= SITE_URL ?>" class="text-gray-300 hover:text-white transition duration-300">الرئيسية</a></li>
                        <li><a href="products.php" class="text-gray-300 hover:text-white transition duration-300">جميع المنتجات</a></li>
                        <li><a href="products.php?featured=1" class="text-gray-300 hover:text-white transition duration-300">المنتجات المميزة</a></li>
                        <li><a href="cart.php" class="text-gray-300 hover:text-white transition duration-300">سلة التسوق</a></li>
                        <?php if (isLoggedIn()): ?>
                        <li><a href="orders.php" class="text-gray-300 hover:text-white transition duration-300">طلباتي</a></li>
                        <li><a href="profile.php" class="text-gray-300 hover:text-white transition duration-300">ملفي الشخصي</a></li>
                        <?php else: ?>
                        <li><a href="login.php" class="text-gray-300 hover:text-white transition duration-300">تسجيل الدخول</a></li>
                        <li><a href="register.php" class="text-gray-300 hover:text-white transition duration-300">إنشاء حساب</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">الفئات</h4>
                    <ul class="space-y-2">
                        <?php
                        $footer_categories = (new Category())->getCategoriesWithProducts();
                        foreach (array_slice($footer_categories, 0, 6) as $cat):
                        ?>
                        <li>
                            <a href="products.php?category=<?= $cat['id'] ?>" 
                               class="text-gray-300 hover:text-white transition duration-300">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">اتصل بنا</h4>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fab fa-whatsapp text-green-400 ms-3"></i>
                            <div>
                                <p class="text-gray-300">واتساب</p>
                                <a href="<?= getWhatsAppLink('Hello! I need assistance.') ?>" 
                                   target="_blank" 
                                   class="text-white hover:text-green-400 transition duration-300">
                                    <?= WHATSAPP_NUMBER ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-blue-400 ms-3"></i>
                            <div>
                                <p class="text-gray-300">البريد الإلكتروني</p>
                                <a href="mailto:support@hi5ve.com" 
                                   class="text-white hover:text-blue-400 transition duration-300">
                                    support@hi5ve.com
                                </a>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-400 ms-3"></i>
                            <div>
                                <p class="text-gray-300">ساعات العمل</p>
                                <p class="text-white">السبت - الخميس: 8 صباحاً - 8 مساءً</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Banners -->
            <?php 
            if (file_exists(__DIR__ . '/banner_display.php')) {
                require_once __DIR__ . '/banner_display.php';
                displayBanners('footer');
            }
            ?>

            <!-- Payment Methods -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <h5 class="text-lg font-semibold mb-2">طرق الدفع</h5>
                        <div class="flex gap-4">
                            <div class="bg-white rounded px-3 py-2">
                                <span class="text-gray-800 font-semibold">الدفع الإلكتروني</span>
                            </div>
                            <div class="bg-green-600 rounded px-3 py-2">
                                <span class="text-white font-semibold">الدفع عند الاستلام</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center md:text-start">
                        <h5 class="text-lg font-semibold mb-2">اطلب عبر واتساب</h5>
                        <a href="<?= getWhatsAppLink('Hello! I would like to place an order.') ?>" 
                           target="_blank" 
                           class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-300 inline-flex items-center">
                            <i class="fab fa-whatsapp ms-2"></i>
                            اطلب الآن
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300">
                    &copy; <?= date('Y') ?> Bin Ali Store. جميع الحقوق محفوظة. | 
                    تم تصميم هذا المتجر بعناية لتقديم الإلكترونيات والهواتف والأجهزة الكهربائية وحلول الطاقة الشمسية في اليمن
                </p>
                <p class="text-gray-400 text-sm mt-2">
                    مدعوم بـ PHP & MySQL | العملة: الريال اليمني (ر.ي)
                </p>
            </div>
        </div>
    </footer>

    <!-- Additional Scripts -->
    <script>
        // Hide loading screen when page is loaded
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                setTimeout(() => {
                    loadingScreen.classList.add('fade-out');
                    setTimeout(() => {
                        loadingScreen.style.display = 'none';
                    }, 500);
                }, 500); // Show loading for at least 500ms
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-hide notifications
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
