<?php
require_once "../config/database.php";

// Function to start a session safely
function start_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Function to check if the user is logged in
function is_logged_in() {
    start_session();
    return isset($_SESSION['user_id']);
}

// Function to check if the user is an admin
function is_admin() {
    start_session();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

// Function to check if the user can manage a specific laboratory
function can_manage_lab($lab_name) {
    $role = $_SESSION['role'] ?? '';
    return ($role === 'Admin') ||
           ($role === 'Microbiology Lab Manager') && $lab_name === 'Microbiology' ||
           ($role === 'Chemical Lab Manager') && $lab_name === 'Chemical' ||
           ($role === 'Metrology Lab Manager') && $lab_name === 'Metrology';
}

function can_add_lab() {
    $role = $_SESSION['role'] ?? '';
    return ($role === 'Admin') ||
           ($role === 'Microbiology Lab Manager')||
           ($role === 'Chemical Lab Manager')||
           ($role === 'Metrology Lab Manager');
}

// Function to check if the user can manage a specific item
function can_manage_item() {
    $role = $_SESSION['role'] ?? '';
    return ($role === 'Admin') ||
           ($role === 'Microbiology Lab Manager')||
           ($role === 'Chemical Lab Manager')||
           ($role === 'Metrology Lab Manager');
}

// Function to check if the user can manage a specific laboratory
function can_use_inventory($lab_name) {
    $role = $_SESSION['role'] ?? '';
    return ($role === 'Admin') ||
           ($role === 'Microbiology Lab Manager') && $lab_name === 'Microbiology' ||
           ($role === 'Chemical Lab Manager') && $lab_name === 'Chemical' ||
           ($role === 'Metrology Lab Manager') && $lab_name === 'Metrology' ||
           ($role === 'Microbiology Analyst') && $lab_name === 'Microbiology' ||
           ($role === 'Chemical Analyst') && $lab_name === 'Chemical' ||
           ($role === 'Metrology Analyst') && $lab_name === 'Metrology';
}

// Function to check if the user can manage a specific laboratory
function can_editdelete_inventory($lab_name) {
    $role = $_SESSION['role'] ?? '';
    return ($role === 'Admin') ||
           ($role === 'Microbiology Lab Manager') && $lab_name === 'Microbiology' ||
           ($role === 'Chemical Lab Manager') && $lab_name === 'Chemical' ||
           ($role === 'Metrology Lab Manager') && $lab_name === 'Metrology';
}

// Function to check if the user is a Lab Manager
function is_lab_manager() {
    start_session();
    $role = $_SESSION['role'] ?? '';
    return in_array($role, [
        'Microbiology Lab Manager',
        'Chemical Lab Manager',
        'Metrology Lab Manager'
    ]);
}

// Function to check if the user is an Analyst
function is_analyst() {
    start_session();
    $role = $_SESSION['role'] ?? '';
    return in_array($role, [
        'Microbiology Analyst',
        'Chemical Analyst',
        'Metrology Analyst'
    ]);
}

// Function to check if the user is a CRO
function is_cro() {
    start_session();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'CRO';
}

// Function to check if the user can add inventory
function can_add_inventory() {
    return is_admin() || is_lab_manager();
}

// Function to execute a SQL query with prepared statements
function execute_query($sql, $params) {
    global $link;
    $stmt = mysqli_prepare($link, $sql);
    if ($stmt) {
        if ($params) {
            $types = str_repeat('s', count($params)); 
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        return $stmt;
    } else {
        // Log and handle SQL errors
        log_error("MySQL prepare error: " . mysqli_error($link));
        return false;
    }
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to log errors
function log_error($error_message) {
    error_log(date('[Y-m-d H:i:s]') . ' ' . $error_message . "\n", 3, 'error.log');
}
?>