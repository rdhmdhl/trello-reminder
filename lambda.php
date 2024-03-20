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
            "id"=>"6499cadd83f4b09a42ba0fcf",
        ]
    ]    
];

trelloReminder($event);

?>