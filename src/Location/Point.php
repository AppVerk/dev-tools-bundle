<?php

declare(strict_types = 1);

namespace DevTools\Location;

class Point implements PositionInterface
{
    private const PRECISION = 6;

    private float $longitude;

    private float $latitude;

    public function __construct(float $longitude, float $latitude)
    {
        if (abs($longitude) > 180) {
            throw new \InvalidArgumentException('Invalid longitude value');
        }

        if (abs($latitude) > 90) {
            throw new \InvalidArgumentException('Invalid latitude value');
        }

        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public static function zero(): self
    {
        return new self(0, 0);
    }

    public function addNoise(float $longitudeNoise, float $latitudeNoise): self
    {
        $longitude = round($this->longitude + $longitudeNoise, self::PRECISION);
        $latitude = round($this->latitude + $latitudeNoise, self::PRECISION);

        return new self($longitude, $latitude);
    }
}
