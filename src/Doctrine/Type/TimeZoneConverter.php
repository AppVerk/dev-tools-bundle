<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LogicException;

class TimeZoneConverter
{
    private static ?\DateTimeZone $dbTimeZone = null;

    private static ?\DateTimeZone $phpTimeZone = null;

    public static function dbTimeZone(): DateTimeZone
    {
        if (null === static::$dbTimeZone) {
            $name = defined('DATABASE_TIMEZONE') ? DATABASE_TIMEZONE : getenv('DATABASE_TIMEZONE');

            static::$dbTimeZone = is_string($name) ? new DateTimeZone($name) : static::phpTimeZone();
        }

        return static::$dbTimeZone;
    }

    public static function phpTimeZone(): DateTimeZone
    {
        if (null === static::$phpTimeZone) {
            static::$phpTimeZone = new DateTimeZone(date_default_timezone_get());
        }

        return static::$phpTimeZone;
    }

    /**
     * Converts a date time object to the database timezone.
     *
     * The new date time object will still point to the same
     * point in time, it will just have a different timezone
     * attached.
     *
     * When you string format the date however, the converted
     * date will have a different representation, because the
     * point in time looks different from a different
     * timezone.
     */
    public static function convertToDb(DateTimeInterface $dateTime): DateTimeInterface
    {
        if ($dateTime instanceof DateTimeImmutable) {
            return $dateTime->setTimezone(static::dbTimeZone());
        }

        if ($dateTime instanceof DateTime) {
            $asUtc = clone $dateTime;
            $asUtc->setTimezone(static::dbTimeZone());

            return $asUtc;
        }

        throw new LogicException('Unknown DateTimeInterface implementation: ' . get_class($dateTime));
    }
}
