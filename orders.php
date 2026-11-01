<?php
require_once 'config/config.php';
require_once 'classes/Order.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirectTo('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$order = new Order();

// Get parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$limit = 10;

// Get user orders
$orders = $order->getUserOrders($_SESSION['user_id'], $limit, ($page - 1) * $limit, $status);
$total_orders = $order->getUserOrdersCount($_SESSION['user_id'], $status);
$total_pages = ceil($total_orders / $limit);

$page_title = "طلباتي";
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">طلباتي</h1>
                <p class="text-gray-600">
                    <?php if ($total_orders > 0): ?>
                        تعرض <?= min($limit, $total_orders - (($page - 1) * $limit)) ?> من <?= $total_orders ?> طلبات
                    <?php else: ?>
                        لا توجد طلبات
                    <?php endif; ?>
                </p>
            </div>
            
            <div class="mt-4 md:mt-0 flex gap-4">
                <a href="products.php" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                    <i class="fas fa-shopping-cart ms-2"></i>متابعة التسوق
                </a>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">تصفية الطلبات</h3>
            <div class="flex flex-wrap gap-2">
                <a href="orders.php" 
                   class="px-4 py-2 rounded-lg <?= empty($status) ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition duration-300">
                    جميع الطلبات
                </a>
                <a href="orders.php?status=pending" 
                   class="px-4 py-2 rounded-lg <?= $status === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition duration-300">
                    قيد الانتظار
                </a>
                <a href="orders.php?status=confirmed" 
                   class="px-4 py-2 rounded-lg <?= $status === 'confirmed' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition duration-300">
                    مؤكدة
                </a>
                <a href="orders.php?status=delivered" 
                   class="px-4 py-2 rounded-lg <?= $status === 'delivered' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition duration-300">
                    تم التسليم
                </a>
                <a href="orders.php?status=cancelled" 
                   class="px-4 py-2 rounded-lg <?= $status === 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> transition duration-300">
                    ملغاة
                </a>
            </div>
        </div>

        <!-- Orders List -->
        <?php if (empty($orders)): ?>
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="mb-8">
                <i class="fas fa-shopping-bag text-gray-300 text-6xl"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-600 mb-4">
                <?php if ($status): ?>
                    لم يتم العثور على طلبات <?= ['pending' => 'قيد الانتظار', 'confirmed' => 'مؤكدة', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغاة'][$status] ?? htmlspecialchars($status) ?>
                <?php else: ?>
                    لا توجد طلبات
                <?php endif; ?>
            </h2>
            <p class="text-gray-500 mb-8">
                <?php if ($status): ?>
                    لا يوجد لديك أي طلبات <?= ['pending' => 'قيد الانتظار', 'confirmed' => 'مؤكدة', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغاة'][$status] ?? htmlspecialchars($status) ?> حتى الآن.
                <?php else: ?>
                    لم تقم بإضافة أي طلبات بعد. ابدأ بالتسوق لرؤية طلباتك هنا.
                <?php endif; ?>
            </p>
            <div class="gap-4 flex justify-center">
                <a href="products.php" 
                   class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                    <i class="fas fa-shopping-cart ms-2"></i>ابدأ التسوق
                </a>
                <a href="<?= getWhatsAppLink('Hello! I would like to place an order.') ?>" 
                   target="_blank" 
                   class="inline-block bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition duration-300">
                    <i class="fab fa-whatsapp ms-2"></i>اطلب عبر واتساب
                </a>
            </div>
        </div>
        <?php else: ?>
        
        <div class="space-y-6">
            <?php foreach ($orders as $order_item): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Order Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                الطلب #<?= htmlspecialchars($order_item['order_id']) ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                تم الطلب في <?= date('M j, Y g:i A', strtotime($order_item['created_at'])) ?>
                            </p>
                        </div>
                        <div class="mt-2 md:mt-0 flex items-center gap-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?php
                                switch ($order_item['status']) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'confirmed':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'delivered':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?= ['pending' => 'قيد الانتظار', 'confirmed' => 'مؤكد', 'delivered' => 'تم التسليم', 'cancelled' => 'ملغى'][$order_item['status']] ?? ucfirst($order_item['status']) ?>
                            </span>
                            <span class="text-lg font-bold text-green-600">
                                <?= formatCurrency($order_item['total_amount']) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="px-6 py-4">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">معلومات الطلب</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-gray-600">طريقة الدفع:</span> <?= ['cash_on_delivery' => 'الدفع عند الاستلام', 'online_payment' => 'الدفع الإلكتروني', 'bank_transfer' => 'تحويل بنكي', 'whatsapp' => 'واتساب'][$order_item['payment_method']] ?? ucfirst($order_item['payment_method']) ?></p>
                                <p><span class="text-gray-600">رسوم التوصيل:</span> <?= formatCurrency($order_item['delivery_fee']) ?></p>
                                <?php if ($order_item['notes']): ?>
                                <p><span class="text-gray-600">ملاحظات:</span> <?= htmlspecialchars($order_item['notes']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">معلومات التوصيل</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-gray-600">الاسم:</span> <?= htmlspecialchars($order_item['customer_name']) ?></p>
                                <p><span class="text-gray-600">الهاتف:</span> <?= htmlspecialchars($order_item['customer_phone']) ?></p>
                                <p><span class="text-gray-600">العنوان:</span> <?= htmlspecialchars($order_item['customer_address']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 md:gap-0">
                        <div class="flex gap-4">
                            <a href="order-details.php?id=<?= $order_item['id'] ?>" 
                               class="text-green-600 hover:text-green-700 font-medium">
                                <i class="fas fa-eye ms-1"></i>عرض التفاصيل
                            </a>
                            
                            <?php if ($order_item['status'] === 'pending'): ?>
                            <a href="<?= getWhatsAppLink('Hello! I would like to modify my order #' . $order_item['order_id']) ?>" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fab fa-whatsapp ms-1"></i>تعديل الطلب
                            </a>
                            <?php endif; ?>
                            
                            <a href="<?= getWhatsAppLink('Hello! I need help with my order #' . $order_item['order_id']) ?>" 
                               target="_blank" 
                               class="text-gray-600 hover:text-gray-700 font-medium">
                                <i class="fas fa-headset ms-1"></i>احصل على المساعدة
                            </a>
                        </div>
                        
                        <?php if ($order_item['status'] === 'delivered'): ?>
                        <a href="products.php" 
                           class="bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition duration-300">
                            <i class="fas fa-redo ms-2"></i>إعادة الطلب
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center mt-8">
            <nav class="flex items-center gap-2">
                <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                   class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                    <i class="fas fa-chevron-left"></i>
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
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 