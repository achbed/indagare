<?php
include 'user.php';
include 'db.php';

if(!indagare\users\User::hasUserSession()) {
    die('not authorized');
}

$task;
if (isset($_GET["task"])) {
    $task = $_GET["task"];
} 
else {
    die("no task identified");
}

$user = \indagare\users\User::getUserBySession();
if (!$user instanceof \indagare\users\User) {
    die("no user");
}

if ($task == "itineraries") {
    $itineraries = \indagare\db\CrmDB::getItineraries($user->getID());
}

else if ($task == "itinerarieItems") {
    $items = \indagare\db\CrmDB::getItineraryItems($_POST["itineraryID"]);
}


