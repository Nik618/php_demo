<?php
ini_set('memory_limit', '-1');
class BestСhangeService
{

    private $exch;
    private $cy;
    private $rows;
    private $rates;
    private $reserve;

    public function __construct($exch, $cy, $rates)
    {
        $this->exch = array_map(function($data) { return str_getcsv($data,";");}
            , file('files/' . $exch));

        $this->cy = array_map(function($data) { return str_getcsv($data,";");}
            , file('files/' . $cy));

        $this->rows = array_map(function($data) { return str_getcsv($data,";");}
            , file('files/' . $rates));

        $this->reserve = 0;
    }

    function get_exch() {
        return $this->exch;
    }

    function get_rates() {
        return $this->rates;
    }

    function get_reserve() {
        return $this->reserve;
    }

    function cache_get_contents($url, $time = 1000) {
        $file = '/tmp/file_cache_' . md5($url);
        if (file_exists($file) && filemtime($file) > time() - $time)
            return file_get_contents($file);
        $contents = file_get_contents($url);
        if ($contents !== false)
            file_put_contents($file, $contents);
        return $contents;
    }

    function zip_load($url) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/files/zip/info.zip', $this->cache_get_contents($url));
        $zip = new ZipArchive;
        $zip->open('files/zip/info.zip');
        $zip->extractTo('files/');
        $zip->close();
    }

    function cy_get_contents($param) {
        foreach ($this->cy as $cy) {
            $attr = ($param == $cy[0]) ? 'selected' : '';
            echo '<option value="' . $cy[0] . '"' . $attr . '>' . iconv("windows-1251", "utf-8", $cy[3]) . '</option>';
        }
    }

    function rows_to_rates($value1, $value2) {
        foreach ($this->rows as $row) {
            if (($row[0] == $value1) && ($row[1] == $value2)) {
                $this->rates[] = $row;
                $this->reserve += (double) $row[5];
            }
        }

        function cmp($a, $b) {
            return strcmp($a[3]/$a[4], $b[3]/$b[4]);
        }

        if ($this->rates != null)
            usort($this->rates, "cmp");
    }

    function get_string_value($value) {
        $stringValue = 0;
        foreach ($this->cy as $cy) {
            if ($cy[0] == $value) {
                $stringValue = iconv("windows-1251", "utf-8", $cy[3]);
                break;
            }
        }
        return $stringValue;
    }
}