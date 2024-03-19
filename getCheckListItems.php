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
        if(isset($card->idChecklists)) {
            // Loop through each checklist of the card
            foreach($card->idChecklists as $checklistId) {
                // Construct the API URL to fetch checklist items
                $url = "https://api.trello.com/1/checklists/{$checklistId}/checkItems?" . http_build_query($query);
                
                // Fetch checklist items data
                $response = @file_get_contents($url);
                if ($response === false) {
                    // Handle the error, maybe log it
                    continue; // Skip to the next checklist
                }

                // Decode JSON response
                $checklistItemsData = json_decode($response);
                print_r("checklist items data" . $checklistItemsData);

                // Check if JSON decoding was successful
                if ($checklistItemsData === null) {
                    // Handle the decoding error
                    continue; // Skip to the next checklist
                }

                // Loop through each checklist item
                foreach($checklistItemsData as $checklistItem) {
                    // Check if the checklist item contains the userId
                    if(in_array($userId, $checklistItem->idMembers)) {
                        // Add the checklist item to the array
                        $checklistItems[] = $checklistItem;
                    }
                }
            }
        }
    }
    // Return array of checklist items
    return $checklistItems;
}
?>