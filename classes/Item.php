<?php
class Item {
    private $conn;
    private $table = 'items';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($name, $description, $price_per_day, $stock, $image) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (name, description, price_per_day, stock, image)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $description, $price_per_day, $stock, $image]);
    }

    public function update($id, $name, $description, $price_per_day, $stock, $image = null) {
        if ($image) {
            $stmt = $this->conn->prepare(
                "UPDATE {$this->table} SET name=?, description=?, price_per_day=?, stock=?, image=? WHERE id=?"
            );
            return $stmt->execute([$name, $description, $price_per_day, $stock, $image, $id]);
        }
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET name=?, description=?, price_per_day=?, stock=? WHERE id=?"
        );
        return $stmt->execute([$name, $description, $price_per_day, $stock, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function decreaseStock($id, $qty) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stock = stock - ? WHERE id=? AND stock >= ?");
        return $stmt->execute([$qty, $id, $qty]);
    }

    public function increaseStock($id, $qty) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET stock = stock + ? WHERE id=?");
        return $stmt->execute([$qty, $id]);
    }
}
