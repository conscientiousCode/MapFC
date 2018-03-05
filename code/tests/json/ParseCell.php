<?php
/**;
 * Created by PhpStorm.
 * User: daniel
 * Date: 01/03/18
 * Time: 12:59 AM
 */


function findNextUnescapedQuote($string, $startIndex, $upperIndexBound){
    $i = $startIndex;
    if(strlen($string) < $upperIndexBound){
        return -1;
    }
    while($i < $upperIndexBound){
        if($string[$i] == '\\'){
            $i++; //skip the next character
        }elseif($string[$i] == '"'){
            return $i;
        }
        $i++;
    }
    return -1;
}

function findNextOf($characters, $string, $startIndex, $upperIndexBound){
    $i = $startIndex;
    if(strlen($string) < $upperIndexBound){
        return -1;
    }
    while($i < $upperIndexBound) {

    }
}

function indexOfCharContained($characters, $char){
    for($i = 0; $i < strlen($characters); $i++){
        if($char == $characters[$i]){
            return $i;
        }
    }
    return -1;
}

function findNextNonTextCommaOrEndBrace($string, $startIndex, $upperIndexBound){
    $i = $startIndex;
    if(strlen($string) < $upperIndexBound){
        return -1;
    }
    $chars = ',}';
    while($i < $upperIndexBound) {
        if($string[$i] == '"') {
            $i = findNextUnescapedQuote($string, $i + 1, $upperIndexBound);
            if ($i == -1) {//no such character
                return -1;
            }
            //We automatically move past the quote at the end of the loop
        }elseif(0 <= indexOfCharContained($chars, $string[$i])){
            return $i;
        }
        $i+=1;
    }
    return -1;
}

//If quote not closed, return "";
function replaceQuoteDelimitedStrings($inputString, $replacementSequence){
    $i = 0;
    $outputString = "";
    while($i < strlen($inputString)){
        if($inputString[$i] == '"'){
            $i = findNextUnescapedQuote($inputString, $i+1, strlen($inputString));
            if($i == -1){
                return "";
            }
            $outputString = $outputString.$replacementSequence;//String concat
        }else{
           $outputString = $outputString.$inputString[$i];//String concat
        }

        $i+=1;
    }
    return $outputString;
}

function isNewBefore($string, $i){
    if($i < 3){
        return false;
    }else{
        return $string[$i-1] == 'w' and $string[$i-2] == 'e' and $string[$i-3] == 'n';
    }
}

function deleteWhitespace($string){
    $output = "";
    for($i =0; $i < strlen($string); $i++){
        if(!isWhiteSpace($string[$i]) || isNewBefore($string, $i)){
           $output = $output.$string[$i];
        }
    }
    return $output;
}

function isWhiteSpace($char){
    $whiteSpaceCharacters = "\0\t\n\x0B\r ";

    for($i =0; $i<strlen($whiteSpaceCharacters); $i++){
        if($char == $whiteSpaceCharacters[$i]){
           return true;
        }
    }
    return false;
}

function prepareJSONForValidation($json){
    $json = replaceQuoteDelimitedStrings($json, '"');
    $json = deleteWhitespace($json);
    return $json;
}
