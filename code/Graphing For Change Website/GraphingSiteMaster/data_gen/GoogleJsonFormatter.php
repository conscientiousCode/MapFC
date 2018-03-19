<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/03/18
 * Time: 10:58 AM
 */

class GoogleJsonFormatter
{
    private static $JSON_OPEN = '{';
    private static $JSON_CLOSE = '}';

    private static $COLS_OPEN = '{ "cols":[';
    private static $COLS_CLOSE = ']},';
    private static $COL_OPEN = '';
    private static $COL_CLOSE = '';

    private static $ROWS_OPEN = '';
    private static $ROWS_CLOSE = '';
    private static $ROW_OPEN = '';
    private static $ROW_CLOSE = '';
    private static $ROW_ITEM_OPEN = '';
    private static $ROW_ITEM_CLOSE  = '}';

    private $cols = [];
    private $cols_i = -1;
    private $rows = [];
    private $rows_i = -1;

    //$col is a map with key's 'name' and 'type'
    public function addCol($col){
        if($col["name"] == null || $col["type"] == null){
            throw new Exception("Either 'name' or 'type' is null");
        }

        for($i = 0; $i<= $this->cols_i; $i++){
            if($col["name"] == $this->cols["name"]){
               throw new InvalidArgumentException("Cannot have two columns of the same name");
            }
        }
        $this->cols_i += 1;
        $this->cols[$this->cols_i] = array("name"=>$col["name"], "type"=>$col["type"]);
    }

    //Must be indexed $0 to numberOfCols -1
    //Row values should be in the same order as which the columns were added
    //A Row should not be null, make it the empty string instead
    public function addRow($row){
        if(sizeof($row) +1 != $this->cols_i){
            throw new InvalidArgumentException("this row does not have the same number of values as there are columns");
        }
        $temp = [];
        for($i = 0; $i <= $this->cols_i; $i++){
            if($row[$i] == null){
                throw new InvalidArgumentException("A row value cannot be null, make it the empty string instead");
            }
            $temp[$i] = $row[$i];
        }

        $this->rows_i += 1;
        $this->rows[$this->rows_i] = $temp;
    }

    public function getJson(){
        assert ($this->cols_i >= 0);
        assert ($this->rows_i >= 0);
        $returnString = self::$JSON_OPEN.self::$COLS_OPEN;
        $comma = ',';
        for($i = 0; $i <= $this->cols_i; $i++){
            if($i == $this->cols_i){
                $comma = '';
            }
            $returnString = $returnString.self::$COL_OPEN.'"id":"","label":'.$this->cols[$i]['name']
                .',"pattern":"","type":'.$this->cols[$i]['type'].self::$COL_CLOSE.$comma;
        }


        

    }



}