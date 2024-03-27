<?php
require './getUsersCards.php';
require './getCheckListItems.php';
require './getAllBoards.php';

function trelloReminder($event) {
    foreach ($event["users"] as $user) {
        $userId = $user["id"];
        $boards = getAllBoards($userId);
        $cards = getUsersCards($boards);
        $userCheckListItems = getCheckListItems($cards, $userId);
        foreach($userCheckListItems as $checkListItem){
            print_r($checkListItem);
        };
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