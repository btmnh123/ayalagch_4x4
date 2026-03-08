<?php
require_once 'config_db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$action = $data['action'];

try {
    switch ($action) {
        case 'fetchData':
            $productsResult = $conn->query("SELECT * FROM products ORDER BY id DESC");
            $products = [];
            if ($productsResult) {
                while ($row = $productsResult->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            
            $salesResult = $conn->query("SELECT * FROM sales ORDER BY sale_date DESC, id DESC");
            $sales = [];
            if ($salesResult) {
                while ($row = $salesResult->fetch_assoc()) {
                    $sales[] = $row;
                }
            }
            
            echo json_encode([
                "status" => "success",
                "products" => $products,
                "sales" => $sales
            ]);
            break;

        case 'addProduct':
            $name = $conn->real_escape_string($data['name']);
            $price = floatval($data['price']);
            $stock = intval($data['stock']);
            $category = $conn->real_escape_string($data['category']);
            $image = isset($data['image']) ? $conn->real_escape_string($data['image']) : '';
            
            $sql = "INSERT INTO products (name, price, stock, category, image) 
                    VALUES ('$name', $price, $stock, '$category', '$image')";
            
            if ($conn->query($sql)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Product added successfully",
                    "id" => $conn->insert_id
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Error: " . $conn->error
                ]);
            }
            break;

        case 'updateStock':
            $productName = $conn->real_escape_string($data['productName']);
            $newStock = intval($data['newStock']);
            
            $sql = "UPDATE products SET stock = $newStock WHERE name = '$productName'";
            
            if ($conn->query($sql)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Stock updated successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Error: " . $conn->error
                ]);
            }
            break;

        case 'processSale':
            $productId = intval($data['productId']);
            $productName = $conn->real_escape_string($data['productName']);
            $quantity = intval($data['qty']);
            $total = floatval($data['total']);
            $customer = $conn->real_escape_string($data['customer'] ?? 'Guest');
            $saleDate = date('Y-m-d H:i:s');
            
            $conn->begin_transaction();
            
            try {
                $sqlSale = "INSERT INTO sales (sale_date, product_name, quantity, total_price, customer_name) 
                           VALUES ('$saleDate', '$productName', $quantity, $total, '$customer')";
                
                if (!$conn->query($sqlSale)) {
                    throw new Exception("Error inserting sale: " . $conn->error);
                }
                
                $sqlStock = "UPDATE products SET stock = stock - $quantity WHERE id = $productId";
                
                if (!$conn->query($sqlStock)) {
                    throw new Exception("Error updating stock: " . $conn->error);
                }
                
                if ($conn->affected_rows == 0) {
                    throw new Exception("Product not found or insufficient stock");
                }
                
                $conn->commit();
                echo json_encode([
                    "status" => "success",
                    "message" => "Sale processed successfully"
                ]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]);
            }
            break;

        case 'deleteProduct':
            $productId = intval($data['productId']);
            
            $sql = "DELETE FROM products WHERE id = $productId";
            
            if ($conn->query($sql)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Product deleted successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Error: " . $conn->error
                ]);
            }
            break;

        case 'deleteSale':
            $saleId = intval($data['saleId']);
            $productName = $conn->real_escape_string($data['productName']);
            $quantity = intval($data['quantity']);
            
            $conn->begin_transaction();
            
            try {
                $sqlDelete = "DELETE FROM sales WHERE id = $saleId";
                
                if (!$conn->query($sqlDelete)) {
                    throw new Exception("Error deleting sale: " . $conn->error);
                }
                
                $sqlRestore = "UPDATE products SET stock = stock + $quantity WHERE name = '$productName'";
                $restoreResult = $conn->query($sqlRestore);
                
                if ($restoreResult && $conn->affected_rows > 0) {
                    $conn->commit();
                    echo json_encode([
                        "status" => "success",
                        "message" => "Sale deleted and stock restored successfully"
                    ]);
                } else {
                    $conn->commit();
                    echo json_encode([
                        "status" => "success",
                        "message" => "Sale deleted successfully (product no longer exists, stock not restored)"
                    ]);
                }
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode([
                    "status" => "error",
                    "message" => $e->getMessage()
                ]);
            }
            break;

        case 'getAnalytics':
            $revenueResult = $conn->query("SELECT SUM(total_price) as total FROM sales");
            $totalRevenue = $revenueResult->fetch_assoc()['total'] ?? 0;
            
            $salesCountResult = $conn->query("SELECT COUNT(*) as count FROM sales");
            $totalSales = $salesCountResult->fetch_assoc()['count'] ?? 0;
            
            $todayResult = $conn->query("SELECT SUM(total_price) as total FROM sales WHERE DATE(sale_date) = CURDATE()");
            $todaySales = $todayResult->fetch_assoc()['total'] ?? 0;
            
            $lowStockResult = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock <= 5");
            $lowStockCount = $lowStockResult->fetch_assoc()['count'] ?? 0;
            
            $bestProductResult = $conn->query("SELECT product_name, SUM(quantity) as total_qty FROM sales GROUP BY product_name ORDER BY total_qty DESC LIMIT 1");
            $bestProduct = $bestProductResult->num_rows > 0 ? $bestProductResult->fetch_assoc() : null;
            
            echo json_encode([
                "status" => "success",
                "analytics" => [
                    "totalRevenue" => floatval($totalRevenue),
                    "totalSales" => intval($totalSales),
                    "todaySales" => floatval($todaySales),
                    "lowStockCount" => intval($lowStockCount),
                    "bestProduct" => $bestProduct
                ]
            ]);
            break;

        default:
            echo json_encode([
                "status" => "error",
                "message" => "Unknown action"
            ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

$conn->close();
?>
