<?php
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getUsersCards($boards) {
    $allCards = [];
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
            "https://api.trello.com/1/boards/{$boardId}/cards/open?" . http_build_query($query)
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
        $allCards = array_merge($allCards, $cards);
        echo "fetched cards for board: " . $board->name;
        echo "\n";
    }
    // sort cards for processing in getCheckListItems
    usort($allCards, function($a, $b){
        // Prioritize non-empty checkItemStates first
        $aEmpty = empty($a->checkItemStates) ? 1 : 0;
        $bEmpty = empty($b->checkItemStates) ? 1 : 0;
            return $aEmpty <=> $bEmpty;
    });
    
    echo "cards are sorted...\n";
    return $allCards;
}
?>