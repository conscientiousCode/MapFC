<?php
/**;
 * Created by PhpStorm.
 * User: daniel
 * Date: 01/03/18
 * Time: 12:59 AM
 */


//LAYER TYPES

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

//Assumes that it starts on the first character past the previous delimeter
//NEVER START ON THE OPENNING BRACE OF A LAYER
function findNextValueInSameLayer($string, $start){
    $braceStack = [];
    $braceIndex = -1;//points at the top of the stack

    for($i = $start; $i < strlen($string); $i++){
        switch ($string[$i]){
            case '{':
            case '[':
                $braceIndex++;
                $braceStack[$braceIndex] = $string[$i];
                break;
            case '}':
            case ']':
                if($braceIndex >= 0 && isBracketConjugate($braceStack[$braceIndex], $string[$i])){
                    $braceIndex--;
                }elseif($braceIndex == -1){
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

function inclusiveSubstring($string, $start, $end){
    $subString = "";
    for($i = $start; $i <= $end; $i++){
        $subString = $subString.$string[$i];
    }
    return $subString;
}

//Layer should start with { or [, and end with },]
//Returns an array of strings for each value in the layer
function getAllTokensForLayer($layer){
    $tokens = [];
    $token_index = 0;

    for($i = 1; $i< strlen($layer); $i++){
        $j = findNextValueInSameLayer($layer, $i);
        $tokens[$token_index++] = inclusiveSubstring($layer, $i, $j-1);//remove ,
        $i = $j;//pass delimeter on loop
    }
    return $tokens;
}

//ONE OF: PRIMITIVE, MAP, ARRAY
//Input layer should have all non-delimited whitespace removed
function getLayerType($layer){
    switch ($layer[0]){
        case '{':
            return "MAP";
            break;
        case '[':
            return "ARRAY";
            break;
        default:
            return "PRIMITIVE";
    }

}



//$layer must be of type MAP
//IF NOT SUCH KEY RETURN null
//$key does not have surrounding quotes
//Assumes no excess whitespace
function getValueForMap($layer, $key){
    for($i = 1; $i < strlen($layer); $i++){
        if($layer[$i] == '"'){
            $j = findNextUnescapedQuote($layer, $i+1, strlen($layer));
            $K = inclusiveSubstring($layer, $i+1, $j-1);
            $i = $j+2;//Move past :
            $j = findNextValueInSameLayer($layer,$i);

            if($K == $key){//MATCH
                return inclusiveSubstring($layer, $i, $j-1);
            }
            $i = $j;
        }
    }

    return null;
}


function prepareJSONForValidation($json){
    try {
        $json = deleteWhitespaceNotQuoted($json, '"');
    }catch (OutOfBoundsException $e){
        throw new OutOfBoundsException("Failed to find a closing quote for a string value");
    }
    return $json;
}

//TODO: MAKE the tests more robust and indepth
function assertGoogleChartJSONValid($json){

    $json = prepareJSONForValidation($json);
    Assert(getLayerType($json) == "MAP");

    $cols = getValueForMap($json, "cols");
    $rows = getValueForMap($json, "rows");
    Assert(getLayerType($cols) == "ARRAY");
    Assert(getLayerType($rows) == "ARRAY");

    $cols_tokens = getAllTokensForLayer($cols);
    $numberOfColumns = 0;
    foreach($cols_tokens as $col){
        $numberOfColumns++;
        Assert(getLayerType($col) == "MAP");
        //TODO: CHECK FOR SPECIFIC KEYS IN EACH COLUMN
    }


    $rows_tokens = getAllTokensForLayer($rows);

    foreach($rows_tokens as $row){
        Assert(getLayerType($row) == "MAP");
        $r_i = getValueForMap($row, "c");
        Assert(getLayerType($r_i) == "ARRAY");
        $r_i_tokens = getAllTokensForLayer($r_i);
        Assert(sizeof($r_i_tokens) == $numberOfColumns);
        foreach($r_i_tokens as $r_i_token){
            Assert(getLayerType($r_i_token) == "MAP");
            //TODO: GO DEEPER AND CHECK THE KEYS k, f, p
        }
    }

    return true;
}


