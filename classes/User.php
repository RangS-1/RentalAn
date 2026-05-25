<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $full_name, $email = null, $phone = null) {
        // username 'admin' otomatis jadi admin
        $role = (strtolower($username) === 'admin') ? 'admin' : 'user';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (username, password, full_name, email, phone, role)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$username, $hash, $full_name, $email, $phone, $role]);
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // Auto-promote ke admin jika username persis 'admin'
            if (strtolower($user['username']) === 'admin' && $user['role'] !== 'admin') {
                $this->conn->prepare("UPDATE {$this->table} SET role='admin' WHERE id=?")
                           ->execute([$user['id']]);
                $user['role'] = 'admin';
            }
            return $user;
        }
        return false;
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return (bool) $stmt->fetch();
    }

    public function getAll() {
        return $this->conn->query("SELECT id, username, full_name, email, phone, role, created_at FROM {$this->table} ORDER BY created_at DESC")->fetchAll();
    }

    public function getAllAdmins() {
        $stmt = $this->conn->prepare("SELECT id, full_name, username FROM {$this->table} WHERE role='admin'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateRole($id, $role) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET role=? WHERE id=?");
        return $stmt->execute([$role, $id]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }
}
