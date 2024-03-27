<?php
require __DIR__ . '/vendor/autoload.php';
// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getAllBoards($userId) {
    // Access environment variables
    $apiKey = $_ENV['API_KEY'];
    $apiToken = $_ENV['TOKEN'];

    $query = array(
        'key' => $apiKey,
        'token' => $apiToken
    );

    // Construct the API URL
    $url = "https://api.trello.com/1/members/{$userId}/boards?" . http_build_query($query);

    // Fetch boards data
    $response = @file_get_contents($url);
    if ($response === false) {
        // Handle the error, maybe log it
        return []; // Return an empty array
    }

    // Decode JSON response
    $boards = json_decode($response);

    // Check if JSON decoding was successful
    if ($boards === null) {
        echo "boards is null";
        // Handle the decoding error
        return []; // Return an empty array
    }
    echo "user " . $userId . " boards are fetched...\n";
    return $boards;
}
?>