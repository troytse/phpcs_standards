<?php
/**
 * Detect the naming spelling are correctly.
 *
 * @author    Troy Tse <troy@holyhi.net>
 * @copyright 2019 Holyhi.Net
 * @license   https://github.com/troytse/phpcs_standards/blob/master/licence.txt MIT Licence
 */

namespace PHP_CodeSniffer\Standards\Holyhi\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Exceptions\RuntimeException;

class NamingSpellingCheckSniff implements Sniff
{
    /**
     * Custom excluded words.
     *
     * @var array
     */
    public $excludes = [];

    /**
     * Custom included words.
     *
     * @var array
     */
    public $includes = [];

    /**
     * The dictionary for spelling check.
     *
     * @var array
     */
    protected $dictionary;

    /**
     * A list of all PHP magic methods.
     *
     * @var array
     */
    protected $magicMethods = [
        'construct'  => true,
        'destruct'   => true,
        'call'       => true,
        'callstatic' => true,
        'get'        => true,
        'set'        => true,
        'isset'      => true,
        'unset'      => true,
        'sleep'      => true,
        'wakeup'     => true,
        'tostring'   => true,
        'set_state'  => true,
        'clone'      => true,
        'invoke'     => true,
        'debuginfo'  => true,
        'autoload'   => true,
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $this->initializeDictionary();
        return [
            T_STRING,
            T_FUNCTION,
            T_VARIABLE,
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_DOUBLE_QUOTED_STRING,
            T_HEREDOC,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens     = $phpcsFile->getTokens();
        $name       = $tokens[$stackPtr]['content'];
        $ignoreCase = false;

        switch ($tokens[$stackPtr]['code']) {
            case T_FUNCTION:
            case T_CLASS:
            case T_INTERFACE:
            case T_TRAIT:
                $name = $phpcsFile->getDeclarationName($stackPtr);
                if ($tokens[$stackPtr]['code'] === T_FUNCTION
                && $name{0} . $name{1} == '__'
                && isset($this->magicMethods[substr($name, 2)])
                ) {
                    // skip magic methods
                    return;
                }
            break;

            case T_VARIABLE:
                $name = $tokens[$stackPtr]['content'];
            break;

            case T_STRING:
                $name       = $this->parseConstant($phpcsFile, $stackPtr);
                $ignoreCase = true;
            break;

            default:
                var_dump($tokens[$stackPtr]);
            break;
        }//end switch

        if (!empty($name)) {
            $incorrectWords = $this->spellingCheck($name, $ignoreCase);
            $error         = 'Bad spelling: "%s"';
            foreach ($incorrectWords as $word) {
                $phpcsFile->addWarning($error, $stackPtr, 'BadSpelling', [$word]);
            }
        }//end if

    }//end process()


    /**
     * Process constant name
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return string
     */
    protected function parseConstant(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $name   = $tokens[$stackPtr]['content'];

        // If this token is in a heredoc, ignore it.
        if ($phpcsFile->hasCondition($stackPtr, T_START_HEREDOC) === true) {
            return '';
        }

        // Special case for PHP 5.5 class name resolution.
        if (strtolower($name) === 'class' && $tokens[($stackPtr - 1)]['code'] === T_DOUBLE_COLON) {
            return '';
        }

        // Special case for PHPUnit.
        if ($name === 'PHPUnit_MAIN_METHOD') {
            return '';
        }

        // If the next non-whitespace token after this token
        // is not an opening parenthesis then it is not a function call.
        for ($openBracket = ($stackPtr + 1); $openBracket < $phpcsFile->numTokens; $openBracket++) {
            if ($tokens[$openBracket]['code'] !== T_WHITESPACE) {
                break;
            }
        }

        if ($openBracket === $phpcsFile->numTokens) {
            return '';
        }

        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            $findTokens = [
                T_WHITESPACE,
                T_COMMA,
                T_COMMENT,
                T_STRING,
                T_NS_SEPARATOR
            ];
            $functionKeyword = $phpcsFile->findPrevious($findTokens, ($stackPtr - 1), null, true);

            if ($tokens[$functionKeyword]['code'] !== T_CONST) {
                return '';
            }

            // This is a class constant.
            return $name;
        }//end if

        if (strtolower($name) !== 'define') {
            return '';
        }

        /*
            This may be a "define" function call.
         */

        // Make sure this is not a method call.
        $previous = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$previous]['code'] === T_OBJECT_OPERATOR
            || $tokens[$previous]['code'] === T_DOUBLE_COLON
        ) {
            return '';
        }

        // The next non-whitespace token must be the constant name.
        $constPtr = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);
        if ($tokens[$constPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING) {
            return '';
        }

        $name = $tokens[$constPtr]['content'];

        // Check for constants like self::CONSTANT.
        $prefix   = '';
        $splitPos = strpos($name, '::');
        if ($splitPos !== false) {
            $prefix = substr($name, 0, ($splitPos + 2));
            $name   = substr($name, ($splitPos + 2));
        }

        // Strip namesspace from constant like /foo/bar/CONSTANT.
        $splitPos = strrpos($name, '\\');
        if ($splitPos !== false) {
            $prefix = substr($name, 0, ($splitPos + 1));
            $name   = substr($name, ($splitPos + 1));
        }

        return $name;

    }//end parseConstant()


    /**
     * Initialize the dictionary.
     *
     * @return void
     */
    protected function initializeDictionary()
    {
        // Load the dictionary file.
        $path = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'Dicts', 'en.dat']));
        if (is_file($path) && is_readable($path)) {
            $this->dictionary = unserialize(file_get_contents($path));
        } else {
            $this->dictionary = [];
        }

        // includes
        foreach ($this->includes as $word) {
            $this->dictionary[$word] = 0;
        }

        // excludes
        foreach ($this->excludes as $word) {
            if (isset($this->dictionary[$word])) {
                $this->dictionary[$word] = 1;
            }
        }
    }//end initializeDictionary()


    /**
     * Spelling Check
     *
     * @param string  $text       Input
     * @param boolean $ignoreCase [optional]
     * @return array The incorret words
     */
    protected function spellingCheck($text, $ignoreCase=false)
    {
        $results = [];
        $length  = strlen($text);
        $buffer  = '';
        if (!$ignoreCase) {
            // use camel-casing splition.
            for ($i = 0; $i < $length; $i++) {
                $char = ord($text{$i});
                $word = '';

                if ($char > 64 && $char < 91) {
                    // Upper case.
                    if (!empty($buffer)) {
                        $word   = $buffer;
                        $buffer = '';
                    }

                    $buffer .= $text{$i};
                } else if ($char > 96 && $char < 123) {
                    // Lower case.
                    $buffer .= $text{$i};
                } else if (!empty($buffer)) {
                    // Others char, begin new word.
                    $word   = $buffer;
                    $buffer = '';
                }

                // Last char
                if (($i + 1) === $length && !empty($buffer)) {
                    $word = $buffer;
                }

                // invalid word.
                if (empty($word) || is_numeric($word)) continue;

                // Detect the word is existed and included.
                $key = strtolower($word);
                if (!isset($this->dictionary[$key]) || $this->dictionary[$key] === 1) {
                    $results[] = $word;
                }
            }//end for
        } else {
            // use underscore splition.
            $words = array_filter(explode('_', trim($text, " \t\n\r\0\x0B\"'-()[]")));
            foreach ($words as $word) {
                $key = strtolower($word);
                if (is_numeric($word)) continue;
                if (!isset($this->dictionary[$key]) || $this->dictionary[$key] === 1) {
                    $results[] = $word;
                }
            }
        }//end if
        return $results;

    }//end spellingCheck()


}//end class
