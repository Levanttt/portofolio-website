<?php
// admin/update_order.php - Fixed for Database class
session_start();

// Prevent any output before JSON
error_reporting(0);
ini_set('display_errors', 0);

ob_start();

try {
    require_once __DIR__ . '/../config/database.php';
    
    // Check admin login
    if (!isset($_SESSION['admin_logged_in'])) {
        throw new Exception('Unauthorized');
    }
    
    // Only accept POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    // Get JSON input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }
    
    if (!isset($input['order']) || !is_array($input['order']) || empty($input['order'])) {
        throw new Exception('Invalid or empty order array');
    }
    
    // Get database connection using your Database class
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Start transaction
    $conn->beginTransaction();
    
    // Prepare statement
    $stmt = $conn->prepare("UPDATE projects SET display_order = :order WHERE id = :id");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }
    
    // Execute updates
    $count = 0;
    foreach ($input['order'] as $index => $projectId) {
        $projectId = intval($projectId);
        
        if ($projectId <= 0) {
            continue; // Skip invalid IDs
        }
        
        $order = $index + 1;
        
        $stmt->bindParam(':order', $order, PDO::PARAM_INT);
        $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update project ID: ' . $projectId);
        }
        
        $count++;
    }
    
    // Commit transaction
    $conn->commit();
    
    // Clear output buffer and send success JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully',
        'updated_count' => $count
    ]);
    
} catch (Exception $e) {
    // Rollback if transaction is active
    if (isset($conn) && $conn instanceof PDO && $conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log error
    error_log('Update order error: ' . $e->getMessage());
    
    // Clear output buffer and send error JSON
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
?>