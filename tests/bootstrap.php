<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\Reflection {
    class NoConstructor {}

    class ConstructorNoParam {
        public function __construct() {}
    }

    class ConstructorWithClassParam {
        public function __construct(\stdClass $param) {}
    }

    class ConstructorWithDefaultParam {
        public function __construct(?string $test = null) {}
    }

    class ConstructorWithPrimitiveParam {
        public function __construct(string $test) {}
    }
}