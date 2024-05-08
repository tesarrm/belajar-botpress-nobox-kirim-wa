<?php
include 'db_connection.php'; // $conn

header("content-type:application/json");

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];
$results = array();
