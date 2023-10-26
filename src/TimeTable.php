<?php declare(strict_types=1);

namespace Moteam\TimeTable;

/**
 * Represents a timetable - set anything enabled/disabled on certain dates
 */
class TimeTable implements TimeTableInterface {
    /**
     * Entries list
     * @var array
     */
    protected ?array $entries = null;

    /**
     * @var array $entries
     */
    protected function __construct(array $entries) {
        $this->validateEntries($entries);
        $this->entries = $entries;
    }
    
    /**
     * @param array $entries
     */
    protected function validateEntries(array $entries) {
        $is_date = function($date) {
            $format = "Y-m-d H:i:s";
            $d = \DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        };
        $is_days = function($days) {
            return \is_numeric($days) && $days > 0;
        };

        $required_keys = [
            "event" => "is_string", 
            "date" => $is_date,
            "days" => $is_days,
        ];
        $optional_keys = [
            "open" => $is_date,
            "open_days" => $is_days,
            "version" => "is_scalar",
            "params" => "is_array",
        ];

        foreach($entries as $entry) {
            foreach($required_keys as $key => $validator) {
                if(!\array_key_exists($key, $entry)) {
                    throw new \InvalidArgumentException("Required key '{$key}'");
                }
            }
            foreach($entry as $key => $value) {
                if(!\in_array($key, \array_keys($required_keys)) && !\in_array($key, \array_keys($optional_keys))) {
                    throw new \UnexpectedValueException("Unexpected key '{$key}'");
                }
                $validator = \in_array($key, \array_keys($required_keys)) ? $required_keys[$key] : $optional_keys[$key];
                if(\call_user_func($validator, $value) !== true) {
                    throw new \InvalidArgumentException("Wrong value for key '{$key}' = '{$value}'");
                }
            }
        }
    }

    /**
     * @param string $json Valid JSON string
     */
    public static function fromJsonString(string $json): self {
        $decoded = \json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if($decoded === null) {
            throw new \JsonException();
        }
        return new self($decoded);
    }

    /**
     * @param array $data
     */
    public static function fromArray(array $data): self {
        return new self($data);
    }

    /**
     * @inheritDoc
     */
    public function timeToStart(string $name, int $time = null)
    {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date = \strtotime($e["date"]);
                if($now < $date) {
                    $delta = $date - $now;
                    return $delta;
                }
            }
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function timeToOpen(string $name, int $time = null)
    {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name && \array_key_exists("open", $e) && $e["open"]) {
                $date = \strtotime($e["open"]);
                if($now < $date) {
                    $delta = $date - $now;
                    return $delta;
                }
            }
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function timeToEnd(string $name, int $time = null)
    {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date = \strtotime($e["date"]) + $e["days"] * 3600 * 24;
                if($now < $date) {
                    $delta = $date - $now;
                    return $delta;
                }
            }
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function timePercent(string $name, $time = null) {
        if($this->isRunning($name, $time)) {
            $now = $time ?? \time();
            foreach($this->entries as $e) {
                if($e["event"] == $name) {
                    $date1 = \strtotime($e["date"]);
                    $date2 = $date1 + $e["days"] * 3600 * 24;
                    if($date1 <= $now && $now <= $date2) {
                        return ($now - $date1) / ($date2 - $date1);
                    }
                }
            }
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function isRunning(string $name, int $time = null) {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date1 = \strtotime($e["date"]);
                $date2 = $date1 + $e["days"] * 3600 * 24;
                if($date1 <= $now && $now <= $date2) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @inheritDoc
     */
    public function timeStarted(string $name, int $time = null) {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date1 = \strtotime($e["date"]);
                $date2 = $date1 + $e["days"] * 3600 * 24;
                if($date1 <= $now && $now <= $date2) {
                    return $date1;
                }
            }
        }
        return false;
    }
    /**
     * @inheritDoc
     */
    public function isOpen(string $name, int $time = null) {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name && \array_key_exists("open", $e) && \array_key_exists("open_days", $e) && $e["open"]) {
                $date1 = \strtotime($e["open"]);
                $date2 = $date1 + $e["open_days"] * 3600 * 24;
                if($date1 <= $now && $now <= $date2) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @inheritDoc
     */
    public function getVersion(string $name, int $time = null) {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date1 = \strtotime($e["date"]);
                $date2 = $date1 + $e["days"] * 3600 * 24;
                if($date1 <= $now && $now <= $date2) {
                    if(\array_key_exists("version", $e)) {
                        return $e["version"];
                    }
                }
            }
        }
        return null;
    }
    /**
     * @inheritDoc
     */
    public function getParams(string $name, int $time = null) {
        $now = $time ?? \time();
        foreach($this->entries as $e) {
            if($e["event"] == $name) {
                $date1 = \strtotime($e["date"]);
                $date2 = $date1 + $e["days"] * 3600 * 24;
                if($date1 <= $now && $now <= $date2) {
                    if(\array_key_exists("params", $e)) {
                        return $e["params"];
                    }
                }
            }
        }
        return null;
    }
}
