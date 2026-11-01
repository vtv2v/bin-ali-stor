<?php
session_start();
require_once 'config/config.php';

$page_title = "مقارنة المنتجات";
include 'includes/header.php';

// Get comparison list from session
$user_id = $_SESSION['user_id'] ?? null;
$session_id = session_id();
$comparison_key = $user_id ? "comparison_user_{$user_id}" : "comparison_session_{$session_id}";
$comparison_list = $_SESSION[$comparison_key] ?? [];
?>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-balance-scale text-blue-500 me-3"></i>مقارنة المنتجات
                    </h1>
                    <p class="text-gray-600">قارن حتى 4 منتجات جنبًا إلى جنب</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex items-center gap-4">
                    <span class="text-sm text-gray-600">
                        <span id="comparison-count"><?= count($comparison_list) ?></span> من 4 منتجات
                    </span>
                    <?php if (!empty($comparison_list)): ?>
                    <button onclick="clearComparison()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                        <i class="fas fa-trash me-2"></i>مسح الكل
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (empty($comparison_list)): ?>
        <!-- Empty Comparison -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <i class="fas fa-balance-scale text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">لا توجد منتجات للمقارنة</h2>
                <p class="text-gray-600 mb-8">أضف منتجات إلى قائمة المقارنة لتراها جنبًا إلى جنب. يمكنك مقارنة المزايا والأسعار والمواصفات.</p>
                <a href="products.php" class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
                    <i class="fas fa-shopping-bag me-2"></i>
                    تصفح المنتجات
                </a>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Comparison Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div id="comparison-table" class="overflow-x-auto">
                <!-- Table will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Add More Products -->
        <?php if (count($comparison_list) < 4): ?>
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">إضافة منتجات أخرى</h3>
            <div class="flex items-center gap-4">
                <input type="text" id="product-search" placeholder="ابحث عن منتجات للإضافة..." 
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <button onclick="searchProducts()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-300">
                    <i class="fas fa-search me-2"></i>بحث
                </button>
            </div>
            
            <!-- Search Results -->
            <div id="search-results" class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 hidden">
                <!-- Results will be populated by JavaScript -->
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Product Details Modal -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">تفاصيل المنتج</h3>
                    <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="modal-content">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load comparison data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadComparisonTable();
    
    // Add search functionality
    document.getElementById('product-search')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchProducts();
        }
    });
});

function loadComparisonTable() {
    fetch('ajax/phase3.php?action=get_comparison_list')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                renderComparisonTable(data.products);
            }
        })
        .catch(error => {
            console.error('Error loading comparison:', error);
        });
}

function renderComparisonTable(products) {
    const tableContainer = document.getElementById('comparison-table');
    
    if (products.length === 0) {
        tableContainer.innerHTML = '<p class="text-center text-gray-500 p-8">لا توجد منتجات للمقارنة</p>';
        return;
    }
    
    let html = `
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-start text-sm font-medium text-gray-900 w-48">المواصفات</th>
    `;
    
    // Product headers
    products.forEach(product => {
        html += `
            <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 min-w-64">
                <div class="space-y-2">
                    <img src="${product.image ? 'uploads/products/' + product.image : 'get_placeholder_image.php?text=' + encodeURIComponent(product.name)}" 
                         alt="${product.name}" class="w-20 h-20 object-cover rounded-lg mx-auto">
                    <h4 class="font-semibold text-gray-900">${product.name}</h4>
                    <button onclick="removeFromComparison(${product.id})" 
                            class="text-red-500 hover:text-red-700 text-sm">
                        <i class="fas fa-times me-1"></i>إزالة
                    </button>
                </div>
            </th>
        `;
    });
    
    html += `
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
    `;
    
    // Price row
    html += `
        <tr class="bg-green-50">
            <td class="px-6 py-4 font-medium text-gray-900">السعر</td>
    `;
    products.forEach(product => {
        const price = product.discount_price > 0 ? product.discount_price : product.price;
        const originalPrice = product.discount_price > 0 ? product.price : null;
        html += `
            <td class="px-6 py-4 text-center">
                <div class="space-y-1">
                    <div class="text-lg font-bold text-green-600">ر.ي${parseFloat(price).toLocaleString()}</div>
                    ${originalPrice ? `<div class="text-sm text-gray-500 line-through">ر.ي${parseFloat(originalPrice).toLocaleString()}</div>` : ''}
                </div>
            </td>
        `;
    });
    html += '</tr>';
    
    // Stock row
    html += `
        <tr>
            <td class="px-6 py-4 font-medium text-gray-900">حالة المخزون</td>
    `;
    products.forEach(product => {
        const stockStatus = product.stock_quantity > 0 ? 
            (product.stock_quantity > 10 ? 'متوفر' : `مخزون منخفض (${product.stock_quantity})`) : 
            'غير متوفر';
        const statusClass = product.stock_quantity > 0 ? 
            (product.stock_quantity > 10 ? 'text-green-600' : 'text-yellow-600') : 
            'text-red-600';
        html += `
            <td class="px-6 py-4 text-center">
                <span class="${statusClass} font-medium">${stockStatus}</span>
            </td>
        `;
    });
    html += '</tr>';
    
    // Description row
    html += `
        <tr class="bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">الوصف</td>
    `;
    products.forEach(product => {
        html += `
            <td class="px-6 py-4 text-center text-sm text-gray-600">
                ${product.description ? product.description.substring(0, 100) + '...' : 'لا يوجد وصف متاح'}
            </td>
        `;
    });
    html += '</tr>';
    
    // Actions row
    html += `
        <tr>
            <td class="px-6 py-4 font-medium text-gray-900">الإجراءات</td>
    `;
    products.forEach(product => {
        html += `
            <td class="px-6 py-4 text-center">
                <div class="space-y-2">
                    ${product.stock_quantity > 0 ? 
                        `<button onclick="addToCart(${product.id})" 
                                class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-300 text-sm">
                            <i class="fas fa-cart-plus me-1"></i>أضف إلى السلة
                        </button>` :
                        `<button disabled class="w-full bg-gray-300 text-gray-500 py-2 px-4 rounded-lg text-sm cursor-not-allowed">
                            غير متوفر
                        </button>`
                    }
                    <button onclick="addToWishlist(${product.id})" 
                            class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition duration-300 text-sm">
                        <i class="fas fa-heart me-1"></i>أضف إلى المفضلة
                    </button>
                    <a href="product-details.php?id=${product.id}" 
                       class="block w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 text-sm">
                        <i class="fas fa-eye me-1"></i>عرض التفاصيل
                    </a>
                </div>
            </td>
        `;
    });
    html += '</tr>';
    
    html += `
            </tbody>
        </table>
    `;
    
    tableContainer.innerHTML = html;
}

