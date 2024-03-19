<?php
require './getUsersCards.php';
require './getCheckListItems.php';
require './getAllBoards.php';

function trelloReminder($event) {
    foreach ($event["users"] as $user) {
        $userId = $user["id"];
        $boards = getAllBoards($userId);
        $cards = getUsersCards($boards, $userId);
        $checkListItemsNotDone = getCheckListItems($cards, $userId);
        foreach ($checkListItemsNotDone as $item) {
            echo $item . PHP_EOL; // print each checklist item
        }
    }
};

$event = [
    "users" => [
        [
            "id"=>"60bea1412afb06664eccd452",
        ]
    ]    
];

trelloReminder($event);

?>