<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 01/03/18
 * Time: 1:01 AM
 */

require 'ParseCell.php';

class ParseCellTest extends PHPUnit_Framework_TestCase
{

    function testFindNextUnescapedQuoteTest(){
        $str = '"Hello"';
        $start = 1;
        $return = 6;
        $this -> assertTrue(findNextUnescapedQuote($str, $start, strlen($str)) == $return);

        $str = '"ignore\""';
        $return = 9;
        $this -> assertTrue(findNextUnescapedQuote($str, $start, strlen($str)) == $return);

    }

    function testIndexOfCharContainedTest(){
        $chars = ',}';
        $this -> assertTrue(indexOfCharContained($chars, ',') == 0);
        $this -> assertTrue(indexOfCharContained($chars, '}') == 1);
        $this -> assertTrue(indexOfCharContained($chars, '"') == -1);
    }

    function testNextNonTextCommaOrEndBrace(){
        $string = '{"key1":value, "key2":"value"}';
        $start = 0;
        $expected =13;

        $this -> assertTrue(findNextNonTextCommaOrEndBrace($string, $start, strlen($string)) == $expected);
        $start = 14;
        $expected = strlen($string) -1;
        $this -> assertTrue(findNextNonTextCommaOrEndBrace($string, $start, strlen($string)) == $expected);

    }

    function testReplaceQuoteDelimitedStrings(){
        $inputString = '"Hello" I am not part of the strings "another string" neither am I';
        $replacementSequence = 'STRING';
        $outputString = 'STRING I am not part of the strings STRING neither am I';


        self::assertTrue($outputString == replaceQuoteDelimitedStrings($inputString, $replacementSequence));

    }

    function testIsNewBefore(){
        self::assertTrue(isNewBefore("new ", 3));
        self::assertTrue(isNewBefore("Hello new ", 9));
        self::assertTrue(isNewBefore("Hello new ", 9));
    }

    function testIsWhite(){
        self::assertTrue(isWhiteSpace("\n"));
        self::assertFalse(isWhiteSpace('k'));
    }

    function testDeleteWhitespace(){
        $input = " No\n he\tllo new Date";
        $output = "Nohellonew Date";

        self::assertTrue(deleteWhitespace($input) == $output);

        $input = "{fake:\"value here mate\n\" date: new Date()}";
        $output = '{fake:"valueheremate"date:new Date()}';

        self::assertTrue(deleteWhitespace($input) == $output);
    }

    function testPrepareJSONForValidation(){
        $input = "{f:\"STRING\",  \nv:\"ANOTHER STRING\"}";
        $output = "{f:\"STRING\",v:\"ANOTHER STRING\"}";

        self::assertTrue($output == prepareJSONForValidation($input));
    }

    function testDeleteWhitespaceNotQuoted(){
        $input = "Every Space \n And New Line Should Be \t Gone";
        $output = "EverySpaceAndNewLineShouldBeGone";

        self::assertTrue($output == deleteWhitespaceNotQuoted($input));

        $input = "Not \nAll \"White \tSpace Will\" Be Spared";
        $output = "NotAll\"White \tSpace Will\"BeSpared";

        self::assertTrue($output == deleteWhitespaceNotQuoted($input));

    }

    function testFindNextValueInSameLayer(){
        $input = "{}";
        $output = 1;

        self::assertTrue(findNextValueInSameLayer($input, 0) == 1);
    }

}
