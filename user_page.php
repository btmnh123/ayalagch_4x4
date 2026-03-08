<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
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
    <title>Products - АЯЛАГЧ 4X4</title>
    <style>
        :root {
            --primary: #000000;
            --secondary: #f5f5f5;
            --text-dark: #333;
            --text-light: #666;
            --border: #ddd;
            --white: #ffffff;
            --danger: #d32f2f;
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

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-text {
            font-size: 0.9em;
            color: var(--text-dark);
        }

        .logout-btn {
            background-color: var(--danger);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #b71c1c;
        }

        .main-content {
            background-color: var(--white);
            border: 1px solid var(--border);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        h2 {
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .product-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .detail-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .detail-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .detail-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background-color: var(--secondary);
        }

        .detail-info { 
            padding: 15px; 
        }

        .detail-info h4 {
            margin-bottom: 10px;
            color: var(--text-dark);
            font-size: 1.1em;
        }

        .detail-info .price {
            color: var(--primary);
            font-weight: bold;
            font-size: 1.2em;
            margin: 10px 0;
        }

        .detail-info .meta {
            font-size: 0.9em;
            color: var(--text-light);
            margin: 5px 0;
        }

        .stock-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
            margin-top: 8px;
        }

        .stock-in-stock {
            background-color: #d4edda;
            color: #155724;
        }

        .stock-low {
            background-color: #fff3cd;
            color: #856404;
        }

        .stock-out {
            background-color: #f8d7da;
            color: #721c24;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-light);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--text-light);
        }

        .refresh-indicator {
            font-size: 0.8em;
            color: var(--text-light);
            text-align: right;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .container { 
                padding: 10px; 
            }
            .header-bar { 
                flex-direction: column; 
                text-align: center; 
                gap: 15px;
            }
            .header-right {
                flex-direction: column;
                gap: 10px;
            }
            .product-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h1>АЯЛАГЧ 4X4 - Products</h1>
            <div class="header-right">
                <?php if ($userName): ?>
                    <div class="welcome-text">Welcome, <?php echo htmlspecialchars($userName); ?></div>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="main-content">
            <div class="refresh-indicator" id="lastUpdated">Last updated: Just now</div>
            <h2>Product Catalog</h2>
            <div id="productsList" class="product-detail-grid">
                <div class="loading">Loading products...</div>
            </div>
        </div>
    </div>

    <script>
        let products = [];
        let refreshInterval;

        function loadProducts() {
            fetch('../aylagch_4x4/save_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'fetchData' })
            })
            .then(response => response.json())
            .then(result => {
                if(result.status === "success") {
                    products = (result.products || []).map(p => ({
                        ...p,
                        id: parseInt(p.id),
                        price: parseFloat(p.price),
                        stock: parseInt(p.stock)
                    }));
                    
                    displayProducts();
                    updateLastUpdated();
                } else {
                    document.getElementById('productsList').innerHTML = 
                        '<div class="empty-state">Error loading products. Please try again.</div>';
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById('productsList').innerHTML = 
                    '<div class="empty-state">Error connecting to server. Please check your connection.</div>';
            });
        }

        function displayProducts() {
            const container = document.getElementById('productsList');
            
            if(products.length === 0) {
                container.innerHTML = '<div class="empty-state">No products available at the moment.</div>';
                return;
            }
            
            container.innerHTML = products.map(p => {
                const imageSrc = p.image || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2RkZCIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';
                
                let stockClass = 'stock-in-stock';
                let stockText = `${p.stock} in stock`;
                if (p.stock === 0) {
                    stockClass = 'stock-out';
                    stockText = 'Out of stock';
                } else if (p.stock <= 5) {
                    stockClass = 'stock-low';
                    stockText = `Low stock (${p.stock})`;
                }
                
                return `
                    <div class="detail-card">
                        <img src="${imageSrc}" 
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2RkZCIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM5OTkiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIj5ObyBJbWFnZTwvdGV4dD48L3N2Zz4=';"
                             alt="${p.name}">
                        <div class="detail-info">
                            <h4>${p.name}</h4>
                            <div class="price">₮${parseFloat(p.price).toFixed(2)}</div>
                            <div class="meta">Category: ${p.category}</div>
                            <div>
                                <span class="stock-badge ${stockClass}">${stockText}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function updateLastUpdated() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.getElementById('lastUpdated').textContent = `Last updated: ${timeString}`;
        }

        loadProducts();

        refreshInterval = setInterval(loadProducts, 5000);

        window.addEventListener('beforeunload', () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        });
    </script>
</body>
</html>
