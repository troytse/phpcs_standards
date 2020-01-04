<?php
define('CORRECT_CONST', 1);
define('INCORRECTCONST', 1);

define("CORRECT_CONST_2", 1);
define("INCORRECTCONST2", 1);

abstract class My_Class {

	const CORRECT_CONST_3 = 1;
	const INCORRECTCONST_3 = 1;

    public function __construct() {}
    public function My_Class() {}
    public function _My_Class() {}

	public $memberPropp=0;

	/**
	 *
	 * @param string $valuee
	 *
	 * @return void
	 */
    public function getSomeeValue($valuee) {}
    public function parseMyDSN() {}
    public function get_some_value() {}
    public function GetSomeValue() {}
    public function getSomeValue_Again() {}


    private function _getSomeValue() {}
    private function _get_some_value() {}


}//end class

function getSomeValue() {}
function parseMyDSN() {}
function get_some_value() {}


/* Test for magic functions */

class Magic_Test {
    function __construct() {}
    function __destruct() {}
    function __call($methodd, $a) {}
    function __callStatic($methodd, $a) {}
    function __get($a) {}
    function __set($a, $b) {}
    function __isset($a) {}
    function __unset($a) {}
    function __sleep() {}
    function __wakeup() {}
    function __toString() {}
    function __set_state() {}
    function __clone() {}
    function __autoload() {}
    function __invoke() {}
    function __myFunction() {}
    function __my_function() {}
}

function __construct() {}
function __destruct() {}
function __call() {}
function __callStatic() {}
function __get() {}
function __set() {}
function __isset() {}
function __unset() {}
function __sleep() {}
function __wakeup() {}
function __toString() {}
function __set_state() {}
function __clone() {}
function __autoload($a) {}
function __invoke() {}
function __myFunction() {}
function __my_function() {}

class Closure_Test {
    function test() {
        $foo = function() { echo 'foo'; };
    }
}

function test() {
    $foo = function() { echo 'foo'; };
}

/* @codingStandardsIgnoreStart */
class MyClass
{
    /* @codingStandardsIgnoreEnd */
    public function __construct() {}
}

interface ooInterface
{
    function __call($a, $b);
}

trait Foo
{
    function __call($a, $b) {}
}

class Magic_Case_Test {
    function __Construct() {}
    function __isSet($a) {}
    function __tostring() {}
}

class Foo2 extends \SoapClient
{
    public function __soapCall(
        $functionName,
        $arguments,
        $options = array(),
        $inputHeaders = null,
        &$outputHeaders = array()
    ) {
        // body
    }
}

function __debugInfo() {}
class Foo {
    function __debugInfo() {}
}

function ___tripleUnderscore() {} // Ok.

class triple {
    public function ___tripleUnderscore() {} // Ok.
}

/* Magic methods in anonymous classes. */
/* $a = new class { */
/*     function __construct() {} */
/*     function __destruct() {} */
/*     function __call() {} */
/*     function __callStatic() {} */
/*     function __get() {} */
/*     function __set() {} */
/*     function __isset() {} */
/*     function __unset() {} */
/*     function __sleep() {} */
/*     function __wakeup() {} */
/*     function __toString() {} */
/*     function __set_state() {} */
/*     function __clone() {} */
/*     function __autoload() {} */
/*     function __invoke() {} */
/*     function __myFunction() {} */
/*     function __my_function() {} */
/*     function __call() {} */
/* } */
