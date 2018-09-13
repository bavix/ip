<?php

namespace Bavix\IP;

class RangeUtil
{

    /**
     * @var array
     */
    protected $checked = [];

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var string
     */
    protected $lastIP;

    /**
     * @return RangeUtil
     */
    public static function sharedInstance(): self
    {
        static $object;

        if (!$object) {
            $object = new static();
        }

        return $object;
    }

    /**
     * @param string $ip
     * @param string $subnet
     * @return bool
     */
    protected function has(string $ip, string $subnet): bool
    {
        return isset($this->checked[$subnet][$ip]);
    }

    /**
     * @param string $ip
     * @param string $subnet
     * @return bool
     */
    protected function get(string $ip, string $subnet): bool
    {
        return $this->results[$subnet][$ip];
    }

    /**
     * @param string $ip
     * @param string $subnet
     * @param bool $value
     * @return bool
     */
    protected function set(string $ip, string $subnet, bool $value): bool
    {
        $this->checked[$subnet][$ip] = true;
        $this->results[$subnet][$ip] = $value;
        return $value;
    }

    /**
     * @param string $subnet
     * @param int $max
     *
     * @return int
     */
    protected function netMask(string $subnet, int $max): int
    {
        $netMask = $max;
        $parts = \explode('/', $subnet, 2);

        if (\count($parts) === 2) {
            $netMask = (int)\array_pop($parts);
        }

        $this->lastIP = \array_pop($parts);
        return \max(1, \min($max, $netMask));
    }

    /**
     * @param string $ip
     * @param string $subnet
     *
     * @return bool
     */
    protected function ipv4(string $ip, string $subnet): bool
    {
        $max = 32;
        $netMask = $this->netMask($subnet, $max);
        $ipSubnet = $this->lastIP;
        $networkLong = \ip2long($ipSubnet);

        $mask = 0xffffffff << ($max - $netMask);
        $ipLong = \ip2long($ip);

        return ($ipLong & $mask) === ($networkLong & $mask);
    }

    /**
     * @param string $ip
     * @param string $subnet
     *
     * @return bool
     */
    protected function ipv6(string $ip, string $subnet): bool
    {
        $max = 128;
        $netMask = $this->netMask($subnet, $max);
        $ipSubnet = $this->lastIP;

        $bytesSubnet = \unpack('n*', @\inet_pton($ipSubnet));
        $bytesAddress = \unpack('n*', @\inet_pton($ip));

        if (empty($bytesSubnet) || empty($bytesAddress)) {
            return false;
        }

        for ($i = 1, $ceil = \ceil($netMask / 16); $i <= $ceil; ++$i) {
            $left = $netMask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xffff >> $left) & 0xffff;

            if (($bytesSubnet[$i] & $mask) !== ($bytesAddress[$i] & $mask)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $ip
     * @param string $subnet
     * @return bool
     */
    public function check(string $ip, string $subnet): bool
    {
        if ($this->has($ip, $subnet)) {
            return $this->get($ip, $subnet);
        }

        if (\strpos($ip, '.')) {
            return $this->set(
                $ip,
                $subnet,
                $this->ipv4($ip, $subnet)
            );
        }

        return $this->set(
            $ip,
            $subnet,
            $this->ipv6($ip, $subnet)
        );
    }

}
