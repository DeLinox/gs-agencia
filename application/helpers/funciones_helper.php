<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function THS($arr) {
    $str = "";
    foreach ($arr as $cod => $val) {
        if(!preg_match('/DT_/',$val['dt']))
        $str .= '<th class="ths">' . $val['dt'] . '</th>';
    }
    return $str;
}

function es($valor){
    return $valor!='0.00'&&!empty($valor);
}
    
function dateToMysql($date){
    $date = trim($date);
    if(preg_match('/ /',$date)) return preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date);
    return preg_replace('#(\d{2})/(\d{2})/(\d{4})#', '$3-$2-$1', $date);
}

function genDataTable($id,$columns,$withcheck=false,$responsive=false){
    if($responsive) $class = "table table-striped table-bordered responsive nowrap";
    else $class = "table table-striped table-bordered";
    return '<table id="'.$id.'" wch="'.$withcheck.'" cellpadding="0" cellspacing="0" border="0" width="100%" class="'.$class.'">
            <thead>
                <tr>
                    '.($withcheck?'<th></th>':'').THS($columns).'
                </tr>
            </thead>
        </table>';
}
