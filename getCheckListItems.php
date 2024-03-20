<?php
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getCheckListItems($cards, $userId) {
    $checklistItems = [];

    // Loop through each card
    foreach($cards as $card) {
        // Access environment variables
        $apiKey = $_ENV['API_KEY'];
        $apiToken = $_ENV['TOKEN'];

        $query = array(
            'key' => $apiKey,
            'token' => $apiToken
        );
            // Check if the card has checklists
            if(!empty($card->checkItemStates)) {
                // Loop through each checklist of the card
                foreach($card->checkItemStates as $checkItemState) {
                    $checkItemId = $checkItemState->idCheckItem;
                    // fetch checklist items
                    $url = "https://api.trello.com/1/cards/$card->id/checkItem/{$checkItemId}?" . http_build_query($query);
                    // Fetch checklist items data
                    $response = @file_get_contents($url);
                    if ($response === false) {
                        echo "response === false";
                        // Handle the error, maybe log it
                        continue; // Skip to the next check item
                    }
                    // Decode JSON response
                    $checkItemData = json_decode($response);

                    // Check if JSON decoding was successful
                    if ($checkItemData === null) {
                        echo "response === null";
                        // Handle the decoding error
                        continue; // Skip to the next check item
                    }

                    
                    // Loop through each checklist item
                    // Check if the checklist item contains the userId
                    if(($userId === $checkItemData->idMember)) {
                        if ($checkItemData->state != "complete"){
                            // Add the checklist item to the array if item is not complete
                            $checklistItems[] = $checkItemData;
                        }
                    }
                }
            }
    }
    echo "checklist items: ";
    print_r($checklistItems);
    // Return array of checklist items
    return $checklistItems;
}
?>