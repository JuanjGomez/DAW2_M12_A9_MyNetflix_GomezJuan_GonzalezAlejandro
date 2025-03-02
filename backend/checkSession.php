<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'isLoggedIn' => isset($_SESSION['idUser']),
    'userId' => isset($_SESSION['idUser']) ? $_SESSION['idUser'] : null
]);