function removeFromComparison(productId) {
    fetch('ajax/phase3.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove_from_comparison&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadComparisonTable();
            updateComparisonCount();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ', 'error');
    });
}

function clearComparison() {
    if (confirm('هل أنت متأكد أنك تريد مسح جميع المنتجات من المقارنة؟')) {
        fetch('ajax/phase3.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clear_comparison'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ', 'error');
        });
    }
}

function searchProducts() {
    const query = document.getElementById('product-search').value.trim();
    if (query.length < 2) {
        showNotification('يرجى إدخال حرفين على الأقل للبحث', 'warning');
        return;
    }
    
    fetch(`products.php?search=${encodeURIComponent(query)}&ajax=1`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderSearchResults(data.products);
            } else {
                showNotification('لم يتم العثور على منتجات', 'info');
            }
        })
        .catch(error => {
            showNotification('فشل البحث', 'error');
        });
}

function renderSearchResults(products) {
    const resultsContainer = document.getElementById('search-results');
    
    if (products.length === 0) {
        resultsContainer.innerHTML = '<p class="text-center text-gray-500 col-span-full">لم يتم العثور على منتجات</p>';
        resultsContainer.classList.remove('hidden');
        return;
    }
    
    let html = '';
    products.forEach(product => {
        html += `
            <div class="bg-gray-50 rounded-lg p-4">
                <img src="${product.image ? 'uploads/products/' + product.image : 'get_placeholder_image.php?text=' + encodeURIComponent(product.name)}" 
                     alt="${product.name}" class="w-full h-32 object-cover rounded-lg mb-3">
                <h4 class="font-semibold text-gray-900 mb-2">${product.name}</h4>
                <p class="text-green-600 font-bold mb-3">ر.ي${parseFloat(product.price).toLocaleString()}</p>
                <button onclick="addToComparison(${product.id})" 
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 text-sm">
                    <i class="fas fa-plus me-1"></i>أضف للمقارنة
                </button>
            </div>
        `;
    });
    
    resultsContainer.innerHTML = html;
    resultsContainer.classList.remove('hidden');
}

function addToComparison(productId) {
    fetch('ajax/phase3.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_comparison&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'warning');
        if (data.success) {
            loadComparisonTable();
            updateComparisonCount();
            document.getElementById('search-results').classList.add('hidden');
            document.getElementById('product-search').value = '';
        }
    })
    .catch(error => {
        showNotification('حدث خطأ', 'error');
    });
}

function addToCart(productId) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تمت إضافة المنتج إلى السلة!', 'success');
            updateCartCount();
        } else {
            showNotification(data.message || 'فشل إضافة المنتج إلى السلة', 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ', 'error');
    });
}

function addToWishlist(productId) {
    fetch('ajax/phase3.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add_to_wishlist&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            updateWishlistCount();
        }
    })
    .catch(error => {
        showNotification('An error occurred', 'error');
    });
}

function updateComparisonCount() {
    fetch('ajax/phase3.php?action=get_comparison_list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('comparison-count').textContent = data.count;
            }
        });
}

function updateCartCount() {
    fetch('ajax/cart.php?action=count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const countElement = document.getElementById('cart-count');
                if (countElement) {
                    countElement.textContent = data.count;
                }
            }
        });
}

function updateWishlistCount() {
    fetch('ajax/phase3.php?action=get_wishlist_count')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const countElement = document.getElementById('wishlist-count');
                if (countElement) {
                    countElement.textContent = data.count;
                }
            }
        });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' :
                type === 'error' ? 'fa-exclamation-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' :
                'fa-info-circle'
            } me-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>

<?php include 'includes/footer.php'; ?> 