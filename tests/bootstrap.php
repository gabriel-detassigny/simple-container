<?php

namespace GabrielDeTassigny\SimpleContainer\Tests\Reflection {
    class NoConstructor {}

    class ConstructorNoParam {
        public function __construct() {}
    }

    class ConstructorWithClassParam {
        public function __construct(\stdClass $param) {}
    }
}