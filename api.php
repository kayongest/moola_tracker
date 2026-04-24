<?php
// api.php - Backend logic for MoolaTracker
header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            $stmt = $db->query("SELECT * FROM loans ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
            break;

        case 'add':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) throw new Exception("Invalid data");

            $stmt = $db->prepare("INSERT INTO loans (borrower_name, lender_name, amount, request_date, payment_date, percentage, total_repayment) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['borrower_name'],
                $data['lender_name'],
                $data['amount'],
                $data['request_date'],
                $data['payment_date'],
                $data['percentage'],
                $data['total_repayment']
            ]);
            echo json_encode(['success' => true, 'message' => 'Loan recorded successfully']);
            break;

        case 'update_status':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $db->prepare("UPDATE loans SET status = ?, amount_paid = (CASE WHEN ? = 'Paid' THEN total_repayment ELSE 0 END) WHERE id = ?");
            $stmt->execute([$data['status'], $data['status'], $data['id']]);
            echo json_encode(['success' => true]);
            break;

        case 'delete':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("DELETE FROM loans WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'stats':
            $stats = [
                'total_lent' => $db->query("SELECT SUM(amount) FROM loans")->fetchColumn() ?: 0,
                'expected_return' => $db->query("SELECT SUM(total_repayment) FROM loans")->fetchColumn() ?: 0,
                'total_remaining' => $db->query("SELECT SUM(total_repayment - amount_paid) FROM loans")->fetchColumn() ?: 0,
                'pending_count' => $db->query("SELECT COUNT(*) FROM loans WHERE status = 'Pending'")->fetchColumn() ?: 0
            ];
            echo json_encode($stats);
            break;

        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
