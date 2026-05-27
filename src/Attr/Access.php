<?php

namespace JscPhp\Routes\Attr;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Access
{
    const ACCESS_ALL       = 'all';
    const ACCESS_PUBLIC    = 'public';
    const ACCESS_PROTECTED = 'protected';
    private self $secure;

    public function __construct(self|string $secure = self::ACCESS_PROTECTED)
    {
        $this->secure = $secure;
    }

    public function isSecure(): string
    {
        return $this->secure;
    }
}