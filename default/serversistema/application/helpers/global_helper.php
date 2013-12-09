<?php

function requierePost() {

    //print_r($_POST);
    if (!empty($_POST)) {
        return true;
    } else {
        responseCode(true, 'Requiere datos POST');
    }
}

function getFecha($fecha) {
    return date('c', strtotime($fecha));
}

function setFecha($fecha) {
    
}

function jsonDecode($data) {
    //print_r(json_decode($data));
    $data = json_decode($data);
    foreach ($data as $key => $value) {
        $array[$value->name] = $value->value;
    }
    return arrayToObject($array);
}

function response($success, $data = '') {
    header("HTTP/1.0 200 OK");
    header('Content-type: application/json');
    $data2["success"] = $success;
    $data2["message"] = $data;
    print_r((json_encode($data2)));
}

function responseCode($success = true, $message = '', $data = array(), $code = 1) {
    header("HTTP/1.0 200 OK");
    header('Content-type: application/json');
    $data2["success"] = $success;
    $data2["message"] = $message;
    $data2["code"] = $code;
    $data2["data"] = $data;
    if (isset($_GET['callback'])) {
        print_r($_GET['callback'] . '(' . (json_encode($data2)) . ');');
        //print_r(prettyPrint(json_encode($data)));
    } else {
        print_r(prettyPrint(json_encode($data2)));
    }
    die();
    //print_r(prettyPrint(json_encode($data2)));
}

function responseData($success, $data = array()) {
    header("HTTP/1.0 200 OK");
    header('Content-type: application/json');
    $data2['data'] = $data;
    $data2["success"] = $success;

    //print_r(prettyPrint(json_encode($data2)));
    if (isset($_GET['jsoncallback'])) {
        print_r($_GET['jsoncallback'] . '(' . prettyPrint(json_encode($data2)) . ');');
        //print_r(prettyPrint(json_encode($data)));
    } else {
        print_r(prettyPrint(json_encode($data2)));
    }
}

function prettyPrint($json) {
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen($json);

    for ($i = 0; $i < $json_length; $i++) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if ($ends_line_level !== NULL) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ($char === '"' && $prev_char != '\\') {
            $in_quotes = !$in_quotes;
        } else if (!$in_quotes) {
            switch ($char) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        }
        if ($new_line_level !== NULL) {
            $result .= "\n" . str_repeat("\t", $new_line_level);
        }
        $result .= $char . $post;
        $prev_char = $char;
    }

    return $result;
}

/* Limpiar caracteres especiales para busqueda de ingredientes */

function cleanSpecialCharacters($string) {
    /* $string = htmlentities($string);
      $string = preg_replace('/\&(.)[^;]*;/', '\\1', $string); */

    $string = trim($string);

    $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', '�?', 'À', 'Â', 'Ä'), array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'), $string
    );

    $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'), array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'), $string
    );

    $string = str_replace(
            array('í', 'ì', 'ï', 'î', '�?', 'Ì', '�?', 'Î'), array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'), $string
    );

    $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'), array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'), $string
    );

    $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'), array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'), $string
    );

    $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'), array('n', 'N', 'c', 'C',), $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
            array("\\", "¨", "º", "-",
        "#", "@", "|", "!", "\"",
        "·", "$", "/",
        "?", "¡",
        "¿", "[", "^", "`", "]",
        "+", "}", "{", "¨", "´",
        ">", "<", ":"), '', $string
    );

    return $string;
}

/////////////////////////////////////Convierte decimal a fraccion
function fractionvalue($qty) {
    $Qty = $qty;
    $temp = strval("" . $qty . "");

    $beforpoint = "";
    $afterpoint = "";
    $flag = 0;
    for ($x = 0; $x < strlen($temp); $x++) {
        if ($temp[$x] == '.')
            $flag = 1;

        if ($flag == 0)
            $beforpoint.=$temp[$x];
        if ($flag == 1)
            $afterpoint.=$temp[$x];
    }
    switch ($afterpoint) {
        case ".50": $qty = $beforpoint . " " . "1/2";
            break;
        case ".33": $qty = $beforpoint . " " . "1/3";
            break;
        case ".25": $qty = $beforpoint . " " . "1/4";
            break;
        case ".67": $qty = $beforpoint . " " . "2/3";
            break;
        case ".75": $qty = $beforpoint . " " . "3/4";
            break;

        default: break;
    }


    return $qty;
}

