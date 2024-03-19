<?php
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getUsersCards($boards, $userId) {
    $usersCards = [];

    // Access environment variables
    $apiKey = $_ENV['API_KEY'];
    $apiToken = $_ENV['TOKEN'];

    $query = array(
        'key' => $apiKey,
        'token' => $apiToken
    );

    // for each board, get all cards
    foreach($boards as $board ) {
        $boardId = $board->id;
        $response = file_get_contents(
            "https://api.trello.com/1/boards/{$boardId}/cards?" . http_build_query($query)
        );
        
        if ($response === false) {
            // handle error somehow
            continue;
        }
        
        // turn string into json
        $cards = json_decode($response);

        // check if decoding was successful
        if ($cards === null){
            // move to next board
            continue;
        }

        // if card contains userId, add card ID to usersCards
        foreach($cards as $card){
            if(in_array($userId, $card->idMembers)){
                // user is a member of this card
                $usersCards[] = $card->id;
            }
        }
    }
    // print_r($usersCards);
    //return array of user's cards
    return $usersCards;
}
?>