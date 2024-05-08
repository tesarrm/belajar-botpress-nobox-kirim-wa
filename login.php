<?php
include "utils/header.php";
include 'nobox/nobox.php';

switch ($method) {
    case 'POST':
        // Memeriksa apakah email dan password diberikan
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Generate token
            $token = generateToken($email, $password);

            // Masukkan token ke database
            $sql = "INSERT INTO token_nobox (email, token) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $token);
            if ($stmt->execute()) {
                echo json_encode(array("message" => "Autentikasi berhasil", "token_nobox" => $token));
            } else {
                echo json_encode(array('message' => 'Error: ' . $stmt->error));
            }
            $stmt->close();
        } else {
            echo json_encode(array('message' => 'Email dan password harus diberikan'));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array('error' => 'Method not allowed'));
        break;
}

function generateToken($email, $password)
{
    $nobox = new Nobox(null);
    $tokenResponse = $nobox->generateToken($email, $password);
    return $tokenResponse->Data;
}
