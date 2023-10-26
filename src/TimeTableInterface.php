<?php declare(strict_types= 1);

namespace Moteam\TimeTable;

interface TimeTableInterface {
    /**
     * @param string $name
     * @param int|null $time
     * @return int|null
     */
    function timeToStart(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return int|null
     */
    function timeToOpen(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return int|null
     */
    function timeToEnd(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return float|null
     */
    function timePercent(string $name, $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return bool
     */
    function isRunning(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return int|null
     */
    function timeStarted(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return bool
     */
    function isOpen(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return string|int|null
     */
    function getVersion(string $name, int $time = null);
    /**
     * @param string $name
     * @param int|null $time
     * @return array|null
     */
    function getParams(string $name, int $time = null);
}