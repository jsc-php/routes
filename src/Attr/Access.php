<?php

namespace JscPhp\Routes\Attr;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Access
{
    private bool $secure;

    public function __construct(bool $secure = true)
    {
        $this->secure = $secure;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }
}