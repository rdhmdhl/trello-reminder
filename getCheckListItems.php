<?php
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function getCheckListItems($cards, $userId) {
    // echo "number of cards: ";
    // var_dump(count($cards));
    $checklistItems = [];
    $baseApiUrl = "https://api.trello.com/1/checklists";
    $batchBaseUrl = "https://api.trello.com/1/batch?urls=";
    $urls = [];
    $checklistToCardUrl = [];

    if (empty($cards)){
        echo "no cards passed to getCheckListItems \n";
        return $checklistItems;
    }

    foreach ($cards as $card) {
        foreach ($card->idChecklists as $idChecklist) {
            $urls[] = "/checklists/{$idChecklist}/checkItems";
            $checklistToCardUrl[$idChecklist] = $card->url;
        }
    }

    $chunks = array_chunk($urls, 10);

    // Loop through each card
    foreach($chunks as $chunk) {
        // Access environment variables
        $apiKey = $_ENV['API_KEY'];
        $apiToken = $_ENV['TOKEN'];

        $query = array(
            'key' => $apiKey,
            'token' => $apiToken,
            'fields' => 'idMember'
        );

        $encodedUrls = urlencode(implode(',', $chunk));
        $batchUrl = "{$batchBaseUrl}{$encodedUrls}&" . http_build_query($query);
        
        $response = file_get_contents($batchUrl);
        if ($response === false) {
            continue; // Consider logging this error
        }

        $data = json_decode($response, true);
        if (is_null($data)) {
            continue; // Consider logging this error
        }

        // Loop through each checklist of the card
        foreach($data as $batchResponse) {
            if (isset($batchResponse['200'])){
                foreach ($batchResponse['200'] as $item){
                    if(isset($item['idMember']) && $item['idMember'] === $userId && $item['state'] !== 'complete') {
                        $checklistId = $item['idChecklist'];
                        $cardUrl = $checklistToCardUrl[$checklistId] ?? 'N/A';
                        
                        $customItem = [
                            'idMember' => $item['idMember'],
                            'due' => isset($item['due']) ? $item['due'] : null,
                            'state' => $item['state'],
                            'name' => isset($item['name']) ? $item['name'] : null,
                            'cardUrl' => $cardUrl,
                        ];
                        // add custom item into final array
                        $checklistItems[] = $customItem;
                    }
                }
            }
        }
    }
    echo "number of checklist items for you: ";
    var_dump(count($checklistItems));
    // return non-completed check items
    return $checklistItems;
}
?>