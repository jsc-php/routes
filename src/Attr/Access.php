<?php

namespace JscPhp\Routes\Attr;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Access {
    private bool $protected;

    public function __construct(bool $protected = true) {
        $this->protected = $protected;
    }

    public function isProtected(): bool {
        if (empty($this->protected)) {
            return false;
        }
        return $this->protected;
    }
}