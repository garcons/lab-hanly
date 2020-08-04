<?php

namespace Tests\Unit\Contracts;

use App\Contracts\Distance;
use Tests\TestCase;

class DistanceTest extends TestCase
{
    /**
     * @test
     */
    public function canBeFriends_友達になる人が存在する()
    {
        $myPoint = [
            'friends_id' => 123,
            'latitude' => 33.00001,
            'longitude' => 130.00001,
        ];
        $friendPoints = [
            [
                'friends_id' => 111, //友達になる
                'latitude' => 33.00001,
                'longitude' => 130.00001,
            ],
            [
                'friends_id' => 222, //友達になれない
                'latitude' => 33.00070,
                'longitude' => 130.00070,
            ],
            [
                'friends_id' => 333, //友達になる
                'latitude' => 33.00069,
                'longitude' => 130.00069,
            ]
        ];

        $sut = new Distance();

        $actual = $sut->canBeFriends($myPoint, $friendPoints);

        $this->assertSame($actual, [
            111,
            333,
        ]);
    }

    /**
     * @test
     */
    public function canBeFriends_友達になる人が存在しない()
    {
        $myPoint = [
            'friends_id' => 123,
            'latitude' => 33.00001,
            'longitude' => 130.00001,
        ];
        $friendPoints = [
            [
                'friends_id' => 111, //友達になれない
                'latitude' => 34.00001,
                'longitude' => 131.00001,
            ],
            [
                'friends_id' => 222, //友達になれない
                'latitude' => 33.00070,
                'longitude' => 130.00070,
            ],
        ];

        $sut = new Distance();

        $actual = $sut->canBeFriends($myPoint, $friendPoints);

        $this->assertSame($actual, []);
    }

    /**
     * @test
     */
    public function canBeFriends_友達になる対象の人がいない()
    {
        $myPoint = [
            'friends_id' => 123,
            'latitude' => 33.00001,
            'longitude' => 130.00001,
        ];
        $friendPoints = [];

        $sut = new Distance();

        $actual = $sut->canBeFriends($myPoint, $friendPoints);

        $this->assertSame($actual, []);
    }

    /**
     * @test
     */
    public function isNear_距離の判定_NG()
    {
        $point1 = [
            'latitude' => 33.00001,
            'longitude' => 130.00001,
        ];
        $point2 = [
            'latitude' => 33.00070,
            'longitude' => 130.00070,
        ];

        $sut = new Distance();

        $actual = $sut->isNear($point1, $point2);

        $this->assertFalse($actual);
    }

    /**
     * @test
     */
    public function isNear_距離の判定_OK()
    {
        $point1 = [
            'latitude' => 33.00001,
            'longitude' => 130.00001,
        ];
        $point2 = [
            'latitude' => 33.00069,
            'longitude' => 130.00069,
        ];

        $sut = new Distance();

        $actual = $sut->isNear($point1, $point2);

        $this->assertTrue($actual);
    }
}
