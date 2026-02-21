<?php
/**
 * Authentication Handler
 * Handles user login, registration, password reset
 */

header('Content-Type: application/json');

require_once '../config/db-config.php';

$action = $_GET['action'] ?? '';

if ($action === 'register') {
    handleRegistration();
} elseif ($action === 'login') {
    handleLogin();
} elseif ($action === 'verify-email') {
    verifyEmail();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Handle User Registration
 */
function handleRegistration() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate input
    if (!isset($data['email']) || !isset($data['firebaseUid']) || !isset($data['freeFireUid']) || !isset($data['gameName'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    $email = sanitize($data['email']);
    $firebase_uid = sanitize($data['firebaseUid']);
    $free_fire_uid = sanitize($data['freeFireUid']);
    $game_name = sanitize($data['gameName']);
    $age = intval($data['age'] ?? 0);
    
    // Validate age
    if ($age < 18) {
        echo json_encode(['success' => false, 'message' => 'You must be at least 18 years old']);
        return;
    }
    
    // Check if email already exists
    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        return;
    }
    
    // Check if Free Fire UID already exists
    $check_uid = "SELECT id FROM users WHERE free_fire_uid = '$free_fire_uid'";
    $result = $conn->query($check_uid);
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Free Fire UID already registered']);
        return;
    }
    
    // Insert new user
    $sql = "INSERT INTO users (firebase_uid, email, free_fire_uid, game_name, age, level, tournament_count, total_points, wallet_balance) 
            VALUES ('$firebase_uid', '$email', '$free_fire_uid', '$game_name', $age, 1, 0, 0, 0.00)";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully',
            'userId' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
    }
}

/**
 * Handle User Login
 */
function handleLogin() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['email']) || !isset($data['firebaseUid'])) {
        echo json_encode(['success' => false, 'message' => 'Email and UID required']);
        return;
    }
    
    $email = sanitize($data['email']);
    $firebase_uid = sanitize($data['firebaseUid']);
    
    // Get user
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'gameName' => $user['game_name'],
                'level' => $user['level'],
                'walletBalance' => $user['wallet_balance']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}

/**
 * Verify Email
 */
function verifyEmail() {
    global $conn;
    
    $email = sanitize($_GET['email'] ?? '');
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email not provided']);
        return;
    }
    
    // Update user as verified
    $sql = "UPDATE users SET email_verified = 1 WHERE email = '$email'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Email verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Verification failed']);
    }
}

?>