<?php

namespace Bavix\IP;

class IP
{

    /**
     * @var string
     */
    protected $ip;

    /**
     * Ip constructor.
     * @param string $ip
     */
    public function __construct(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @param string $long
     * @return Ip
     */
    public static function fromLong(string $long): self
    {
        return new static(Util::long2ip($long));
    }

    /**
     * @return string
     */
    public function getLong(): string
    {
        return Util::ip2long($this->ip);
    }

    /**
     * @return string
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * @return bool
     */
    public function isIPv4(): bool
    {
        return Util::isIPv4($this->ip);
    }

    /**
     * @return bool
     */
    public function isIPv6(): bool
    {
        return Util::isIPv6($this->ip);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->ip;
    }

    /**
     * @param string $subnet
     * @return bool
     */
    public function checkInRange(string $subnet): bool
    {
        return RangeUtil::sharedInstance()
            ->check($this->ip, $subnet);
    }

}
