<?php
session_start();

// Simple user management
$users = [
    'admin@demo.com' => ['password' => 'admin123', 'name' => 'Admin User', 'is_admin' => true],
    'user@demo.com' => ['password' => 'user123', 'name' => 'Demo User', 'is_admin' => false]
];

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (isset($users[$email]) && $users[$email]['password'] === $password) {
        $_SESSION['user_id'] = $email;
        $_SESSION['user_name'] = $users[$email]['name'];
        $_SESSION['is_admin'] = $users[$email]['is_admin'];
        $login_success = "Login successful!";
    } else {
        $login_error = "Invalid email or password!";
    }
}

// Handle register
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (isset($users[$email])) {
        $register_error = "Email already exists!";
    } else {
        $users[$email] = ['password' => $password, 'name' => $name, 'is_admin' => false];
        $_SESSION['user_id'] = $email;
        $_SESSION['user_name'] = $name;
        $_SESSION['is_admin'] = false;
        $register_success = "Registration successful!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: simple_demo.php');
    exit;
}

// Simple product data without database
$products = [
    [
        'id' => 1,
        'name' => 'iPhone 15 Pro',
        'price' => 999.99,
        'category' => 'Electronics',
        'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400',
        'description' => 'Latest iPhone with advanced features',
        'stock' => 50
    ],
    [
        'id' => 2,
        'name' => 'MacBook Air M2',
        'price' => 1299.99,
        'category' => 'Electronics',
        'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400',
        'description' => 'Powerful laptop for professionals',
        'stock' => 30
    ],
    [
        'id' => 3,
        'name' => 'Samsung Galaxy S24',
        'price' => 899.99,
        'category' => 'Electronics',
        'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400',
        'description' => 'Android flagship smartphone',
        'stock' => 40
    ],
    [
        'id' => 4,
        'name' =>         'DJI Mini 3 Pro Drone',
        'price' => 759.99,
        'category' => 'Electronics',
        'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=400',
        'description' => 'Professional drone for photography',
        'stock' => 25
    ],
    [
        'id' => 5,
        'name' => 'Sony WH-1000XM5',
        'price' => 349.99,
        'category' => 'Electronics',
        'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400',
        'description' => 'Premium noise-canceling headphones',
        'stock' => 60
    ],
    [
        'id' => 6,
        'name' => 'Modern Sofa Set',
        'price' => 899.99,
        'category' => 'Furniture',
        'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400',
        'description' => 'Comfortable 3-seater sofa',
        'stock' => 15
    ],
    [
        'id' => 7,
        'name' => 'Dining Table',
        'price' => 599.99,
        'category' => 'Furniture',
        'image' => 'https://images.unsplash.com/photo-1615066390971-03e4e1c36ddf?w=400',
        'description' => 'Elegant 6-seater dining table',
        'stock' => 20
    ],
    [
        'id' => 8,
        'name' => 'Office Chair',
        'price' => 299.99,
        'category' => 'Furniture',
        'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400',
        'description' => 'Ergonomic office chair',
        'stock' => 35
    ],
    [
        'id' => 9,
        'name' => 'Nike Air Max',
        'price' => 129.99,
        'category' => 'Fashion',
        'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
        'description' => 'Comfortable running shoes',
        'stock' => 100
    ],
    [
        'id' => 10,
        'name' => 'Leather Jacket',
        'price' => 199.99,
        'category' => 'Fashion',
        'image' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400',
        'description' => 'Classic leather jacket',
        'stock' => 45
    ]
];

// Handle search and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$filtered_products = $products;
if ($search || $category_filter) {
    $filtered_products = array_filter($products, function($product) use ($search, $category_filter) {
        $matches_search = !$search || stripos($product['name'], $search) !== false || stripos($product['description'], $search) !== false;
        $matches_category = !$category_filter || $product['category'] === $category_filter;
        return $matches_search && $matches_category;
    });
}

// Get unique categories
$categories = array_unique(array_column($products, 'category'));

// Handle cart actions
if (isset($_POST['action'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    switch ($_POST['action']) {
        case 'add':
            $product_id = $_POST['product_id'];
            $product = array_filter($products, fn($p) => $p['id'] == $product_id);
            if (!empty($product)) {
                $product = array_values($product)[0];
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity']++;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'image' => $product['image'],
                        'quantity' => 1
                    ];
                }
            }
            break;
        case 'remove':
            $product_id = $_POST['product_id'];
            unset($_SESSION['cart'][$product_id]);
            break;
        case 'checkout':
            if (!isset($_SESSION['user_id'])) {
                $checkout_error = "Please login to checkout!";
            } else {
                $_SESSION['cart'] = [];
                $success_message = "Thank you for your order! Your items will be shipped soon.";
            }
            break;
    }
}

