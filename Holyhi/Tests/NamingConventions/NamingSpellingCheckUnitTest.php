<?php
/**
 * Unit test class for Holyhi Coding Standard.
 *
 * @link    https://github.com/troytse/php_standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace HolyhiCS\Holyhi\Tests\NamingConventions;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

/**
 * Unit test class for the NamingSpellingCheck sniff.
 */
class NamingSpellingCheckUnitTest extends AbstractSniffUnitTest {

    /**
     */

    /**
     * Returns the lines where errors should occur.
     *
     * @return array <int line number> => <int number of errors>
     */
    public function getErrorList()
    {
        return array();
    }

    /**
     * Returns the lines where warnings should occur.
     *
     * @return array <int line number> => <int number of warnings>
     */
    public function getWarningList()
    {
        return array(
            2   => 1,
            3   => 1,
            5   => 1,
            6   => 1,
            10  => 1,
            11  => 1,
            17  => 1,
            25  => 2,
            48  => 1,
            49  => 1,
            85  => 1,
            90  => 1,
            100 => 1,
            105 => 1,
            116 => 1,
            130 => 1,
        );
    }
}
