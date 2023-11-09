<?php
/**
 * Created for plugin-component-logistic
 * Date: 10.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Logistic\Waybill;

use SalesRender\Plugin\Components\Logistic\Exceptions\LogisticTrackException;
use PHPUnit\Framework\TestCase;

class TrackTest extends TestCase
{

    /**
     * @dataProvider validTrackDataProvider
     * @param string $number
     * @throws LogisticTrackException
     */
    public function testConstructValidTrack(string $number): void
    {
        $track = new Track($number);
        $this->assertSame(trim($number), $track->get());
        $this->assertSame(trim($number), (string) $track);
    }

    /**
     * @dataProvider invalidTrackDataProvider
     * @param string $track
     */
    public function testConstructInvalidTrack(string $track): void
    {
        $this->expectException(LogisticTrackException::class);
        new Track($track);
    }

    public function validTrackDataProvider(): array
    {
        return [
            ['123456'],
            [str_repeat('1', 25)],
            ['abc_ABC-123'],
            [' abc_ABC-123 '],
        ];
    }

    public function invalidTrackDataProvider(): array
    {
        return [
            ['      '],
            ['русскиебуквы'],
            ['12345'],
            [' 12345 '],
            ['******'],
            [str_repeat('1', 26)],
        ];
    }

}
