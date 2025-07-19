<?php
session_start();

// Simple admin credentials
$admin_username = "admin";
$admin_password = "admin123";

// Handle admin login
if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Invalid credentials!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: simple_admin.php');
    exit;
}

// Sample data for admin panel
$products = [
    [
        'id' => 1,
        'name' => 'iPhone 15 Pro',
        'price' => 999.99,
        'category' => 'Electronics',
        'stock' => 50,
        'status' => 'Active'
    ],
    [
        'id' => 2,
        'name' => 'MacBook Air M2',
        'price' => 1299.99,
        'category' => 'Electronics',
        'stock' => 30,
        'status' => 'Active'
    ],
    [
        'id' => 3,
        'name' => 'Samsung Galaxy S24',
        'price' => 899.99,
        'category' => 'Electronics',
        'stock' => 40,
        'status' => 'Active'
    ],
    [
        'id' => 4,
        'name' => 'Modern Sofa Set',
        'price' => 899.99,
        'category' => 'Furniture',
        'stock' => 15,
        'status' => 'Active'
    ],
    [
        'id' => 5,
        'name' => 'Nike Air Max',
        'price' => 129.99,
        'category' => 'Fashion',
        'stock' => 100,
        'status' => 'Active'
    ]
];

$orders = [
    [
        'id' => 'ORD001',
        'customer' => 'John Doe',
        'email' => 'john@example.com',
        'total' => 1599.98,
        'status' => 'Pending',
        'date' => '2024-07-19'
    ],
    [
        'id' => 'ORD002',
        'customer' => 'Jane Smith',
        'email' => 'jane@example.com',
        'total' => 899.99,
        'status' => 'Shipped',
        'date' => '2024-07-18'
    ],
    [
        'id' => 'ORD003',
        'customer' => 'Mike Johnson',
        'email' => 'mike@example.com',
        'total' => 599.99,
        'status' => 'Delivered',
        'date' => '2024-07-17'
    ]
];

// Calculate dashboard stats
$total_products = count($products);
$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, 'total'));
$pending_orders = count(array_filter($orders, fn($order) => $order['status'] === 'Pending'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Admin Panel - E-Commerce Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background: #343a40; }
        .sidebar .nav-link { color: #fff; }
        .sidebar .nav-link:hover { background: #495057; }
        .sidebar .nav-link.active { background: #007bff; }
        .card-stats { border-left: 4px solid #007bff; }
        .card-stats.success { border-left-color: #28a745; }
        .card-stats.warning { border-left-color: #ffc107; }
        .card-stats.danger { border-left-color: #dc3545; }
        .table-responsive { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <?php if (!isset($_SESSION['admin_logged_in'])): ?>
    <!-- Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Simple Admin Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($login_error)): ?>
                            <div class="alert alert-danger"><?= $login_error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">Demo: admin / admin123</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Admin Dashboard -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Simple Admin Panel</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#products" data-bs-toggle="tab">
                                <i class="fas fa-box me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#orders" data-bs-toggle="tab">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="?logout=1">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane fade show active" id="dashboard">
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <div class="card card-stats">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title text-muted">Total Products</h6>
                                                <h3 class="mb-0"><?= $total_products ?></h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-box fa-2x text-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-stats success">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title text-muted">Total Orders</h6>
                                                <h3 class="mb-0"><?= $total_orders ?></h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-shopping-cart fa-2x text-success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-stats warning">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title text-muted">Total Revenue</h6>
                                                <h3 class="mb-0">$<?= number_format($total_revenue, 2) ?></h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-4">
                                <div class="card card-stats danger">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title text-muted">Pending Orders</h6>
                                                <h3 class="mb-0"><?= $pending_orders ?></h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-clock fa-2x text-danger"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="row">
                            <div class="col-12">
                                <h5>Recent Orders</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                            <tr>
                                                <td><?= $order['id'] ?></td>
                                                <td><?= $order['customer'] ?></td>
                                                <td>$<?= number_format($order['total'], 2) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $order['status'] === 'Delivered' ? 'success' : ($order['status'] === 'Shipped' ? 'info' : 'warning') ?>">
                                                        <?= $order['status'] ?>
                                                    </span>
                                                </td>
                                                <td><?= $order['date'] ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Tab -->
                    <div class="tab-pane fade" id="products">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Product Management</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= $product['id'] ?></td>
                                        <td><?= $product['name'] ?></td>
                                        <td><?= $product['category'] ?></td>
                                        <td>$<?= number_format($product['price'], 2) ?></td>
                                        <td><?= $product['stock'] ?></td>
                                        <td>
                                            <span class="badge bg-success"><?= $product['status'] ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">Edit</button>
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <h5>Order Management</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= $order['customer'] ?></td>
                                        <td><?= $order['email'] ?></td>
                                        <td>$<?= number_format($order['total'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $order['status'] === 'Delivered' ? 'success' : ($order['status'] === 'Shipped' ? 'info' : 'warning') ?>">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= $order['date'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">View</button>
                                            <button class="btn btn-sm btn-outline-success">Update Status</button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Tab functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html> 