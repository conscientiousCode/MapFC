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

function deleteWhitespaceNotQuoted($string){
    $output = "";
    for($i = 0; $i < strlen($string); $i++){
        if($string[$i] == '"'){
            $j = findNextUnescapedQuote($string, $i+1, strlen($string));
            if($j == -1){//No such string
                throw new OutOfBoundsException("End Of String Reached Without Quote");
            }
            for($k = $i; $k <= $j; $k++){
                $output = $output.$string[$k];
            }
            $i = $j;
        }elseif(isWhiteSpace($string[$i])){
            //pass it by
        }else{
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

function isBracketConjugate($char1, $char2){
    switch ($char1){
        case '[':
            return $char2 == ']';
            break;
        case '{':
            return $char2 == '}';
            break;
        case ']':
            return $char2 == '[';
            break;
        case '}':
            return $char2 == "{";
            break;
        default:
            return false;
    }
}

//Assumes that it starts on the first character past the previous delimeter and or openning bracket
function findNextValueInSameLayer($string, $start){
    $braceStack = [];
    $braceIndex = -1;//points at the top of the stack
    if($string[$start] == '{' || ){
        //HOW TO DIFFERENTIATE AN OPPENNING bracket being a value on this layer, or the openning brace
        //of this layer?
    }

    for($i = $start; $i < strlen($string); $i++){
        switch ($string[$i]){
            case '{':
            case '[':
                $braceIndex++;
                $braceStack[$braceIndex] = $string[$i];
                break;
            case '}':
            case ']':
                echo isBracketConjugate($braceStack[$braceIndex], $string[$i])."\n";
                echo $braceIndex."\n";
                if($braceIndex >= 0 && isBracketConjugate($braceStack[$braceIndex], $string[$i])){
                    $braceIndex--;
                }elseif($braceIndex == -1 && isBracketConjugate($braceStack[$braceIndex], $string[$i])){
                    return $i;
                }else{
                    throw new UnexpectedValueException("bracket mismatch");
                }
                break;
            case ',':
                if($braceIndex == -1){
                    return $i;
                }
                break;
            case '"':
                $i = findNextUnescapedQuote($string, $i+1, strlen($string));
                if($i == -1){
                    throw new OutOfBoundsException("No closing quote found");
                }
                break;

            default:
        }
    }
    throw new OutOfBoundsException("End of string reached without finding value delimeter");
}

function prepareJSONForValidation($json){
    try {
        $json = deleteWhitespaceNotQuoted($json, '"');
    }catch (OutOfBoundsException $e){
        throw new OutOfBoundsException("Failed to find a closing quote for a string value");
    }
    return $json;
}


