<?php declare(strict_types=1);

namespace Moteam\TimeTable;

class BasicTest extends \PHPUnit\Framework\TestCase {
    public function testArray() {
        $now = time();
        $tt = TimeTable::fromArray([
            [
                "event" => "testEvent",
                "date" => date("Y-m-d H:i:s", $now),
                "days" => 1,
            ]
        ]);
        $this->assertTrue($tt->isRunning("testEvent"));
        $this->assertTrue($tt->timeToStart("testEvent") == 0);
        $this->assertTrue($tt->timeStarted("testEvent") == $now);
        $this->assertTrue($tt->timeToEnd("testEvent", $now) == 24 * 3600);
        $this->assertTrue($tt->timePercent("testEvent", $now) == 0);
        $this->assertTrue($tt->timePercent("testEvent", $now + 24 * 3600 / 2) == 0.5);
    }

    public function testJson() {
        $now = time();
        $tt = TimeTable::fromJson(json_encode([
            [
                "event" => "testEvent",
                "date" => date("Y-m-d H:i:s", $now),
                "open" => date("Y-m-d H:i:s", $now),
                "open_days" => 0.5,
                "days" => 1,
                "params" => [
                    "testParam1" => 1,
                    "testParam2" => "xxx",
                    "testParam3" => [1, 2, 3],
                ],
            ]
        ]));
        $this->assertTrue($tt->isRunning("testEvent"));
        $this->assertTrue($tt->timeToStart("testEvent") == 0);
        $this->assertTrue($tt->timeStarted("testEvent") == $now);
        $this->assertTrue($tt->timeToEnd("testEvent", $now) == 24 * 3600);
        $this->assertTrue($tt->timePercent("testEvent", $now) == 0);
        $this->assertTrue($tt->timePercent("testEvent", $now + 24 * 3600 / 2) == 0.5);
        $this->assertTrue($tt->isOpen("testEvent"));
        $this->assertFalse($tt->isOpen("testEvent", $now + 24 * 3600 / 2 + 1));
        $this->assertEquals($tt->getParams("testEvent")["testParam1"], 1);
        $this->assertEquals($tt->getParams("testEvent")["testParam2"], "xxx");
        $this->assertSame($tt->getParams("testEvent")["testParam3"], [1, 2, 3]);
    }

    public function testNoEvent() {
        $now = time();
        $tt = TimeTable::fromArray([["event" => "testEvent", "date" => date("Y-m-d H:i:s", $now), "days" => 1,]]);
        $this->assertFalse($tt->isRunning("noEvent"));
    }
    
    public function testWrongKey() {
        $this->expectException(\UnexpectedValueException::class);
        $now = time();
        $tt = TimeTable::fromArray([["someKey" => 1, "event" => "testEvent", "date" => date("Y-m-d H:i:s", $now), "days" => 1,]]);
    }
    
    public function testWrongValue() {
        $this->expectException(\InvalidArgumentException::class);
        $now = time();
        $tt = TimeTable::fromArray([["event" => "testEvent", "date" => date("Y-m-d H:i:s", $now), "days" => "xxx",]]);
    }
    
    public function testMissingRequiredKey() {
        $this->expectException(\InvalidArgumentException::class);
        $now = time();
        $tt = TimeTable::fromArray([["date" => date("Y-m-d H:i:s", $now), "days" => 1,]]);
    }
}