//Objeto a array
function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

//Array  a Objeto
function arrayToObject($d) {
    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return (object) array_map(__FUNCTION__, $d);
    } else {
        // Return object
        return $d;
    }
}

function cleanUsername($string) {
    $string = preg_replace("/[[:punct:]]/", '', $string);
    $string = preg_replace("/[\s]/", '', $string);
    return $string;
}

function specialHtml($string) {
    $string = stripslashes(htmlspecialchars($string));
    return $string;
}

//En la descripcion del serving eliminar lo que esta entre parentesis para reducir la longitud del texto
function clearPortion($portion = "") {

    return @trim(preg_replace("/\([^\)]+\)/", "", stripslashes($portion)));
}

//Ordena un array de objetos fuente=>http ://php.net/manual/es/function.array-multisort.php
function sort_arr_of_obj($array, $sortby, $direction = 'asc') {

    $sortedArr = array();
    $tmp_Array = array();

    foreach ($array as $k => $v) {
        $tmp_Array[] = strtolower($v->$sortby);
    }

    if ($direction == 'asc') {
        asort($tmp_Array);
    } else {
        arsort($tmp_Array);
    }

    foreach ($tmp_Array as $k => $tmp) {
        $sortedArr[] = $array[$k];
    }

    return $sortedArr;
}

