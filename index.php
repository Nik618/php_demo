<!DOCTYPE html>
<html lang='ru'>

<head>
    <meta charset='utf-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Currency exchange data</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/css/style.css"/>
</head>

<?php
require_once 'BestСhangeService.php';
$bestChangeService = new BestСhangeService('bm_exch.dat', 'bm_cy.dat', 'bm_rates.dat');
$bestChangeService->zip_load('http://api.bestchange.ru/info.zip');
?>

<body>
<div class="text-center">
    <div id='header'>
        <h2>Currency exchange data</h2>
    </div><br>
    <label for="valuteGive"></label><select id="valuteGive">
            <option value="no select">select currency to give</option>
            <?php $bestChangeService->cy_get_contents($_GET['value1']);?>
        </select><br>
        <label for="valuteGet"></label><select id="valuteGet">
            <option value="no select">select currency to get</option>
            <?php $bestChangeService->cy_get_contents($_GET['value2']);?>
        </select><br>
        <button id="js-button" class="get-button">get a list</button>
    <br>
    I want <span class="checkboxValue"></span> in quantity: <label for="textCount">
        <?php echo '<input type="text" id="textCount" value="' . $_GET['valueCount'] . '" size="5">'; ?>
    </label>
    <label>
        <?php
        if ($_GET['checked'] == 'false')
            echo '<input type="checkbox" id="checkboxGiveGet" class="checkBox">';
        else
            echo '<input type="checkbox" id="checkboxGiveGet" class="checkBox" checked>';?>
    </label>
    (get/give)<br>
    I will <span class="checkboxValueMirror"></span> at the chosen rate: <span class="textValue"></span> (best if not selected)

    <?php
    $stringValue1 = $bestChangeService->get_string_value($_GET['value1']);
    $stringValue2 = $bestChangeService->get_string_value($_GET['value2']);
    $bestChangeService->rows_to_rates($_GET['value1'], $_GET['value2']);
    ?>

    <script>
        $('#js-button').click(function() {
            const valueCount = $('#textCount').val();
            const value1 = $('#valuteGive').val();
            const value2 = $('#valuteGet').val();
            window.location.href = "index.php?value1=" + value1 + "&value2=" + value2 + "&valueCount=" + valueCount + "&checked=" + checkbox.checked;
        });

        let indexRow = 0;
        let rates = <?php echo json_encode($bestChangeService->get_rates()) ?>;
        const checkbox = document.getElementById('checkboxGiveGet');
        const text = document.getElementById('textCount');

        $(function(){
            $('button.select-button').on('click',function() {
                indexRow = $(this).closest('tr').index() - 1;
                send();
            })
        })

        send();
        document.addEventListener('DOMContentLoaded', function () {
            text.addEventListener('input',
                function() {
                    send();
                })
            checkbox.addEventListener('input',
                function() {
                    send();
                })
        });

        function send() {
            let value = parseInt(text.value, 10);
            if (isNaN(value) || value == null) value = 0;
            if (checkbox.checked) {
                document.querySelector('.textValue').innerHTML = (rates[indexRow][3] / rates[indexRow][4] * value).toString();
                document.querySelector('.checkboxValue').innerHTML = 'get';
                document.querySelector('.checkboxValueMirror').innerHTML = 'give';
            } else {
                document.querySelector('.textValue').innerHTML = (value / (rates[indexRow][3] / rates[indexRow][4])).toString();
                document.querySelector('.checkboxValue').innerHTML = 'give';
                document.querySelector('.checkboxValueMirror').innerHTML = 'get';
            }
        }


    </script>

    <table>
        <tr>
            <th>Name exchanger</th>
            <th>price: n <?php echo $stringValue1 ?> -> 1 <?php echo $stringValue2 ?></th>
            <th>Reserve</th>
            <th>Feedbacks</th>
            <th>Action</th>

            <?php if ($bestChangeService->get_rates() != null) foreach ($bestChangeService->get_rates() as $rate) { ?>
        <tr>
            <td>
                <?php
                foreach ($bestChangeService->get_exch() as $exch) {
                    if ($exch[0] == $rate[2]) {
                        echo iconv("windows-1251", "utf-8", $exch[1]);
                    }
                }
                ?>
            </td>
            <td>
                <?php
                echo (double) $rate[3]/$rate[4];
                ?>
            </td>
            <td>
                <?php
                echo $rate[5];
                ?>
            </td>
            <td>
                <?php
                $array = str_split($rate[6]);
                echo 'negative: ';
                foreach ($array as $item) {
                    if ($item != '.') echo $item; else echo ' positive: ';
                }
                ?>
            </td>
            <td><button class="select-button">select</button></td>
        </tr>
        <?php } ?>
    </table>
    <?php echo "total changes: " . count($bestChangeService->get_rates()) . " | total reserve: " . $bestChangeService->get_reserve(); ?>
</div>
</body>
</html>