<?php
class Rental {
    private $conn;
    private $table = 'rentals';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function requestBorrow($user_id, $item_id, $borrower_name, $location, $duration, $quantity) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
             (user_id, item_id, borrower_name, borrow_location, duration_days, quantity, status)
             VALUES (?, ?, ?, ?, ?, ?, 'pending_borrow')"
        );
        return $stmt->execute([$user_id, $item_id, $borrower_name, $location, $duration, $quantity]);
    }

    public function confirmBorrow($rental_id) {
        $rental = $this->getById($rental_id);
        if (!$rental || $rental['status'] !== 'pending_borrow') return false;

        $deadline = date('Y-m-d H:i:s', strtotime("+{$rental['duration_days']} days"));
        $this->conn->beginTransaction();
        try {
            // kurangi stok
            $stmt = $this->conn->prepare("UPDATE items SET stock = stock - ? WHERE id=? AND stock >= ?");
            $stmt->execute([$rental['quantity'], $rental['item_id'], $rental['quantity']]);
            if ($stmt->rowCount() === 0) {
                $this->conn->rollBack();
                return false;
            }
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table} SET status='active', borrow_confirmed_at=NOW(), return_deadline=? WHERE id=?"
            );
            $stmt->execute([$deadline, $rental_id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function rejectBorrow($rental_id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status='rejected' WHERE id=? AND status='pending_borrow'");
        return $stmt->execute([$rental_id]);
    }

    public function requestReturn($rental_id, $admin_id, $money_paid, $money_change, $return_location) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table}
             SET status='pending_return', return_admin_id=?, money_paid=?, money_change=?, return_location=?, return_requested_at=NOW()
             WHERE id=? AND status='active'"
        );
        return $stmt->execute([$admin_id, $money_paid, $money_change, $return_location, $rental_id]);
    }

    public function confirmReturn($rental_id) {
        $rental = $this->getById($rental_id);
        if (!$rental || $rental['status'] !== 'pending_return') return false;

        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("UPDATE items SET stock = stock + ? WHERE id=?");
            $stmt->execute([$rental['quantity'], $rental['item_id']]);
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table} SET status='returned', return_confirmed_at=NOW() WHERE id=?"
            );
            $stmt->execute([$rental_id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function rejectReturn($rental_id) {
        $rental = $this->getById($rental_id);
        if (!$rental || $rental['status'] !== 'pending_return') return false;

        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET status='active', return_admin_id=NULL, money_paid=NULL, money_change=NULL, return_location=NULL, return_requested_at=NULL WHERE id=?"
        );
        return $stmt->execute([$rental_id]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare(
            "SELECT r.*, i.name AS item_name, i.image AS item_image, i.price_per_day
             FROM {$this->table} r
             JOIN items i ON r.item_id = i.id
             WHERE r.user_id=? ORDER BY r.created_at DESC"
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getPendingBorrows() {
        return $this->conn->query(
            "SELECT r.*, i.name AS item_name, i.price_per_day, u.username, u.full_name AS user_full_name
             FROM {$this->table} r
             JOIN items i ON r.item_id=i.id
             JOIN users u ON r.user_id=u.id
             WHERE r.status='pending_borrow' ORDER BY r.created_at ASC"
        )->fetchAll();
    }

    public function getPendingReturns() {
        return $this->conn->query(
            "SELECT r.*, i.name AS item_name, u.username, u.full_name AS user_full_name,
                    a.full_name AS admin_name
             FROM {$this->table} r
             JOIN items i ON r.item_id=i.id
             JOIN users u ON r.user_id=u.id
             LEFT JOIN users a ON r.return_admin_id=a.id
             WHERE r.status='pending_return' ORDER BY r.return_requested_at ASC"
        )->fetchAll();
    }

    public function getAll() {
        return $this->conn->query(
            "SELECT r.*, i.name AS item_name, u.username
             FROM {$this->table} r
             JOIN items i ON r.item_id=i.id
             JOIN users u ON r.user_id=u.id
             ORDER BY r.created_at DESC"
        )->fetchAll();
    }
}