function get_daily_time($envioHoras, $validateDateTime = '', $date_journal = '') { // meals time have to round off to 30 not 15(bed and wake time, excercises)
    $k = ($food_time) ? 2 : 4;
    $incement = ($food_time) ? 30 : 15;
    $dailytime = array();
    $indice = 1;
    $rangoMin = 0;
    $rangoMax = 0;
    $fecha_oneHours = strtotime($envioHoras[0]['date_journal']);
    $arrayHours[$indice] = date("H:i:s", $fecha_oneHours);
    $arryFecha[$indice] = date("Y-m-d", $fecha_oneHours);
    //print_r($date_journal."\n");
    while ($fecha_oneHours != strtotime('-15 minutes', strtotime($envioHoras[1]['date_journal']))) {
        $indice++;
        $fecha_oneHours = strtotime('+15 minutes', $fecha_oneHours);
        $arrayHours[$indice] = date("H:i:s", $fecha_oneHours);
        $arryFecha[$indice] = date("Y-m-d", $fecha_oneHours);
        //print_r($arryFecha[$indice]." ".$arrayHours[$indice]."\n");
    }

    $indice = 0;

    for ($i = 0; $i <= 23; $i++) {
        $counter = 0;
        for ($j = 0; $j < $k; $j++) {

            $hour_key = ($i < 10) ? "0" . $i : $i;
            $min_key = ($counter < 10) ? '0' . $counter : $counter;
            $sec_key = "00";
            $sec_label = ($i < 12) ? 'AM' : 'PM';

            if ($i < 13) {

                if ($i == 0) {
                    $hour_label = 12;
                } else {
                    $hour_label = ($i < 10) ? "0" . $i : $i;
                }
            } else if ($i >= 13) {
                $hour_label = ($i - 12);
                $hour_label = ($hour_label < 10) ? "0" . $hour_label : $hour_label;
            }

            $counter = $counter + $incement;
            $id = $hour_key . ':' . $min_key . ':' . $sec_key;
            $value = $hour_label . ':' . $min_key . ' ' . $sec_label;
            if ((array_search($id, $arrayHours))) {
                $new_indice = array_search($id, $arrayHours);
                $dailytime[$indice]->id = $id;
                $dailytime[$indice]->value = $value;
                $dailytime[$indice]->date = $arryFecha[$new_indice];
                $nuevaFecha = $arryFecha[$new_indice] . " " . $arrayHours[$new_indice];
                $dailytime[$indice]->date_journal = $nuevaFecha;
                $dailytime[$indice]->atributo = (array_search($nuevaFecha, $validateDateTime)) ? "disabled='disabled'" : "";
                $dailytime[$indice]->strtotime = strtotime($nuevaFecha);
                if ($nuevaFecha == $date_journal) {
                    $dailytime[$indice]->atributo = "selected='selected'";
                }
                if ($dailytime[$indice]->atributo == "disabled='disabled'") {
                    unset($dailytime[$indice]);
                    $indice--;
                }
                $indice++;
            }
        }
    }
    //print_r(date('H:i:s')."\n"); 
    //print_r($dailytime);
    if (strlen($date_journal) < 1) { //print_r($dailytime);
        $indiceSelect = 0;
        $horaInicial = (int) strtotime($dailytime[0]->id);
        $horaFin = (int) strtotime($dailytime[count($dailytime) - 1]->id);
        $horaActual = (int) strtotime(date("H:i:s"));
        //$hora = "23:40:21";
        //$horaActual = (int)strtotime($hora);
        //print_r($dailytime[0]->id." - ".$dailytime[count($dailytime)-1]->id." - ".date("H:i:s"));
        // print_r($horaInicial." ".$horaFin." ".$horaActual);
        //print_r($hora);
        if ($horaActual > $horaInicial) { //print_r(date("H:i:s")); print_r(" ".$dailytime[count($dailytime)-1]->id);
            if ($horaActual < $horaFin) {
                /* for($z=0; $z<count($dailytime); $z++) {
                  //print_r("\n".$dailytime[$z]->id."\n");
                  //print_r("\n".(int)$horaActual ."-".(int)strtotime($dailytime[$z]->id)."-".$dailytime[$z]->id."\n");
                  if((int)$horaActual<=(int)strtotime($dailytime[$z]->id)) { //print_r($dailytime[$z]->id."\n");
                  //print_r("\n".(int)$horaActual ."-".(int)strtotime($dailytime[$z]->id)."-".$dailytime[$z]->id."\n");
                  $indiceSelect = $z-1;         break;
                  }
                  } */
                foreach ($dailytime as $keySelect => $value) {
                    if ((int) $horaActual <= (int) strtotime($dailytime[$keySelect]->id)) { //print_r($value->id);
                        //print_r("\n".(int)$horaActual ."-".(int)strtotime($dailytime[$keySelect]->id)."-".$dailytime[$keySelect]->id."\n");
                        $indiceSelect = $keySelect - 1;
                        break;
                    }
                }
            } else {
                $indiceSelect = count($dailytime) - 1;
            }
        }
        $dailytime[$indiceSelect]->atributo = "selected='selected'";
    }

    $dailytime = (sort_arr_of_obj($dailytime, 'strtotime'));
    return $dailytime;
}

function get_current_time_db($time_db = '', $action = '') {
    //this code obtains curent time or saved time from db.
    $hora = date('H');
    $min = date('i');
    $min = ($min < 30) ? "00" : "30";
    $meridiano = "00";
    $current_hour = $hora . ":" . $min . ":" . $meridiano;
    $time = ($action == "addnew") ? $current_hour : $time_db;

    return $time;
}

function array_key_relative($array, $current_key, $offset = 1) {
    // create key map
    $keys = array_keys($array);
    // find current key
    $current_key_index = array_search($current_key, $keys);
    // return desired offset, if in array, or false if not
    if (isset($keys[$current_key_index + $offset])) {
        return $keys[$current_key_index + $offset];
    }
    return false;
}

function mostrarDiferencia($date1, $date2) {     //(min, max)
    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));

    $minuts = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);

    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));

    return array("years" => $years, "months" => $months, "days" => $days, "hours" => $hours, "minuts" => $minuts, "seconds" => $seconds);
    //printf("%d years, %d months, %d days, %d hours, %d minuts\n, %d seconds\n", $years, $months, $days, $hours, $minuts, $seconds); 
}

?>