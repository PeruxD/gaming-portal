<?php
header('Content-Type: application/json');
include '../config/database.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $query = "INSERT INTO newsletter_subscribers (email) VALUES ('$email')
                  ON DUPLICATE KEY UPDATE is_active = 1";
        
        if (mysqli_query($conn, $query)) {
            $response['success'] = true;
            $response['message'] = 'Suscripción exitosa';
        } else {
            $response['message'] = 'Error en la suscripción';
        }
    } else {
        $response['message'] = 'Email inválido';
    }
}

echo json_encode($response);
?>