$cart_total = isset($_SESSION['cart']) ? array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart'])) : 0;
$cart_count = isset($_SESSION['cart']) ? array_sum(array_map(fn($item) => $item['quantity'], $_SESSION['cart'])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Complete E-Commerce Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .search-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 3rem;
        }
        .features-section {
            background: #f8f9fa;
            padding: 3rem 0;
        }
        .feature-card {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="simple_demo.php">
                <i class="fas fa-store me-2"></i>Complete E-Commerce Demo
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="simple_demo.php">Store</a>
                <a class="nav-link" href="#about">About</a>
                <a class="nav-link" href="#contact">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a class="nav-link" href="simple_admin.php">Admin Panel</a>
                    <?php endif; ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#profile">My Profile</a></li>
                            <li><a class="dropdown-item" href="#orders">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?logout=1">Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
                <?php endif; ?>
                <a class="nav-link position-relative" href="#" data-bs-toggle="modal" data-bs-target="#cartModal">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?= $cart_count ?></span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">🚀 Complete E-Commerce Demo</h1>
            <p class="lead mb-4">Full-featured e-commerce website with login, register, admin panel, and more!</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h5>Shop Products</h5>
                                <small>Browse our catalog</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <h5>User Accounts</h5>
                                <small>Login & Register</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-center">
                                <i class="fas fa-cog fa-2x mb-2"></i>
                                <h5>Admin Panel</h5>
                                <small>Manage everything</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category ?>" <?= $category_filter === $category ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4">
                        <?php if ($search || $category_filter): ?>
                            Search Results
                            <?php if ($search): ?>
                                for "<?= htmlspecialchars($search) ?>"
                            <?php endif; ?>
                            <?php if ($category_filter): ?>
                                in <?= htmlspecialchars($category_filter) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            Featured Products
                        <?php endif; ?>
                    </h2>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($filtered_products as $product): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card product-card h-100">
                            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted"><?= htmlspecialchars($product['category']) ?></p>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0">$<?= number_format($product['price'], 2) ?></span>
                                        <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                            <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                                        </span>
                                    </div>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-primary w-100" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                            <i class="fas fa-cart-plus me-2"></i>
                                            <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($filtered_products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="simple_demo.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="about">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our Store?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-shipping-fast feature-icon"></i>
                        <h5>Free Shipping</h5>
                        <p class="text-muted">Free shipping on orders over $100</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h5>Secure Payment</h5>
                        <p class="text-muted">100% secure payment processing</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <i class="fas fa-undo feature-icon"></i>
                        <h5>Easy Returns</h5>
                        <p class="text-muted">30-day return policy</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Our Store</h5>
                    <p>Your trusted destination for quality products. We provide excellent customer service and competitive prices.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="simple_demo.php" class="text-light">Home</a></li>
                        <li><a href="#about" class="text-light">About</a></li>
                        <li><a href="#contact" class="text-light">Contact</a></li>
                        <li><a href="simple_admin.php" class="text-light">Admin Panel</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-envelope me-2"></i>info@demo-store.com</p>
                    <p><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i>123 Demo Street, Demo City</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2024 Complete E-Commerce Demo. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <?php if (isset($login_error)): ?>
                            <div class="alert alert-danger"><?= $login_error ?></div>
                        <?php endif; ?>
                        <?php if (isset($login_success)): ?>
                            <div class="alert alert-success"><?= $login_success ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <?php if (isset($register_error)): ?>
                            <div class="alert alert-danger"><?= $register_error ?></div>
                        <?php endif; ?>
                        <?php if (isset($register_success)): ?>
                            <div class="alert alert-success"><?= $register_success ?></div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="register" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($_SESSION['cart'])): ?>
                        <p class="text-center text-muted">Your cart is empty</p>
                    <?php else: ?>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover;" class="me-3">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                        <small class="text-muted">$<?= number_format($item['price'], 2) ?> x <?= $item['quantity'] ?></small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="me-3">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Total: $<?= number_format($cart_total, 2) ?></h5>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="checkout">
                                <button type="submit" class="btn btn-success btn-lg">Checkout</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($checkout_error)): ?>
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <?= $checkout_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 