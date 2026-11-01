<?php
session_start();
require_once 'config/config.php';
require_once 'classes/Order.php';
require_once 'classes/PaymentGateway.php';

$order = new Order();
$paymentGateway = new PaymentGateway();

// Get order ID from URL
$order_id = $_GET['order_id'] ?? '';
$payment_status = $_GET['payment'] ?? '';
$payment_method = $_GET['payment_method'] ?? '';

if (empty($order_id)) {
    header('Location: index.php');
    exit();
}

// Get order details
$order_details = $order->getByOrderId($order_id);

if (!$order_details) {
    header('Location: index.php');
    exit();
}

// Get payment details
$payment_details = $paymentGateway->getOrderPayments($order_id);
$latest_payment = !empty($payment_details) ? $payment_details[0] : null;

// Check for payment success/error messages
$payment_success = $_SESSION['payment_success'] ?? null;
$payment_error = $_SESSION['payment_error'] ?? null;

// Clear session messages
unset($_SESSION['payment_success'], $_SESSION['payment_error']);

include 'includes/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Payment Status Alert -->
            <?php if ($payment_success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>تم الدفع بنجاح!</strong> تم معالجة دفعتك البالغة ر.ي<?php echo number_format($payment_success['amount'], 2); ?> 
                    بنجاح عبر <?php echo $payment_success['gateway']; ?>.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($payment_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>فشل الدفع!</strong> <?php echo htmlspecialchars($payment_error['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Order Confirmation Card -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                تم تأكيد الطلب!
                            </h4>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-light text-dark fs-6">
                                #<?php echo htmlspecialchars($order_details['order_id']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Thank You Message -->
                    <div class="text-center mb-4">
                        <i class="fas fa-shopping-bag text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">شكرًا لك على طلبك، <?php echo htmlspecialchars($order_details['customer_name']); ?>!</h5>
                        <p class="text-muted">
                            لقد استلمنا طلبك وسنبدأ في معالجته قريبًا. 
                            ستصلك تحديثات عبر واتساب والبريد الإلكتروني.
                        </p>
                    </div>

                    <!-- Order Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">حالة الطلب</h6>
                                    <?php
                                    $status_colors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'processing' => 'primary',
                                        'shipped' => 'secondary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $status_color = $status_colors[$order_details['order_status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $status_color; ?> fs-6">
                                        <?php echo ucfirst($order_details['order_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">حالة الدفع</h6>
                                    <?php
                                    $payment_colors = [
                                        'pending' => 'warning',
                                        'paid' => 'success',
                                        'failed' => 'danger'
                                    ];
                                    $payment_color = $payment_colors[$order_details['payment_status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $payment_color; ?> fs-6">
                                        <?php echo ucfirst($order_details['payment_status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">معلومات العميل</h6>
                            <p class="mb-1"><strong>الاسم:</strong> <?php echo htmlspecialchars($order_details['customer_name']); ?></p>
                            <p class="mb-1"><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($order_details['customer_email']); ?></p>
                            <p class="mb-1"><strong>الهاتف:</strong> <?php echo htmlspecialchars($order_details['customer_phone']); ?></p>
                            <p class="mb-3"><strong>العنوان:</strong> <?php echo nl2br(htmlspecialchars($order_details['delivery_address'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">معلومات الطلب</h6>
                            <p class="mb-1"><strong>تاريخ الطلب:</strong> <?php echo date('M d, Y g:i A', strtotime($order_details['created_at'])); ?></p>
                            <p class="mb-1"><strong>طريقة الدفع:</strong> 
                                <?php 
                                $payment_method_names = [
                                    'paystack_card' => 'بطاقة ائتمان/خصم (Paystack)',
                                    'paystack_bank' => 'تحويل بنكي (Paystack)',
                                    'paystack_ussd' => 'الدفع عبر USSD',
                                    'flutterwave_card' => 'بطاقة ائتمان/خصم (Flutterwave)',
                                    'flutterwave_bank' => 'تحويل بنكي (Flutterwave)',
                                    'whatsapp_payment' => 'الدفع عبر واتساب',
                                    'bank_transfer' => 'تحويل بنكي مباشر',
                                    'cod' => 'الدفع عند الاستلام'
                                ];
                                echo $payment_method_names[$order_details['payment_method']] ?? ucfirst(str_replace('_', ' ', $order_details['payment_method']));
                                ?>
                            </p>
                            <?php if ($order_details['estimated_delivery']): ?>
                                <p class="mb-1"><strong>التسليم المتوقع:</strong> <?php echo date('M d, Y', strtotime($order_details['estimated_delivery'])); ?></p>
                            <?php endif; ?>
                            <?php if ($latest_payment && $latest_payment['gateway_reference']): ?>
                                <p class="mb-1"><strong>مرجع الدفع:</strong> <code><?php echo htmlspecialchars($latest_payment['gateway_reference']); ?></code></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h6 class="border-bottom pb-2 mt-4">عناصر الطلب</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th class="text-end">المجموع الفرعي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_details['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image']): ?>
                                                    <img src="uploads/products/<?php echo htmlspecialchars($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                         class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php endif; ?>
                                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                            </div>
                                        </td>
                                        <td>ر.ي<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?> <?php echo $item['unit'] ?? 'قطعة'; ?></td>
                                        <td class="text-end">ر.ي<?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">المجموع الفرعي:</th>
                                    <th class="text-end">ر.ي<?php echo number_format($order_details['total_amount'] - 500, 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="3">رسوم التوصيل:</th>
                                    <th class="text-end">ر.ي500.00</th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="3">الإجمالي:</th>
                                    <th class="text-end">ر.ي<?php echo number_format($order_details['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Special Instructions -->
                    <?php if ($order_details['notes']): ?>
                        <h6 class="border-bottom pb-2 mt-4">تعليمات خاصة</h6>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($order_details['notes'])); ?></p>
                    <?php endif; ?>

                    <!-- Payment Instructions for Bank Transfer -->
                    <?php if ($order_details['payment_method'] === 'bank_transfer' && $order_details['payment_status'] === 'pending'): ?>
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-university me-2"></i>تعليمات التحويل البنكي</h6>
                            <p class="mb-2">يرجى تحويل مبلغ ر.ي<?php echo number_format($order_details['total_amount'], 2); ?> إلى الحساب التالي:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>اسم البنك:</strong> بنك التضامن</p>
                                    <p class="mb-1"><strong>اسم الحساب:</strong> Bin Ali Store</p>
                                    <p class="mb-1"><strong>رقم الحساب:</strong> 1234567890</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>المرجع:</strong> <?php echo $order_details['order_id']; ?></p>
                                    <p class="mb-1"><strong>المبلغ:</strong> ر.ي<?php echo number_format($order_details['total_amount'], 2); ?></p>
                                </div>
                            </div>
                            <p class="mt-2 mb-0">
                                <small>بعد إجراء التحويل، يرجى إرسال إثبات الدفع إلى واتسابنا: +967776656060</small>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Cash on Delivery Instructions -->
                    <?php if ($order_details['payment_method'] === 'cod'): ?>
                        <div class="alert alert-warning mt-4">
                            <h6><i class="fas fa-money-bill-wave me-2"></i>الدفع عند الاستلام</h6>
                            <p class="mb-0">
                                يرجى تجهيز مبلغ ر.ي<?php echo number_format($order_details['total_amount'], 2); ?> عند وصول طلبك. 
                                سيقوم مندوب التوصيل بجمع المبلغ عند التسليم.
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- WhatsApp Payment Instructions -->
                    <?php if ($order_details['payment_method'] === 'whatsapp_payment'): ?>
                        <div class="alert alert-success mt-4">
                            <h6><i class="fab fa-whatsapp me-2"></i>الدفع عبر واتساب</h6>
                            <p class="mb-2">أكمل الدفعة عبر واتساب بمبلغ ر.ي<?php echo number_format($order_details['total_amount'], 2); ?></p>
                            
                            <?php if (isset($_SESSION['whatsapp_payment_link'])): ?>
                                <div class="d-grid gap-2 mb-3">
                                    <a href="<?php echo $_SESSION['whatsapp_payment_link']; ?>" 
                                       target="_blank" 
                                       class="btn btn-success">
                                        <i class="fab fa-whatsapp me-2"></i>متابعة الدفع عبر واتساب
                                    </a>
                                </div>
                                <?php unset($_SESSION['whatsapp_payment_link']); ?>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>خيارات الدفع:</strong></p>
                                    <ul class="mb-2">
                                        <li>تحويل بنكي</li>
                                        <li>تحويل عبر الهاتف المحمول</li>
                                        <li>إيداع نقدي</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>مرجع الطلب:</strong> <?php echo $order_details['order_id']; ?></p>
                                    <p class="mb-1"><strong>المبلغ:</strong> ر.ي<?php echo number_format($order_details['total_amount'], 2); ?></p>
                                    <?php if (isset($_GET['reference'])): ?>
                                        <p class="mb-1"><strong>مرجع الدفع:</strong> <code><?php echo htmlspecialchars($_GET['reference']); ?></code></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="bg-light p-3 rounded mt-3">
                                <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>حالة الدفع</h6>
                                <?php if ($order_details['payment_status'] === 'pending'): ?>
                                    <p class="mb-2 text-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        <strong>في انتظار تأكيد الدفع</strong>
                                    </p>
                                    <p class="mb-0 small">
                                        بعد إجراء الدفع عبر واتساب، سيؤكد مديرنا الدفع وسيقوم بتحديث حالة الطلب. 
                                        ستتلقى رسالة تأكيد بمجرد التحقق من الدفع.
                                    </p>
                                <?php elseif ($order_details['payment_status'] === 'paid'): ?>
                                    <p class="mb-0 text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        <strong>تم تأكيد الدفع!</strong> الطلب قيد المعالجة الآن.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="index.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-home me-2"></i>متابعة التسوق
                            </a>
                        </div>
                        <div class="col-md-6">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="account/orders.php" class="btn btn-primary w-100">
                                    <i class="fas fa-list me-2"></i>عرض جميع الطلبات
                                </a>
                            <?php else: ?>
                                <a href="track-order.php?order_id=<?php echo $order_details['order_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>تتبع هذا الطلب
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h6>هل تحتاج إلى مساعدة؟</h6>
                    <p class="text-muted mb-3">
                        إذا كان لديك أي أسئلة حول طلبك، فلا تتردد في التواصل معنا.
                    </p>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="https://wa.me/967776656060" class="btn btn-success btn-sm w-100" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i>واتساب
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="mailto:support@hi5vemarketplace.com" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-envelope me-1"></i>البريد الإلكتروني
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="tel:+967776656060" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-phone me-1"></i>اتصال
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-dismiss alerts after 10 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 10000);
</script>

<?php include 'includes/footer.php'; ?> 