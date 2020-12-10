<?php
/**
 * Created for plugin-component-logistic
 * Date: 11.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic\Components;


use JsonSerializable;
use Leadvertex\Plugin\Components\Logistic\Exceptions\OpeningHoursException;
use Spatie\OpeningHours\Exceptions\Exception;

class OpeningHours implements JsonSerializable
{

    private array $schedule = [];

    /**
     * OpeningHours constructor.
     * @param array $schedule
     * @throws OpeningHoursException
     */
    public function __construct(array $schedule)
    {
        $schedule = array_map(fn($value) => is_null($value) ? [] : $value, $schedule);
        unset($schedule['exceptions']);

        try {
            \Spatie\OpeningHours\OpeningHours::create($schedule); //for validation only
        } catch (Exception $exception) {
            throw new OpeningHoursException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $this->schedule = $schedule;
    }

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function jsonSerialize(): array
    {
        return $this->schedule;
    }
}