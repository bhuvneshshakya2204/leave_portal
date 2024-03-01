<?php
function sortByOrder($a, $b) {
    return $a['seq'] - $b['seq'];
}

function getColumns($columns){
    $colStr = "";
    usort($columns, 'sortByOrder');
    foreach ($columns as $key => $column) {
        $colStr .= strlen($column['db']) > 0 ? $column['db']." as `".$column['name']."`," : "";
    }
    return $colStr = substr($colStr, 0, -1);
}

