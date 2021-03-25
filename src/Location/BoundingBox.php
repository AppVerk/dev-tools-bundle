<?php

declare(strict_types = 1);

namespace DevTools\Location;

class BoundingBox implements PositionInterface
{
    private const METERS = 0.0000089;

    private Point $leftBottom;

    private Point $rightTop;

    private Point $rightBottom;

    private Point $leftTop;

    public function __construct(Point $leftBottom, Point $rigthTop)
    {
        $this->leftBottom = $leftBottom;
        $this->rightTop = $rigthTop;
        $this->rightBottom = new Point($this->rightTop->getLongitude(), $this->leftBottom->getLatitude());
        $this->leftTop = new Point($this->leftBottom->getLongitude(), $this->rightTop->getLatitude());
    }

    public function getLeftBottom(): Point
    {
        return $this->leftBottom;
    }

    public function getRightTop(): Point
    {
        return $this->rightTop;
    }

    public function getRightBottom(): Point
    {
        return $this->rightBottom;
    }

    public function getLeftTop(): Point
    {
        return $this->leftTop;
    }

    public function increase(int $meters): self
    {
        $distance = $meters * self::METERS;

        $lbLat = $this->leftBottom->getLatitude() - $distance;
        $lbLong = $this->leftBottom->getLongitude() - $distance;

        $trLat = $this->rightTop->getLatitude() + $distance;
        $trLong = $this->rightTop->getLongitude() + $distance;

        return new self(new Point($lbLong, $lbLat), new Point($trLong, $trLat));
    }
}
