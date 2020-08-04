<?php
declare(strict_types=1);

namespace App\Contracts;

/**
 * ヒュベニの公式に基づき緯度経度で与えられた２点間の距離を計算する
 * @see https://palmgate.co.jp/masato_kato/article.html&id=110
 */
class Distance
{
    private const EQUATOR_RADIUS = 6378137.000000;   // WGS84 楕円体モデルの地球赤道半径 (長半径) (m)
    private const EARTH_RADIUS = 6356752.314245; // WGS84 楕円体モデルの地球極半径 (短半径) (m)
    private const DISTANCE_JUDGMENT_METERS = 100; // 近さ判定の閾値(m)

    /**
     * 友達になれるかもしれない人を検索
     * @param array $myPin
     * @param array $notFriends
     * @return array
     */
    public function canBeFriends(array $myPin, array $notFriens): array
    {
        $newFriendIds = [];
        foreach ($notFriens as $friend) {
            $point = [
                'latitude' => $friend['latitude'],
                'longitude' => $friend['longitude'],
            ];

            if ($this->isNear($myPin, $point)) {
                $newFriendIds[] = $friend['friends_id'];
            }
        }

        return $newFriendIds;
    }

    /**
     * 近さの判定
     * @param $point1
     * @param $point2
     * @return boolean
     */
    public function isNear(array $point1, array $point2): bool
    {
        $dist = $this->hyubenyDistance(
            $point1['latitude'],
            $point1['longitude'],
            $point2['latitude'],
            $point2['longitude']
        );

        return $dist <= self::DISTANCE_JUDGMENT_METERS;
    }

    /**
     * ヒュベニ公式を用いて２点間の距離を算出
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function hyubenyDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $e2 = (pow(self::EQUATOR_RADIUS, 2) - pow(self::EARTH_RADIUS, 2))/pow(self::EQUATOR_RADIUS, 2);  // 第一離心率の二乗値
        $x1 = deg2rad($lon1);   // 経度 ⇒ ラジアン変換
        $y1 = deg2rad($lat1);   // 緯度 ⇒ ラジアン変換
        $x2 = deg2rad($lon2);   // 経度 ⇒ ラジアン変換
        $y2 = deg2rad($lat2);   // 緯度 ⇒ ラジアン変換

        $dy = $y1 - $y2;    // 緯度の差
        $dx = $x1 - $x2;    // 経度の差

        $mu_y = ($y1 + $y2) / 2.0;  // 緯度の平均値

        $W = sqrt(1.0 - ($e2 * pow(sin($mu_y), 2)));

        $N = self::EQUATOR_RADIUS / $W;  // 卯酉線曲率半径
        $M = (self::EQUATOR_RADIUS * (1 - $e2)) / pow($W, 3);    // 子午線曲率半径

        return sqrt(pow($dy * $M, 2) + pow($dx*$N*cos($mu_y), 2));  // ２点間の距離 (m);
    }
}
