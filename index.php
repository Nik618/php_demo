<!DOCTYPE html>
<html lang='ru'>

<head>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Currency exchange data</title>
    <!--<script src="https://code.jquery.com/jquery-3.6.0.js"></script>-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/css/style.css"/>
</head>

<?php
function cache_get_contents($url, $offset = 1000) {
    $file = '/tmp/file_cache_' . md5($url);
    if (file_exists($file) && filemtime($file) > time() - $offset)
        return file_get_contents($file);
    $contents = file_get_contents($url);
    if ($contents !== false)
        file_put_contents($file, $contents);
    return $contents;
}

function cy_get_contents($cy, $param) {
    foreach ($cy as $cy2) {
        $attr = ($param == $cy2[0]) ? 'selected' : '';
        echo '<option value="' . $cy2[0] . '"' . $attr . '>' . iconv("windows-1251", "utf-8", $cy2[3]) . '</option>';
    }
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/files/zip/info.zip', cache_get_contents('http://api.bestchange.ru/info.zip'));
$zip = new ZipArchive;
$zip->open('files/zip/info.zip');
$zip->extractTo('files/');
$zip->close();

$cy = array_map(function($data) { return str_getcsv($data,";");}
    , file('files/bm_cy.dat'));
?>

<body>
    <div class="text-center">
        <div id='header'>
            <h2>Currency exchange data</h2>
        </div><br>
        <!--<label for="valuteGet"></label><select id="valuteGet">
            <option value="no select">select currency to give</option>
            <?php //cy_get_contents($cy);?>
        </select><br>
        <label for="valuteGive"></label><select id="valuteGive">
            <option value="no select">select currency to get</option>
            <?php //cy_get_contents($cy);?>
        </select><br>
        <button id="js-button" class="get-button">get a list</button>-->
        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_SESSION['idGet'] = $_POST['valuteGet'];
            $_SESSION['idGive'] = $_POST['valuteGive'];
        }
        ?>

        <form method="post">
            <label>
                <select name="valuteGet">
                    <option value="no select">select currency to give</option>
                    <?php cy_get_contents($cy, $_SESSION['idGet']);?>
                </select>
            </label><br>
            <label>
                <select name="valuteGive">
                    <option value="no select">select currency to get</option>
                    <?php cy_get_contents($cy, $_SESSION['idGive']);?>
                </select>
            </label>
            <p><input type="submit" value="get a list of exchangers" class="get-button"/></p>
        </form>

        <?php
        $exch = array_map(function($data) { return str_getcsv($data,";");}
            , file('files/bm_exch.dat'));

        $reserve = 0;
        //$value1 = $_GET["value1"];
        //$value2 = $_GET["value2"];
        $value1 = $_POST['valuteGet'];
        $value2 = $_POST['valuteGive'];
        foreach ($cy as $cy2) {
            if ($cy2[0] == $value1) $stringValue1 = iconv("windows-1251", "utf-8", $cy2[3]);
            if ($cy2[0] == $value2) $stringValue2 = iconv("windows-1251", "utf-8", $cy2[3]);
        }

        $rows = array_map(function($data) { return str_getcsv($data,";");}
            , file('files/bm_rates.dat'));
        $rates = [];

        foreach ($rows as $row1) {
            if (($row1[0] == $value1) && ($row1[1] == $value2)) {
                $rates[] = $row1;
                $reserve += (double) $row1[5];
            }
        }?>

        <!--<script>
            $('#js-button').click(function(){
                const value1 = $('#valuteGet').val();
                const value2 = $('#valuteGive').val();
                window.location.href = "index.php?value1=" + value1 + "&value2=" + value2;
            });
        </script>-->

        <table>
            <tr>
                <th>Name exchanger</th>
                <th><?php echo $stringValue1 ?> -> 1 <?php echo $stringValue2 ?></th>
                <th>Reserve</th>
                <th>Feedbacks</th>

            <?php foreach ($rates as $rate1) { ?>
            <tr>
                <td>
                    <?php
                    foreach ($exch as $exch2) {
                        if ($exch2[0] == $rate1[2]) {
                            echo iconv("windows-1251", "utf-8", $exch2[1]);
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    echo (double) $rate1[3]/$rate1[4];
                    ?>
                </td>
                <td>
                    <?php
                    echo $rate1[5];
                    ?>
                </td>
                <td>
                    <?php
                    $array = str_split($rate1[6]);
                    echo 'negative: ';
                    foreach ($array as $item) {
                        if ($item != '.') echo $item; else echo ' positive: ';
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
        </table>
        <?php echo "total changes: " . count($rates) . " | total reserve: " . $reserve; ?>
    </div>
</body>
</html>