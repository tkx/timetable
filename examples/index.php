<?php
require("../vendor/autoload.php");

use Moteam\TimeTable\TimeTable;

$tt = TimeTable::fromJson(file_get_contents("config.json"));
print_r([
    "isRunning" => $tt->isRunning("testEvent"),
    "timePercent" => $tt->timePercent("testEvent"),
    "timeToStart" => $tt->timeToStart("testEvent"),
    "timeToOpen" => $tt->timeToOpen("testEvent"),
    "timeToEnd" => $tt->timeToEnd("testEvent"),
    "timeStarted" => $tt->timeStarted("testEvent"),
    "isOpen" => $tt->isOpen("testEvent"),
    "getVersion" => $tt->getVersion("testEvent"),
    "getParams" => $tt->getParams("testEvent"),
]);

$tt = TimeTable::fromArray([
    [
        "event" => "testEvent",
        "date" => date("Y-m-d H:i:s"),
        "days" => 1,
    ]
]);
print_r([
    "isRunning" => $tt->isRunning("testEvent"),
    "timePercent" => $tt->timePercent("testEvent"),
    "timeToStart" => $tt->timeToStart("testEvent"),
    "timeToOpen" => $tt->timeToOpen("testEvent"),
    "timeToEnd" => $tt->timeToEnd("testEvent"),
    "timeStarted" => $tt->timeStarted("testEvent"),
    "isOpen" => $tt->isOpen("testEvent"),
    "getVersion" => $tt->getVersion("testEvent"),
    "getParams" => $tt->getParams("testEvent"),
]);