<?php
/**
 * Tournament Handler
 * Manage tournaments and registrations
 */

header('Content-Type: application/json');

require_once '../config/db-config.php';

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    listTournaments();
} elseif ($action === 'get') {
    getTournament();
} elseif ($action === 'register') {
    registerTournament();
} elseif ($action === 'my-tournaments') {
    getMyTournaments();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * List all tournaments
 */
function listTournaments() {
    global $conn;
    
    $level = intval($_GET['level'] ?? 0);
    $status = sanitize($_GET['status'] ?? 'upcoming');
    
    $where = "WHERE status = '$status'";
    if ($level > 0) {
        $where .= " AND level = $level";
    }
    
    $sql = "SELECT * FROM tournaments $where ORDER BY start_date ASC LIMIT 50";
    $result = $conn->query($sql);
    
    $tournaments = [];
    while ($row = $result->fetch_assoc()) {
        $tournaments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'tournaments' => $tournaments,
        'count' => count($tournaments)
    ]);
}

/**
 * Get single tournament
 */
function getTournament() {
    global $conn;
    
    $id = intval($_GET['id'] ?? 0);
    
    $sql = "SELECT * FROM tournaments WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $tournament = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'tournament' => $tournament
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tournament not found']);
    }
}

/**
 * Register user for tournament
 */
function registerTournament() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $user_id = intval($data['userId'] ?? 0);
    $tournament_id = intval($data['tournamentId'] ?? 0);
    $squad_members = json_encode($data['squadMembers'] ?? []);
    
    if ($user_id === 0 || $tournament_id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid user or tournament']);
        return;
    }
    
    // Check if already registered
    $check = "SELECT id FROM registrations WHERE user_id = $user_id AND tournament_id = $tournament_id";
    $result = $conn->query($check);
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Already registered for this tournament']);
        return;
    }
    
    // Insert registration
    $sql = "INSERT INTO registrations (user_id, tournament_id, squad_members, payment_status) 
            VALUES ($user_id, $tournament_id, '$squad_members', 'pending')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Registered successfully',
            'registrationId' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
    }
}

/**
 * Get user's tournaments
 */
function getMyTournaments() {
    global $conn;
    
    $user_id = intval($_GET['userId'] ?? 0);
    
    $sql = "SELECT t.* FROM tournaments t 
            JOIN registrations r ON t.id = r.tournament_id 
            WHERE r.user_id = $user_id 
            ORDER BY t.start_date DESC";
    
    $result = $conn->query($sql);
    
    $tournaments = [];
    while ($row = $result->fetch_assoc()) {
        $tournaments[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'tournaments' => $tournaments,
        'count' => count($tournaments)
    ]);
}

?>