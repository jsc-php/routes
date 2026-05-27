<?php

namespace JscPhp\Routes\Attr;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Access
{
    const  ACCESS_ALL       = 'all';
    const  ACCESS_PROTECTED = 'protected';
    const  ACCESS_ADMIN     = 'admin';
    
    private string $access;

    public function __construct(string $access = self::ACCESS_PROTECTED)
    {
        $this->access = $access;
    }

    public function getAccess(): string
    {
        return $this->access;
    }
}