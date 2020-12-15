<?php
/**
 * Created for plugin-component-logistic
 * Date: 09.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Logistic;


use JsonSerializable;
use Leadvertex\Plugin\Components\Logistic\Exceptions\LogisticTrackException;

class LogisticTrack implements JsonSerializable
{

    protected string $track;

    /**
     * LogisticTrack constructor.
     * @param string $track
     * @throws LogisticTrackException
     */
    public function __construct(string $track)
    {
        $track = trim($track);
        if (!preg_match('~^[a-z\d\-_]{6,25}$~ui', $track)) {
            throw new LogisticTrackException("Track length should be between 6 and 25 chars, and contain only A-Z, 0-9, dash and underscore");
        }
        $this->track = $track;
    }

    public function get(): string
    {
        return $this->track;
    }

    public function __toString(): string
    {
        return $this->track;
    }

    public function jsonSerialize(): string
    {
        return $this->track;
    }
}