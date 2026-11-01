<?php
require_once 'config/config.php';
require_once 'classes/Product.php';
require_once 'classes/Category.php';
require_once 'classes/Cart.php';
require_once 'includes/banner_display.php';

$product = new Product();
$category = new Category();
$cart = new Cart();

// Get parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$featured = isset($_GET['featured']) ? true : false;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Get products based on filters
if ($featured) {
    $products = $product->getFeatured($limit, $offset);
    $total_products = $product->getFeaturedCount();
    $page_title = "منتجات مميزة";
} elseif ($search) {
    $products = $product->search($search, $limit, $offset);
    $total_products = $product->getSearchCount($search);
    $page_title = "نتائج البحث عن '" . htmlspecialchars($search) . "'";
} elseif ($category_id) {
    $products = $product->getByCategory($category_id, $limit, $offset);
    $total_products = $product->getCategoryCount($category_id);
    $category_info = $category->getById($category_id);
    $page_title = $category_info ? $category_info['name'] . " منتجات" : "منتجات الفئة";
} else {
    $products = $product->getAll($limit, $offset);
    $total_products = $product->getTotalCount();
    $page_title = "جميع المنتجات";
}

// Calculate pagination
$total_pages = ceil($total_products / $limit);

// Get all categories for filter
$categories = $category->getAll();

// Get cart count
$cart_count = $cart->getCount(isLoggedIn() ? $_SESSION['user_id'] : null);

