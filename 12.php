<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login_register/index.php");
    exit();
}
$userEmail = $_SESSION['email'] ?? '';
$userName = $_SESSION['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>АЯЛАГЧ 4X4</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        :root {
            --primary: #000000;
            --secondary: #f5f5f5;
            --accent: #c8a96e;
            --text-dark: #333;
            --text-light: #666;
            --border: #ddd;
            --white: #ffffff;
            --danger: #d32f2f;
            --success: #2e7d32;
            --warning: #e65100;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-bar {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-bar h1 {
            color: var(--primary);
            font-size: 1.8rem;
        }

        .header-bar h1 span {
            color: var(--primary);
        }

        .menu {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .menu-item {
            padding: 10px 22px;
            background-color: var(--white);
            border: 1px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            user-select: none;
        }

        .menu-item:hover { background-color: var(--secondary); }

        .menu-item.active {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .main-content {
            background-color: var(--white);
            border: 1px solid var(--border);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
        }

        .hidden { display: none; }

        h2 { font-family: Arial, sans-serif; font-size: 1.8rem; letter-spacing: 2px; margin-bottom: 20px; color: var(--primary); }
        h3 { font-size: 1rem; font-weight: 600; margin-bottom: 15px; color: var(--text-dark); }

        
        .form-group { margin: 14px 0; }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
        }

        input, select, textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            background-color: var(--white);
            color: var(--text-dark);
            border-radius: 6px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            transition: border-color 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #999;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: var(--white);
            font-family: Arial, sans-serif;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        button:hover { background-color: #333; }

        .sell-button { background-color: var(--success); }
        .sell-button:hover { background-color: #1b5e20; }

        .danger-button { background-color: var(--danger); }
        .danger-button:hover { background-color: #b71c1c; }

        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .quick-actions button {
            width: auto;
            padding: 10px 20px;
        }

        
        .product-card {
            background-color: var(--white);
            border: 1px solid var(--border);
            padding: 24px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }

        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: var(--card-shadow);
        }

        .stat-card h3 { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-light); margin-bottom: 8px; }

        .stat-number {
            font-family: Arial, sans-serif;
            font-size: 2.5rem;
            letter-spacing: 2px;
            color: var(--primary);
        }

        .stat-number.danger { color: var(--danger); }

        
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 13px 15px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        th { background-color: var(--secondary); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }

        .product-img-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid var(--border);
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-ok { background: #e8f5e9; color: var(--success); }
        .badge-low { background: #fff3e0; color: var(--warning); }
        .badge-out { background: #ffebee; color: var(--danger); }

        .image-preview-container {
            margin: 8px 0;
            width: 90px;
            height: 90px;
            border: 2px dashed var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            font-size: 0.75rem;
            color: #ccc;
        }

        .image-preview-container img { width: 100%; height: 100%; object-fit: cover; }

        .chart-container {
            background: var(--white);
            border: 1px solid var(--border);
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            position: relative;
        }

        .chart-container canvas { max-height: 320px; }

        .date-filter {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 15px 0 20px;
            flex-wrap: wrap;
        }

        .date-filter label { margin: 0; font-size: 0.85rem; }
        .date-filter input, .date-filter select { width: auto; margin: 0; }

        .message {
            padding: 12px 16px;
            margin: 12px 0;
            border-radius: 6px;
            font-size: 0.9rem;
            display: none;
        }

        .message.success { background-color: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }
        .message.error { background-color: #ffebee; color: #b71c1c; border: 1px solid #ef9a9a; }

        .product-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }

        .detail-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s;
        }

        .detail-card:hover { transform: translateY(-3px); }
        .detail-card img { width: 100%; height: 180px; object-fit: cover; }
        .detail-info { padding: 15px; }
        .detail-info h4 { margin-bottom: 6px; }
        .detail-price { font-family: Arial, sans-serif; font-size: 1.4rem; letter-spacing: 1px; color: var(--primary); }

        .recent-sale-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }

        .recent-sale-item:last-child { border-bottom: none; }

        @media (max-width: 768px) {
            .header-bar { flex-direction: column; gap: 12px; text-align: center; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .quick-actions { flex-direction: column; }
            .quick-actions button { width: 100%; }
            .date-filter { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header-bar">
        <h1>АЯЛАГЧ <span>4X4</span></h1>
        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <span style="font-size:0.8rem; color:var(--text-light);" id="lastSaved">Connected to Database</span>
            <?php if ($userName): ?>
                <span style="font-size:0.9rem; font-weight:500;">Welcome, <?php echo htmlspecialchars($userName); ?></span>
            <?php endif; ?>
            <a href="logout.php" style="background:var(--danger); color:white; padding:8px 18px; border-radius:6px; text-decoration:none; font-size:0.85rem; font-weight:700; transition:0.2s;"
               onmouseover="this.style.background='#b71c1c'" onmouseout="this.style.background='var(--danger)'">Logout</a>
        </div>
    </div>

    <div class="menu">
        <div class="menu-item active" onclick="showSection('dashboard', this)">Dashboard</div>
        <div class="menu-item" onclick="showSection('inventory', this)">Inventory</div>
        <div class="menu-item" onclick="showSection('sales', this)">Sales</div>
        <div class="menu-item" onclick="showSection('analytics', this)">Analytics</div>
        <div class="menu-item" onclick="showSection('products', this)">Product Details</div>
        <div class="menu-item" onclick="showSection('backup', this)">Data Management</div>
    </div>

    <div class="main-content">

        <div id="dashboard" class="section">
            <h2>Dashboard Overview</h2>
            <div class="stats-grid">
                <div class="stat-card"><h3>Total Products</h3><div class="stat-number" id="totalProducts">0</div></div>
                <div class="stat-card"><h3>Total Sales Today</h3><div class="stat-number" id="todaySales">₮0</div></div>
                <div class="stat-card"><h3>Total Revenue</h3><div class="stat-number" id="totalRevenue">₮0</div></div>
                <div class="stat-card"><h3>Low Stock Items</h3><div class="stat-number danger" id="lowStockCount">0</div></div>
            </div>

            <div class="product-card">
                <h3>Recent Sales Activity</h3>
                <div id="recentSales"><p style="color:var(--text-light)">No recent activity.</p></div>
            </div>

            <div class="product-card">
                <h3>Stock Alerts</h3>
                <div id="stockAlerts"><p style="color:var(--success)">All stock levels normal.</p></div>
            </div>
        </div>

        <div id="inventory" class="section hidden">
            <h2>Inventory Management</h2>

            <div class="product-card">
                <h3>Add New Product</h3>
                <div id="inventoryMessage" class="message"></div>
                <div class="form-group">
                    <label for="productName">Product Name</label>
                    <input type="text" id="productName" placeholder="e.g. Roof Rack 80\"">
                </div>
                <div class="form-group">
                    <label for="productPrice">Price (₮)</label>
                    <input type="number" id="productPrice" step="0.01" placeholder="Enter price">
                </div>
                <div class="form-group">
                    <label for="productStock">Stock Quantity</label>
                    <input type="number" id="productStock" placeholder="Enter initial stock">
                </div>
                <div class="form-group">
                    <label for="productCategory">Category</label>
                    <select id="productCategory">
                        <option value="Tires">Tires</option>
                        <option value="Lights">Lights</option>
                        <option value="Winches">Winches</option>
                        <option value="Bumpers">Bumpers</option>
                        <option value="Suspension">Suspension</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Photo</label>
                    <input type="file" id="productImage" accept="image/*" onchange="previewImage(this)">
                    <div class="image-preview-container" id="imgPreview"><span>Preview</span></div>
                </div>
                <button class="sell-button" onclick="addProduct()" style="margin-top:10px;">Add to Inventory</button>
            </div>

            <h3>Current Inventory</h3>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody"></tbody>
                </table>
            </div>
        </div>

        <div id="sales" class="section hidden">
            <h2>Sales Management</h2>

            <div class="product-card">
                <h3>Process New Sale</h3>
                <div id="saleMessage" class="message"></div>

                <div class="form-group">
                    <label for="saleProduct">Select Product</label>
                    <select id="saleProduct"><option value="">Select a product to sell</option></select>
                </div>
                <div class="form-group">
                    <label for="saleQuantity">Quantity</label>
                    <input type="number" id="saleQuantity" min="1" placeholder="Enter quantity to sell">
                </div>
                <div class="form-group">
                    <label for="customerName">Customer Name (Optional)</label>
                    <input type="text" id="customerName" placeholder="Enter customer name">
                </div>
                <div class="form-group">
                    <label for="saleNotes">Sale Notes (Optional)</label>
                    <input type="text" id="saleNotes" placeholder="Any additional notes">
                </div>
                <div class="quick-actions">
                    <button class="sell-button" onclick="processSale()">Complete Sale</button>
                    <button onclick="clearSaleForm()" style="background:#666; width:auto; padding:10px 20px;">Clear Form</button>
                </div>
            </div>

            <div class="product-card">
                <h3>Recent Sales History</h3>
                <div class="date-filter">
                    <label>Filter by:</label>
                    <select id="salesFilter" onchange="filterSales()">
                        <option value="all">All Sales</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                    <input type="date" id="customDate" onchange="filterSales()">
                </div>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Customer</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="analytics" class="section hidden">
            <h2>Analytics & Statistics</h2>

            <div class="stats-grid">
                <div class="stat-card"><h3>Total Sales Count</h3><div class="stat-number" id="totalSalesCount">0</div></div>
                <div class="stat-card"><h3>Average Sale Value</h3><div class="stat-number" id="avgSaleValue">₮0</div></div>
                <div class="stat-card"><h3>Best Selling Product</h3><div class="stat-number" id="bestProduct" style="font-size:1.1rem; padding-top:6px;">None</div></div>
                <div class="stat-card"><h3>Top Category</h3><div class="stat-number" id="topCategory" style="font-size:1.1rem; padding-top:6px;">None</div></div>
            </div>

            <div class="chart-container">
                <h3>Sales Over Time</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Inventory by Category</h3>
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Monthly Revenue</h3>
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <div id="products" class="section hidden">
            <h2>Product Details</h2>
            <div id="productsList" class="product-detail-grid"></div>
        </div>

        <div id="backup" class="section hidden">
            <h2>Data Management</h2>

            <div class="product-card">
                <h3>Export Data</h3>
                <p style="font-size:0.9rem; color:var(--text-light); margin-bottom:15px;">Download your inventory and sales data as a CSV file.</p>
                <div class="quick-actions">
                    <button onclick="exportData()" style="width:auto; padding:10px 22px;">Export All Data (CSV)</button>
                </div>
            </div>

            <div class="product-card">
                <h3>Clear All Data</h3>
                <p style="color:var(--danger); font-size:0.9rem; margin-bottom:15px;">⚠️ This action is permanent. Use phpMyAdmin to reset database tables.</p>
                <button onclick="clearAllData()" class="danger-button">Reset System</button>
                <div id="clearMessage" class="message"></div>
            </div>
        </div>

    </div><!-- end main-content -->
</div>

<script>

    let products = [];
    let sales    = [];
    let currentBase64 = "";
    let salesChart = null, categoryChart = null, monthlyChart = null;

    const NO_IMG = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiNhYWEiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';

    
    function loadFromDatabase() {
        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'fetchData' })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") {
                products = (result.products || []).map(p => ({
                    ...p,
                    id:    parseInt(p.id),
                    price: parseFloat(p.price),
                    stock: parseInt(p.stock)
                }));

                sales = (result.sales || []).map(s => ({
                    id:          s.id,
                    timestamp:   s.sale_date,
                    date:        new Date(s.sale_date).toLocaleDateString(),
                    time:        new Date(s.sale_date).toLocaleTimeString(),
                    productName: s.product_name,
                    qty:         parseInt(s.quantity),
                    unitPrice:   parseFloat(s.unit_price || 0),
                    total:       parseFloat(s.total_price),
                    customer:    s.customer_name || "Guest",
                    notes:       s.notes || ""
                }));

                if (products.length === 0) {
                    insertSampleProduct();
                    return;
                }

                refreshUI();
                document.getElementById('lastSaved').textContent = "Last updated: " + new Date().toLocaleTimeString();
            } else {
                console.error("DB error:", result.message);
            }
        })
        .catch(err => console.error("Fetch error:", err));
    }

    function insertSampleProduct() {
        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action:   'addProduct',
                name:     'LED Light Bar 42"',
                price:    299990,
                stock:    15,
                category: 'Lights',
                image:    ''
            })
        })
        .then(r => r.json())
        .then(() => loadFromDatabase())
        .catch(err => console.error("Sample insert error:", err));
    }

    function refreshUI() {
        updateInventoryTable();
        updateDashboard();
        populateProductSelect();
        filterSales();
    }

    function showSection(id, el) {
        document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
        document.getElementById(id).classList.remove('hidden');
        document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
        el.classList.add('active');

        if (id === 'inventory')  updateInventoryTable();
        if (id === 'sales')      { populateProductSelect(); filterSales(); }
        if (id === 'dashboard')  updateDashboard();
        if (id === 'products')   displayProductDetails();
        if (id === 'analytics')  { updateAnalytics(); setTimeout(createCharts, 100); }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                currentBase64 = e.target.result;
                document.getElementById('imgPreview').innerHTML = `<img src="${currentBase64}">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addProduct() {
        const name  = document.getElementById('productName').value.trim();
        const price = parseFloat(document.getElementById('productPrice').value);
        const stock = parseInt(document.getElementById('productStock').value) || 0;
        const cat   = document.getElementById('productCategory').value;

        if (!name || isNaN(price) || price <= 0) {
            showMessage('inventoryMessage', 'Product name and a valid price are required.', 'error');
            return;
        }

        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'addProduct', name, price, stock, category: cat, image: currentBase64 || '' })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") {
                showMessage('inventoryMessage', 'Product saved to database!', 'success');
                document.getElementById('productName').value    = '';
                document.getElementById('productPrice').value   = '';
                document.getElementById('productStock').value   = '';
                document.getElementById('productCategory').value = 'Tires';
                document.getElementById('productImage').value   = '';
                document.getElementById('imgPreview').innerHTML = '<span>Preview</span>';
                currentBase64 = '';
                loadFromDatabase();
            } else {
                showMessage('inventoryMessage', 'Error: ' + (result.message || 'Failed to save.'), 'error');
            }
        })
        .catch(err => showMessage('inventoryMessage', 'Network error: ' + err, 'error'));
    }

    function updateInventoryTable() {
        const tbody = document.getElementById('inventoryTableBody');
        if (!products.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:24px;color:#aaa;">No products yet. Add your first product above.</td></tr>';
            return;
        }
        tbody.innerHTML = products.map(p => {
            const img  = p.image || NO_IMG;
            const cls  = p.stock === 0 ? 'badge-out' : (p.stock <= 5 ? 'badge-low' : 'badge-ok');
            const lbl  = p.stock === 0 ? 'Out of Stock' : (p.stock <= 5 ? 'Low Stock' : 'In Stock');
            return `<tr>
                <td><img src="${img}" class="product-img-thumb" onerror="this.src='${NO_IMG}'"></td>
                <td>${p.id}</td>
                <td><strong>${p.name}</strong></td>
                <td>₮${p.price.toLocaleString()}</td>
                <td>${p.stock}</td>
                <td>${p.category}</td>
                <td><span class="badge ${cls}">${lbl}</span></td>
                <td>
                    <div class="quick-actions" style="gap:6px;">
                        <button onclick="addStock(${p.id})" style="background:#555;padding:6px 12px;font-size:0.8rem;">+ Stock</button>
                        <button onclick="deleteProduct(${p.id})" class="danger-button" style="padding:6px 12px;font-size:0.8rem;">Remove</button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function addStock(id) {
        const qty = parseInt(prompt('Enter quantity to add:'));
        if (isNaN(qty) || qty <= 0) return;
        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'addStock', productId: id, quantity: qty })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") loadFromDatabase();
            else alert('Error: ' + (result.message || 'Could not add stock.'));
        });
    }

    function deleteProduct(id) {
        if (!confirm('Remove this product from inventory?')) return;
        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'deleteProduct', productId: id })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") loadFromDatabase();
            else alert('Error: ' + (result.message || 'Delete failed.'));
        });
    }

    function populateProductSelect() {
        const sel = document.getElementById('saleProduct');
        sel.innerHTML = '<option value="">Select a product to sell</option>' +
            products.filter(p => p.stock > 0).map(p =>
                `<option value="${p.id}">${p.name} — ₮${p.price.toLocaleString()} (Stock: ${p.stock})</option>`
            ).join('');
    }

    function processSale() {
        const pidVal   = document.getElementById('saleProduct').value;
        const qty      = parseInt(document.getElementById('saleQuantity').value);
        const customer = document.getElementById('customerName').value.trim() || 'Walk-in Customer';
        const notes    = document.getElementById('saleNotes').value.trim();

        if (!pidVal) { showMessage('saleMessage', 'Please select a product.', 'error'); return; }
        if (!qty || qty <= 0) { showMessage('saleMessage', 'Please enter a valid quantity.', 'error'); return; }

        const product = products.find(p => p.id === parseInt(pidVal));
        if (!product) { showMessage('saleMessage', 'Product not found.', 'error'); return; }
        if (qty > product.stock) { showMessage('saleMessage', `Insufficient stock. Available: ${product.stock}`, 'error'); return; }

        const total = qty * product.price;

        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'processSale',
                productId:   product.id,
                productName: product.name,
                qty,
                unitPrice:   product.price,
                total,
                customer,
                notes
            })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") {
                showMessage('saleMessage', 'Sale completed and saved!', 'success');
                clearSaleForm();
                loadFromDatabase();
            } else {
                showMessage('saleMessage', 'Error: ' + (result.message || 'Failed.'), 'error');
            }
        })
        .catch(err => showMessage('saleMessage', 'Network error: ' + err, 'error'));
    }

    function deleteSale(saleId, productName, quantity) {
        if (!confirm(`Delete this sale?\n\nProduct: ${productName}\nQty: ${quantity}\n\nStock will be restored.`)) return;
        fetch('save_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'deleteSale', saleId, productName, quantity })
        })
        .then(r => r.json())
        .then(result => {
            if (result.status === "success") {
                showMessage('saleMessage', 'Sale deleted and stock restored.', 'success');
                loadFromDatabase();
            } else {
                showMessage('saleMessage', 'Error: ' + (result.message || 'Failed.'), 'error');
            }
        });
    }

    function filterSales() {
        const filter = document.getElementById('salesFilter').value;
        const customDate = document.getElementById('customDate').value;
        const tbody = document.getElementById('salesTableBody');
        const now = new Date();

        let filtered = [...sales];

        if (filter === 'today') {
            filtered = sales.filter(s => new Date(s.timestamp).toDateString() === now.toDateString());
        } else if (filter === 'week') {
            const weekAgo = new Date(now); weekAgo.setDate(weekAgo.getDate() - 7);
            filtered = sales.filter(s => new Date(s.timestamp) >= weekAgo);
        } else if (filter === 'month') {
            filtered = sales.filter(s => {
                const d = new Date(s.timestamp);
                return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
            });
        }

        if (customDate) {
            filtered = sales.filter(s => new Date(s.timestamp).toDateString() === new Date(customDate).toDateString());
        }

        filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

        if (!filtered.length) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#aaa;">No sales found for this filter.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(s => `
            <tr>
                <td>${s.date}</td>
                <td>${s.time}</td>
                <td>${s.productName}</td>
                <td>${s.qty}</td>
                <td>₮${parseFloat(s.unitPrice || 0).toLocaleString()}</td>
                <td>₮${parseFloat(s.total).toLocaleString()}</td>
                <td>${s.customer}</td>
                <td>
                    <button onclick="deleteSale(${s.id}, '${s.productName.replace(/'/g,"\\'")}', ${s.qty})"
                            class="danger-button"
                            style="padding:5px 10px; font-size:0.8rem;">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    function clearSaleForm() {
        ['saleProduct','saleQuantity','customerName','saleNotes'].forEach(id => document.getElementById(id).value = '');
    }

    function updateDashboard() {
        document.getElementById('totalProducts').textContent = products.length;

        const totalRev = sales.reduce((a, b) => a + parseFloat(b.total), 0);
        document.getElementById('totalRevenue').textContent = `₮${totalRev.toLocaleString()}`;

        const today      = new Date().toLocaleDateString();
        const todayTotal = sales.filter(s => s.date === today).reduce((a, b) => a + parseFloat(b.total), 0);
        document.getElementById('todaySales').textContent = `₮${todayTotal.toLocaleString()}`;

        const low = products.filter(p => p.stock <= 5);
        document.getElementById('lowStockCount').textContent = low.length;

        const recentEl = document.getElementById('recentSales');
        const recent   = [...sales].sort((a,b) => new Date(b.timestamp) - new Date(a.timestamp)).slice(0, 5);
        recentEl.innerHTML = recent.length
            ? recent.map(s => `
                <div class="recent-sale-item">
                    <div><strong>${s.productName}</strong><br><small style="color:var(--text-light)">${s.date} · ${s.customer}</small></div>
                    <div style="font-weight:700;">₮${parseFloat(s.total).toLocaleString()}</div>
                </div>`).join('')
            : '<p style="color:var(--text-light)">No recent activity.</p>';

        const alertEl = document.getElementById('stockAlerts');
        alertEl.innerHTML = low.length
            ? low.map(p => `
                <div style="padding:10px 14px; border-left:4px solid var(--danger); margin:6px 0; background:#fff5f5; border-radius:4px; font-size:0.9rem;">
                    <strong>${p.name}</strong> is running low — only <strong>${p.stock}</strong> left
                </div>`).join('')
            : '<div style="color:var(--success); padding:10px;">All stock levels normal ✓</div>';
    }

    function displayProductDetails() {
        const container = document.getElementById('productsList');
        if (!products.length) {
            container.innerHTML = '<p style="color:var(--text-light);text-align:center;padding:30px;">No products to display.</p>';
            return;
        }
        container.innerHTML = products.map(p => {
            const img = p.image || NO_IMG;
            return `
                <div class="detail-card">
                    <img src="${img}" onerror="this.src='${NO_IMG}'">
                    <div class="detail-info">
                        <h4>${p.name}</h4>
                        <div class="detail-price">₮${p.price.toLocaleString()}</div>
                        <p style="font-size:0.8rem;color:var(--text-light);margin-top:6px;">
                            ${p.category} &nbsp;·&nbsp; ${p.stock} in stock
                        </p>
                    </div>
                </div>`;
        }).join('');
    }

    function updateAnalytics() {
        const totalRev = sales.reduce((a, b) => a + parseFloat(b.total), 0);
        document.getElementById('totalSalesCount').textContent = sales.length;
        document.getElementById('avgSaleValue').textContent    = sales.length ? `₮${(totalRev / sales.length).toLocaleString(undefined, {maximumFractionDigits:0})}` : '₮0';

        const productSales = {};
        sales.forEach(s => { productSales[s.productName] = (productSales[s.productName] || 0) + s.qty; });
        const best = Object.entries(productSales).sort((a,b) => b[1]-a[1])[0];
        document.getElementById('bestProduct').textContent = best ? best[0] : 'None';

        const catSales = {};
        sales.forEach(s => {
            const p = products.find(p => p.name === s.productName);
            if (p) catSales[p.category] = (catSales[p.category] || 0) + s.total;
        });
        const topCat = Object.entries(catSales).sort((a,b) => b[1]-a[1])[0];
        document.getElementById('topCategory').textContent = topCat ? topCat[0] : 'None';
    }

    function createCharts() {
        createSalesChart();
        createCategoryChart();
        createMonthlyChart();
    }

    function createSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;
        if (salesChart) salesChart.destroy();

        const byDate = {};
        sales.forEach(s => { byDate[s.date] = (byDate[s.date] || 0) + parseFloat(s.total); });
        const labels = Object.keys(byDate).slice(-14);
        const data   = labels.map(d => byDate[d]);

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Daily Sales (₮)',
                    data,
                    borderColor: '#000',
                    backgroundColor: 'rgba(0,0,0,0.05)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#c8a96e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: true } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '₮' + v.toLocaleString() } } }
            }
        });
    }

    function createCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;
        if (categoryChart) categoryChart.destroy();

        const cats  = [...new Set(products.map(p => p.category))];
        const data  = cats.map(c => products.filter(p => p.category === c).reduce((a, p) => a + p.stock, 0));
        const colors = ['#1a1a1a','#c8a96e','#555','#888','#bbb'];

        categoryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: cats,
                datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'right' } }
            }
        });
    }

    function createMonthlyChart() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;
        if (monthlyChart) monthlyChart.destroy();

        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const monthly = Array(12).fill(0);
        sales.forEach(s => {
            const m = new Date(s.timestamp).getMonth();
            monthly[m] += parseFloat(s.total);
        });

        monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Revenue (₮)',
                    data: monthly,
                    backgroundColor: months.map((_, i) => i === new Date().getMonth() ? '#c8a96e' : '#1a1a1a'),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => '₮' + v.toLocaleString() } } }
            }
        });
    }

    function exportData() {
        let csv = "DATA TYPE,ID/DATE,NAME,CATEGORY,PRICE,STOCK/QTY,TOTAL,CUSTOMER\n";

        products.forEach(p => {
            csv += ["PRODUCT", p.id, `"${p.name}"`, p.category, p.price, p.stock, "", ""].join(",") + "\n";
        });

        csv += "\nSALES HISTORY\n";

        sales.forEach(s => {
            csv += ["SALE", s.date, `"${s.productName}"`, "", s.unitPrice || "", s.qty, s.total, `"${s.customer}"`].join(",") + "\n";
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url  = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "ayalagch_4x4_backup.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function clearAllData() {
        if (confirm("WARNING: This will clear all data. Are you sure?")) {
            showMessage('clearMessage', 'To reset database tables, please use phpMyAdmin. This feature is disabled for safety.', 'error');
        }
    }

    function showMessage(id, msg, type) {
        const el = document.getElementById(id);
        el.textContent = msg;
        el.className   = 'message ' + type;
        el.style.display = 'block';
        setTimeout(() => el.style.display = 'none', 5000);
    }

    document.addEventListener('DOMContentLoaded', loadFromDatabase);
</script>
</body>
</html>