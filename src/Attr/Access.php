<?php

namespace JscPhp\Routes\Attr;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Access
{
    const string ACCESS_ALL       = 'all';
    const string ACCESS_PUBLIC    = 'public';
    const string ACCESS_PROTECTED = 'protected';
    private self $access;

    public function __construct(self|string $access = self::ACCESS_PROTECTED)
    {
        $this->access = $access;
    }

    public function getAccess(): string
    {
        return $this->access;
    }
}