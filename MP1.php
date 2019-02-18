<?php
main::start("so-csv.csv.csv");
class main {
    public static function start($csvFileName) {
        $datasets = csv::getRecordsFromCSV($csvFileName);
        $table = html::generateHTMLTable($datasets);
        echo $table;
    }
}
class html {
    public static function generateHTMLTable($datasets) {
        $isFirstDataset = true;
        $table = self::returnHTMLHeader();
        foreach ($datasets as $dataset) {
            $array = $dataset->returnRecordAsArray();
            if($isFirstDataset) {
                $fields = array_keys($array);
                $table = self::returnLoopString($fields, $table);
                $isFirstDataset = false;
            }
            $values = array_values($array);
            $table = self::returnLoopString($values, $table);
        }
        $table.='</table></body></html>';
        return $table;
    }
    public static function returnHTMLHeader(){
        $table = '<!DOCTYPE html><html lang="en"><head><link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" />
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
                    <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script><title>NREL RSF Weather Data 2011</title></head><body><table class="table table-bordered table-striped">';
        return $table;
    }
    public static function returnLoopString($array, $table){
        $table.='<tr>';
        foreach($array as $value){
            $table .= $value;
        }
        $table.= '</tr>';
        return $table;
    }
}
class csv {
    public static function getRecordsFromCSV($csvFileName) {
        $csvFile = fopen($csvFileName, "r");
        $columnNames = array();
        $isHeaderdataset = true;
        while(!feof($csvFile)){
            $row = fgetcsv($csvFile);
            if($isHeaderdataset){
                $columnNames = $row;
                $isHeaderdataset = false;
            } else {
                $datasets[] = recordFactory::createRecord($columnNames, $row);
            }
        }
        fclose($csvFile);
        return $datasets;
    }
}
class recordFactory {
    public static function createRecord(Array $columnNames = null, $cellValues = null) {
        $dataset = new record($columnNames, $cellValues);
        return $dataset;
    }
}
class record {
    public function __construct(Array $columnNames = null, $cellValues = null) {
        $dataset = array_combine($columnNames, $cellValues);
        foreach ($dataset as $key => $value){
            $this -> createProperty($key, $value);
        }
    }
    public function createProperty($key = 'key', $value = 'value') {
        $key = '<th>'. $key . '</th>';
        $value = '<td>'. $value . '</td>';
        $this->{$key} = $value;
    }
    public function returnRecordAsArray(){
        $array = (array) $this;
        return $array;
    }
}