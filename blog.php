<?php
require_once 'config/config.php';
require_once 'classes/Blog.php';

$blog = new Blog();

// Pagination
$limit = 6;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Get published blog posts
$posts = $blog->getPublished($limit, $offset);
$total_posts = $blog->getTotalCount('published');
$total_pages_count = ceil($total_posts / $limit);

$page_title = "المدونة - متجر بن علي";
$meta_description = "اقرأ آخر الأخبار والنصائح والتحديثات من متجر بن علي. ابقَ مطلعًا على منتجاتنا وخدماتنا ومجتمعنا.";

include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">مدونتنا</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                ابقَ على اطلاع بأحدث الأخبار والنصائح والرؤى من متجر بن علي. اكتشف المنتجات الجديدة ونصائح الطهي وقصص المجتمع.
            </p>
        </div>

        <?php if (empty($posts)): ?>
        <!-- No Posts -->
        <div class="text-center py-16">
            <i class="fas fa-blog text-6xl text-gray-300 mb-6"></i>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">لا توجد منشورات في المدونة بعد</h2>
            <p class="text-gray-500 mb-8">نحن نعمل على تقديم محتوى رائع لك. عد قريبًا!</p>
            <a href="/" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                <i class="fas fa-home ms-2"></i>العودة إلى الرئيسية
            </a>
        </div>
        <?php else: ?>
        <!-- Blog Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Featured Image -->
                <?php if ($post['featured_image']): ?>
                <div class="aspect-w-16 aspect-h-9">
                    <img src="<?= htmlspecialchars($post['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($post['title']) ?>"
                         class="w-full h-48 object-cover">
                </div>
                <?php else: ?>
                <div class="w-full h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                    <i class="fas fa-blog text-4xl text-white"></i>
                </div>
                <?php endif; ?>
                
                <!-- Post Content -->
                <div class="p-6">
                    <!-- Meta Info -->
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <i class="fas fa-user ms-2"></i>
                        <span><?= htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) ?></span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-calendar ms-2"></i>
                        <span><?= date('M j, Y', strtotime($post['published_at'])) ?></span>
                    </div>
                    
                    <!-- Title -->
                    <h2 class="text-xl font-semibold text-gray-900 mb-3 line-clamp-2">
                        <a href="blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" 
                           class="hover:text-green-600 transition duration-300">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h2>
                    
                    <!-- Excerpt -->
                    <?php if ($post['excerpt']): ?>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </p>
                    <?php else: ?>
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        <?= htmlspecialchars(substr(strip_tags($post['content']), 0, 150)) ?>...
                    </p>
                    <?php endif; ?>
                    
                    <!-- Read More -->
                    <a href="blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" 
                       class="inline-flex items-center text-green-600 font-semibold hover:text-green-700 transition duration-300">
                        اقرأ المزيد
                        <i class="fas fa-arrow-left me-2"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages_count > 1): ?>
        <div class="flex justify-center">
            <nav class="flex items-center gap-2">
                <!-- Previous Page -->
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?>" 
                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                    <i class="fas fa-chevron-right ms-2"></i>السابق
                </a>
                <?php endif; ?>
                
                <!-- Page Numbers -->
                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages_count, $current_page + 2);
                
                if ($start_page > 1): ?>
                <a href="?page=1" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">1</a>
                <?php if ($start_page > 2): ?>
                <span class="px-2 py-2 text-gray-500">...</span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="px-4 py-2 border rounded-lg transition duration-300 <?= $i === $current_page ? 'bg-green-600 text-white border-green-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($end_page < $total_pages_count): ?>
                <?php if ($end_page < $total_pages_count - 1): ?>
                <span class="px-2 py-2 text-gray-500">...</span>
                <?php endif; ?>
                <a href="?page=<?= $total_pages_count ?>" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300"><?= $total_pages_count ?></a>
                <?php endif; ?>
                
                <!-- Next Page -->
                <?php if ($current_page < $total_pages_count): ?>
                <a href="?page=<?= $current_page + 1 ?>" 
                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                    التالي<i class="fas fa-chevron-left ms-2"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        
        <!-- Newsletter Subscription -->
        <div class="bg-green-600 rounded-lg p-8 text-center text-white mt-16">
            <h3 class="text-2xl font-bold mb-4">ابقَ على اطلاع</h3>
            <p class="text-green-100 mb-6">احصل على أحدث منشورات المدونة والتحديثات عبر واتسابك</p>
            <a href="https://wa.me/<?= WHATSAPP_NUMBER ?>?text=Hi! I'd like to subscribe to your blog updates" 
               target="_blank"
               class="bg-white text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                <i class="fab fa-whatsapp ms-2"></i>اشترك عبر واتساب
            </a>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php include 'includes/footer.php'; ?> 