include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Category Top Banners -->
    <?php if ($category_id): ?>
        <?php displayBanners('category_top'); ?>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= $page_title ?></h1>
            <p class="text-gray-600">
                <?php if ($total_products > 0): ?>
                    عرض <?= min($limit, $total_products - $offset) ?> من أصل <?= $total_products ?> منتج
                <?php else: ?>
                    لا توجد منتجات
                <?php endif; ?>
            </p>
        </div>
        
        <!-- Sort Options -->
        <div class="mt-4 md:mt-0">
            <select id="sort-select" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">ترتيب حسب</option>
                <option value="name_asc">الاسم (أ-ي)</option>
                <option value="name_desc">الاسم (ي-أ)</option>
                <option value="price_asc">السعر (من الأقل إلى الأعلى)</option>
                <option value="price_desc">السعر (من الأعلى إلى الأقل)</option>
                <option value="newest">الأحدث أولاً</option>
            </select>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                <h3 class="text-lg font-semibold mb-4">الفلاتر</h3>
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث عن المنتجات</label>
                    <form action="" method="GET" class="flex">
                        <input type="text" 
                               name="search" 
                               value="<?= htmlspecialchars($search) ?>" 
                               placeholder="ابحث عن المنتجات..."
                               class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="submit" 
                                class="bg-green-600 text-white px-4 py-2 rounded-l-lg hover:bg-green-700 transition duration-300">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئات</label>
                    <div class="space-y-2">
                        <a href="products.php" 
                           class="block px-3 py-2 rounded <?= !$category_id ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-gray-100' ?> transition duration-300">
                            جميع الفئات
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="products.php?category=<?= $cat['id'] ?>" 
                           class="block px-3 py-2 rounded <?= $category_id == $cat['id'] ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-gray-100' ?> transition duration-300">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">روابط سريعة</label>
                    <div class="space-y-2">
                        <a href="products.php?featured=1" 
                           class="block px-3 py-2 rounded <?= $featured ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-gray-100' ?> transition duration-300">
                            <i class="fas fa-star ms-2"></i>منتجات مميزة
                        </a>
                        <a href="cart.php" 
                           class="block px-3 py-2 rounded text-gray-700 hover:bg-gray-100 transition duration-300">
                            <i class="fas fa-shopping-cart ms-2"></i>عرض السلة (<?= $cart_count ?>)
                        </a>
                    </div>
                </div>

                <!-- Sidebar Banners -->
                <?php displayBanners('sidebar'); ?>

                <!-- WhatsApp Order -->
                <div class="bg-green-50 rounded-lg p-4 mt-6">
                    <h4 class="font-semibold text-green-800 mb-2">هل تحتاج إلى مساعدة؟</h4>
                    <p class="text-sm text-green-700 mb-3">اطلب مباشرة عبر واتساب للحصول على المساعدة الشخصية</p>
                    <a href="<?= getWhatsAppLink('Hello! I would like to browse your products and place an order.') ?>" 
                       target="_blank" 
                       class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 flex items-center justify-center">
                        <i class="fab fa-whatsapp ms-2"></i>
                        اطلب عبر واتساب
                    </a>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <?php if (empty($products)): ?>
            <!-- No Products Found -->
            <div class="text-center py-16">
                <div class="mb-8">
                    <i class="fas fa-search text-gray-300 text-6xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-600 mb-4">لا توجد منتجات</h2>
                <p class="text-gray-500 mb-8">
                    <?php if ($search): ?>
                        جرّب تعديل كلمات البحث أو تصفح الفئات المتاحة.
                    <?php else: ?>
                        نعمل حالياً على إضافة المزيد من المنتجات، يُرجى العودة لاحقاً!
                    <?php endif; ?>
                </p>
                <div class="gap-4 flex justify-center">
                    <a href="products.php" 
                       class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                        عرض جميع المنتجات
                    </a>
                    <a href="<?= getWhatsAppLink('Hello! I am looking for specific products. Can you help me?') ?>" 
                       target="_blank" 
                       class="inline-block bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition duration-300">
                        <i class="fab fa-whatsapp ms-2"></i>
                        اسأل عبر واتساب
                    </a>
                </div>
            </div>
            <?php else: ?>
            
            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php foreach ($products as $prod): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card transition duration-300 hover:shadow-lg">
                    <!-- Product Image -->
                    <div class="relative">
                        <img src="<?= $prod['image'] ? 'uploads/products/' . $prod['image'] : 'get_placeholder_image.php?w=300&h=200&text=Product' ?>" 
                             alt="<?= htmlspecialchars($prod['name']) ?>" 
                             class="w-full h-48 object-cover">
                        
                        <?php if ($prod['featured']): ?>
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-semibold">
                            <i class="fas fa-star ms-1"></i>مميز
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($prod['discount_price']): ?>
                        <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                            <?= round((($prod['price'] - $prod['discount_price']) / $prod['price']) * 100) ?>% خصم
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($prod['stock_quantity'] <= 0): ?>
                        <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                            <span class="text-white font-semibold">غير متوفر</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                            <?= htmlspecialchars($prod['name']) ?>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                            <?= htmlspecialchars($prod['description']) ?>
                        </p>
                        
                        <!-- Price -->
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <?php if ($prod['discount_price']): ?>
                                <span class="text-lg font-bold text-green-600">
                                    <?= formatCurrency($prod['discount_price']) ?>
                                </span>
                                <span class="text-sm text-gray-500 line-through ms-2">
                                    <?= formatCurrency($prod['price']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-lg font-bold text-green-600">
                                    <?= formatCurrency($prod['price']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <span class="text-sm text-gray-500">
                                لكل <?= htmlspecialchars($prod['unit']) ?>
                            </span>
                        </div>
                        
                        <!-- Stock Info -->
                        <div class="mb-4">
                            <?php if ($prod['stock_quantity'] > 0): ?>
                                <?php if ($prod['stock_quantity'] <= 10): ?>
                                <span class="text-orange-600 text-sm">
                                    <i class="fas fa-exclamation-triangle ms-1"></i>
                                    فقط <?= $prod['stock_quantity'] ?> متبقياً في المخزون
                                </span>
                                <?php else: ?>
                                <span class="text-green-600 text-sm">
                                    <i class="fas fa-check-circle ms-1"></i>
                                    متوفر حالياً (<?= $prod['stock_quantity'] ?> متاح)
                                </span>
                                <?php endif; ?>
                            <?php else: ?>
                            <span class="text-red-600 text-sm">
                                <i class="fas fa-times-circle ms-1"></i>
                                غير متوفر
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Actions -->
                        <div class="space-y-2">
                            <?php if ($prod['stock_quantity'] > 0): ?>
                            <button onclick="addToCart(<?= $prod['id'] ?>)" 
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition duration-300 flex items-center justify-center">
                                <i class="fas fa-cart-plus ms-2"></i>
                                أضف إلى السلة
                            </button>
                            <?php else: ?>
                            <button disabled 
                                    class="w-full bg-gray-400 text-white py-2 px-4 rounded-lg font-semibold cursor-not-allowed">
                                <i class="fas fa-times ms-2"></i>
                                غير متوفر
                            </button>
                            <?php endif; ?>
                            
                            <a href="<?= getWhatsAppLink('Hello! I am interested in: ' . $prod['name'] . ' - ' . formatCurrency($prod['discount_price'] ?: $prod['price'])) ?>" 
                               target="_blank" 
                               class="w-full bg-green-500 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-600 transition duration-300 flex items-center justify-center">
                                <i class="fab fa-whatsapp ms-2"></i>
                                اطلب عبر واتساب
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center">
                <nav class="flex items-center gap-2">
                    <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                       class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="px-3 py-2 <?= $i == $page ? 'bg-green-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' ?> rounded-lg transition duration-300">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                       class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Add to cart function
function addToCart(productId, quantity = 1) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();
            
            // Show success message
            showNotification('تمت إضافة المنتج إلى السلة!', 'success');
        } else {
            showNotification(data.message || 'فشل في إضافة المنتج إلى السلة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ، يرجى المحاولة مرة أخرى.', 'error');
    });
}

// Update cart count
function updateCartCount() {
    fetch('ajax/cart.php?action=count')
    .then(response => response.json())
    .then(data => {
        document.getElementById('cart-count').textContent = data.count || 0;
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 z-50 px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Slide in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Slide out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Sort functionality
document.getElementById('sort-select').addEventListener('change', function() {
    const sortValue = this.value;
    if (sortValue) {
        const url = new URL(window.location);
        url.searchParams.set('sort', sortValue);
        window.location.href = url.toString();
    }
});

// Set current sort value
const urlParams = new URLSearchParams(window.location.search);
const currentSort = urlParams.get('sort');
if (currentSort) {
    document.getElementById('sort-select').value = currentSort;
}
</script>

<?php include 'includes/footer.php'; ?> 