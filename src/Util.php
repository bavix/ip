<?php

namespace Bavix\IP;

class Util
{

    /**
     * @var int
     */
    public const IPv4_INT_MAX = 4294967295;

    /**
     * @param string $ip
     *
     * @return bool
     */
    public static function isIPv6(string $ip): bool
    {
        return (bool)\inet_ntop($ip);
    }

    /**
     * @param string $ip
     *
     * @return bool
     */
    public static function isIPv4(string $ip): bool
    {
        return (bool)\ip2long($ip);
    }

    /**
     * @param string $ip
     * @return string
     */
    public static function ip2long(string $ip): string
    {
        return \gmp_import(\inet_pton($ip));
    }

    /**
     * @param string $long
     * @param bool $forceIPv6
     * @return string
     */
    public static function long2ip(string $long, bool $forceIPv6 = false): string
    {
        /**
         * @var \GMP $long
         */
        if ($forceIPv6 || \gmp_cmp($long, static::IPv4_INT_MAX) > 0) {
            return (string)\inet_ntop(
                \str_pad(
                    \gmp_export($long),
                    16,
                    "\0",
                    \STR_PAD_LEFT
                )
            );
        }

        return \long2ip($long);
    }

}
