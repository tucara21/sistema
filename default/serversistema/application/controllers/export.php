<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Export extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('PHPExcel');
    }

    public function exportarTFecha() {

        $fecha_reporte = $_GET['fecha_reporte'];

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        $arr = "";

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Eder Otiniano")
                ->setLastModifiedBy("Eder Otiniano")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ITEM')
                ->setCellValue('B1', 'CODIGO TECNICO')
                ->setCellValue('C1', 'NOMBRE TECNICO')
                ->setCellValue('D1', 'CODIGO AUXILIAR')
                ->setCellValue('E1', 'NOMBRE AUXILIAR')
                ->setCellValue('F1', 'HORA DE INGRESO')
                ->setCellValue('G1', 'HORA DE ENTRADA JOBIE')
                ->setCellValue('H1', 'HORA DE DESP ALMACEN')
                ->setCellValue('I1', 'CANTIDAD DE WO')
                ->setCellValue('J1', 'HORA DE LELGADA PRIMER PUNTO')
                ->setCellValue('K1', 'CANTIDAD WO T')
                ->setCellValue('L1', 'ACTIVACIONES OPTIMUS')
                ->setCellValue('M1', 'WO REBOTE')
                ->setCellValue('N1', 'COMENTARIO')
                ->setCellValue('O1', 'TIPO TECNICO')
                ->setCellValue('P1', 'TIPO NOMBRE')
                ->setCellValue('Q1', 'FECHA REPORTE');


// Miscellaneous glyphs, UTF-8

        if ($storedTecnico->num_rows == 0) {
            //print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            $j = 0;
            $hoy = $fecha_reporte;
            $year = substr($hoy, 0, 4); //date("Y",$hoy);
            $mes = substr($hoy, 5, 2); //date("m",$hoy);
            $dia = substr($hoy, 8, 2); //date("d",$hoy);
            foreach ($storedTecnico->result() as $row) {
                $storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                foreach ($storedReporte->result() as $resultReporte) {
                    $j = 0;
                    $nombre_auxiliar = "";
                    foreach ($storedAuxiliar->result() as $row2) {
                        if ($resultReporte->codigo_auxiliar == $row2->codigo) {
                            $nombre_auxiliar = $row2->nombre;
                        }
                        $j = $j + 1;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($i + 2), $resultReporte->item)
                            ->setCellValue('B' . ($i + 2), $resultReporte->codigo_tecnico)
                            ->setCellValue('C' . ($i + 2), $row->nombre)
                            ->setCellValue('D' . ($i + 2), $resultReporte->codigo_auxiliar)
                            ->setCellValue('E' . ($i + 2), $nombre_auxiliar)
                            ->setCellValue('F' . ($i + 2), $resultReporte->hora_de_ingreso)
                            ->setCellValue('G' . ($i + 2), $resultReporte->hora_entrega_jobie)
                            ->setCellValue('H' . ($i + 2), $resultReporte->hora_desp_almacen)
                            ->setCellValue('I' . ($i + 2), $resultReporte->cantidad_de_wo)
                            ->setCellValue('J' . ($i + 2), $resultReporte->hora_llegada_primer_punto)
                            ->setCellValue('K' . ($i + 2), $resultReporte->cantidad_wo_t)
                            ->setCellValue('L' . ($i + 2), $resultReporte->activaciones_optimus)
                            ->setCellValue('M' . ($i + 2), $resultReporte->wo_rebote)
                            ->setCellValue('N' . ($i + 2), $resultReporte->comentario)
                            ->setCellValue('O' . ($i + 2), $row->tipo)
                            ->setCellValue('P' . ($i + 2), 'TECNICO')
                            ->setCellValue('Q' . ($i + 2), $resultReporte->fecha_reporte);

                    $arr[$i] = array(
                        'item' => $resultReporte->item,
                        'codigo_tecnico' => $resultReporte->codigo_tecnico,
                        'codigo_auxiliar' => $resultReporte->codigo_auxiliar,
                        'hora_de_ingreso' => $resultReporte->hora_de_ingreso,
                        'hora_entrega_jobie' => $resultReporte->hora_entrega_jobie,
                        'hora_desp_almacen' => $resultReporte->hora_desp_almacen,
                        'cantidad_de_wo' => $resultReporte->cantidad_de_wo,
                        'hora_llegada_primer_punto' => $resultReporte->hora_llegada_primer_punto,
                        'cantidad_wo_t' => $resultReporte->cantidad_wo_t,
                        'activaciones_optimus' => $resultReporte->activaciones_optimus,
                        'wo_rebote' => $resultReporte->wo_rebote,
                        'fecha_reporte' => $resultReporte->fecha_reporte,
                        'comentario' => $resultReporte->comentario,
                        'nombre_tecnico' => $row->nombre,
                        'nombre_auxiliar' => $nombre_auxiliar,
                        'tipo_tecnico' => $row->tipo,
                        'tipo_nombre' => "tecnico"
                    );
                }
                $i = $i + 1;
            }
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Registro' . $year . '' . $mes . '' . $dia . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        $objWriter->save('php://output');


        exit;
    }

    public function exportarTPorFecha() {

        $fecha_reporte_inicio = $_GET['fecha_reporte_inicio'];
        $fecha_reporte_fin = $_GET['fecha_reporte_fin'];
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        $arr = "";

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Eder Otiniano")
                ->setLastModifiedBy("Eder Otiniano")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ITEM')
                ->setCellValue('B1', 'CODIGO TECNICO')
                ->setCellValue('C1', 'NOMBRE TECNICO')
                ->setCellValue('D1', 'CODIGO AUXILIAR')
                ->setCellValue('E1', 'NOMBRE AUXILIAR')
                ->setCellValue('F1', 'HORA DE INGRESO')
                ->setCellValue('G1', 'HORA DE ENTRADA JOBIE')
                ->setCellValue('H1', 'HORA DE DESP ALMACEN')
                ->setCellValue('I1', 'CANTIDAD DE WO')
                ->setCellValue('J1', 'HORA DE LELGADA PRIMER PUNTO')
                ->setCellValue('K1', 'CANTIDAD WO T')
                ->setCellValue('L1', 'ACTIVACIONES OPTIMUS')
                ->setCellValue('M1', 'WO REBOTE')
                ->setCellValue('N1', 'COMENTARIO')
                ->setCellValue('O1', 'TIPO TECNICO')
                ->setCellValue('P1', 'TIPO NOMBRE')
                ->setCellValue('Q1', 'FECHA REPORTE');


// Miscellaneous glyphs, UTF-8

        if ($storedTecnico->num_rows == 0) {
            //print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            $j = 0;
            /* $hoy = $fecha_reporte;
              $year = substr($hoy, 0, 4); //date("Y",$hoy);
              $mes = substr($hoy, 5, 2); //date("m",$hoy);
              $dia = substr($hoy, 8, 2); //date("d",$hoy); */
            foreach ($storedTecnico->result() as $row) {
                //$storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                $storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and (fecha_reporte between '$fecha_reporte_inicio' and '$fecha_reporte_fin')");
                foreach ($storedReporte->result() as $resultReporte) {
                    $j = 0;
                    $nombre_auxiliar = "";
                    foreach ($storedAuxiliar->result() as $row2) {
                        if ($resultReporte->codigo_auxiliar == $row2->codigo) {
                            $nombre_auxiliar = $row2->nombre;
                        }
                        $j = $j + 1;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($i + 2), $resultReporte->item)
                            ->setCellValue('B' . ($i + 2), $resultReporte->codigo_tecnico)
                            ->setCellValue('C' . ($i + 2), $row->nombre)
                            ->setCellValue('D' . ($i + 2), $resultReporte->codigo_auxiliar)
                            ->setCellValue('E' . ($i + 2), $nombre_auxiliar)
                            ->setCellValue('F' . ($i + 2), $resultReporte->hora_de_ingreso)
                            ->setCellValue('G' . ($i + 2), $resultReporte->hora_entrega_jobie)
                            ->setCellValue('H' . ($i + 2), $resultReporte->hora_desp_almacen)
                            ->setCellValue('I' . ($i + 2), $resultReporte->cantidad_de_wo)
                            ->setCellValue('J' . ($i + 2), $resultReporte->hora_llegada_primer_punto)
                            ->setCellValue('K' . ($i + 2), $resultReporte->cantidad_wo_t)
                            ->setCellValue('L' . ($i + 2), $resultReporte->activaciones_optimus)
                            ->setCellValue('M' . ($i + 2), $resultReporte->wo_rebote)
                            ->setCellValue('N' . ($i + 2), $resultReporte->comentario)
                            ->setCellValue('O' . ($i + 2), $row->tipo)
                            ->setCellValue('P' . ($i + 2), 'TECNICO')
                            ->setCellValue('Q' . ($i + 2), $resultReporte->fecha_reporte);

                    $arr[$i] = array(
                        'item' => $resultReporte->item,
                        'codigo_tecnico' => $resultReporte->codigo_tecnico,
                        'codigo_auxiliar' => $resultReporte->codigo_auxiliar,
                        'hora_de_ingreso' => $resultReporte->hora_de_ingreso,
                        'hora_entrega_jobie' => $resultReporte->hora_entrega_jobie,
                        'hora_desp_almacen' => $resultReporte->hora_desp_almacen,
                        'cantidad_de_wo' => $resultReporte->cantidad_de_wo,
                        'hora_llegada_primer_punto' => $resultReporte->hora_llegada_primer_punto,
                        'cantidad_wo_t' => $resultReporte->cantidad_wo_t,
                        'activaciones_optimus' => $resultReporte->activaciones_optimus,
                        'wo_rebote' => $resultReporte->wo_rebote,
                        'fecha_reporte' => $resultReporte->fecha_reporte,
                        'comentario' => $resultReporte->comentario,
                        'nombre_tecnico' => $row->nombre,
                        'nombre_auxiliar' => $nombre_auxiliar,
                        'tipo_tecnico' => $row->tipo,
                        'tipo_nombre' => "tecnico"
                    );
                    $i = $i + 1;
                }
            }
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="RegistroEntreFechas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        $objWriter->save('php://output');


        exit;
    }

    public function exportarTecnico() {

        $codigo = $_GET['codigo_tiempo'];
        //$fecha_reporte_inicio = $_GET['fecha_reporte_inicio'];
        //$fecha_reporte_fin = $_GET['fecha_reporte_fin'];
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $storedTecnico = $this->db->query("SELECT * FROM pendiente where tipo='1' and codigo='$codigo'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        $arr = "";

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Eder Otiniano")
                ->setLastModifiedBy("Eder Otiniano")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'ITEM')
                ->setCellValue('B1', 'CODIGO TECNICO')
                ->setCellValue('C1', 'NOMBRE TECNICO')
                ->setCellValue('D1', 'CODIGO AUXILIAR')
                ->setCellValue('E1', 'NOMBRE AUXILIAR')
                ->setCellValue('F1', 'HORA DE INGRESO')
                ->setCellValue('G1', 'HORA DE ENTRADA JOBIE')
                ->setCellValue('H1', 'HORA DE DESP ALMACEN')
                ->setCellValue('I1', 'CANTIDAD DE WO')
                ->setCellValue('J1', 'HORA DE LELGADA PRIMER PUNTO')
                ->setCellValue('K1', 'CANTIDAD WO T')
                ->setCellValue('L1', 'ACTIVACIONES OPTIMUS')
                ->setCellValue('M1', 'WO REBOTE')
                ->setCellValue('N1', 'COMENTARIO')
                ->setCellValue('O1', 'TIPO TECNICO')
                ->setCellValue('P1', 'TIPO NOMBRE')
                ->setCellValue('Q1', 'FECHA REPORTE');


// Miscellaneous glyphs, UTF-8

        if ($storedTecnico->num_rows == 0) {
            //print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            $j = 0;
            /* $hoy = $fecha_reporte;
              $year = substr($hoy, 0, 4); //date("Y",$hoy);
              $mes = substr($hoy, 5, 2); //date("m",$hoy);
              $dia = substr($hoy, 8, 2); //date("d",$hoy); */
            foreach ($storedTecnico->result() as $row) {
                //$storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                $storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$codigo'");
                foreach ($storedReporte->result() as $resultReporte) {
                    $j = 0;
                    $nombre_auxiliar = "";
                    foreach ($storedAuxiliar->result() as $row2) {
                        if ($resultReporte->codigo_auxiliar == $row2->codigo) {
                            $nombre_auxiliar = $row2->nombre;
                        }
                        $j = $j + 1;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($i + 2), $resultReporte->item)
                            ->setCellValue('B' . ($i + 2), $resultReporte->codigo_tecnico)
                            ->setCellValue('C' . ($i + 2), $row->nombre)
                            ->setCellValue('D' . ($i + 2), $resultReporte->codigo_auxiliar)
                            ->setCellValue('E' . ($i + 2), $nombre_auxiliar)
                            ->setCellValue('F' . ($i + 2), $resultReporte->hora_de_ingreso)
                            ->setCellValue('G' . ($i + 2), $resultReporte->hora_entrega_jobie)
                            ->setCellValue('H' . ($i + 2), $resultReporte->hora_desp_almacen)
                            ->setCellValue('I' . ($i + 2), $resultReporte->cantidad_de_wo)
                            ->setCellValue('J' . ($i + 2), $resultReporte->hora_llegada_primer_punto)
                            ->setCellValue('K' . ($i + 2), $resultReporte->cantidad_wo_t)
                            ->setCellValue('L' . ($i + 2), $resultReporte->activaciones_optimus)
                            ->setCellValue('M' . ($i + 2), $resultReporte->wo_rebote)
                            ->setCellValue('N' . ($i + 2), $resultReporte->comentario)
                            ->setCellValue('O' . ($i + 2), $row->tipo)
                            ->setCellValue('P' . ($i + 2), 'TECNICO')
                            ->setCellValue('Q' . ($i + 2), $resultReporte->fecha_reporte);

                    $arr[$i] = array(
                        'item' => $resultReporte->item,
                        'codigo_tecnico' => $resultReporte->codigo_tecnico,
                        'codigo_auxiliar' => $resultReporte->codigo_auxiliar,
                        'hora_de_ingreso' => $resultReporte->hora_de_ingreso,
                        'hora_entrega_jobie' => $resultReporte->hora_entrega_jobie,
                        'hora_desp_almacen' => $resultReporte->hora_desp_almacen,
                        'cantidad_de_wo' => $resultReporte->cantidad_de_wo,
                        'hora_llegada_primer_punto' => $resultReporte->hora_llegada_primer_punto,
                        'cantidad_wo_t' => $resultReporte->cantidad_wo_t,
                        'activaciones_optimus' => $resultReporte->activaciones_optimus,
                        'wo_rebote' => $resultReporte->wo_rebote,
                        'fecha_reporte' => $resultReporte->fecha_reporte,
                        'comentario' => $resultReporte->comentario,
                        'nombre_tecnico' => $row->nombre,
                        'nombre_auxiliar' => $nombre_auxiliar,
                        'tipo_tecnico' => $row->tipo,
                        'tipo_nombre' => "tecnico"
                    );
                    $i = $i + 1;
                }
            }
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="RegistroTecnico.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        $objWriter->save('php://output');


        exit;
    }

    public function exportarAlertaTiempo() {
        $codigo = $_GET['codigo_tiempo'];
        //$fecha_reporte_inicio = $_GET['fecha_reporte_inicio'];
        //$fecha_reporte_fin = $_GET['fecha_reporte_fin'];
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        $fecha_actual = date("d/m/y h:i:s a");
        $fecha_limite = date("d/m/y h:i:s a", (strtotime("+" . $codigo . " Hours")));

        $storedTecnico = $this->db->query("SELECT `CUSTOMER ID` as CUSTOMERID,`WORK ORDER ID` as WORKORDERID,`WORK ORDER SERVICE ID` as WORKORDERSERVICEID,`REGISTERED DATE TIME` as REGISTEREDDT,SERVICIO,TIPO,STATUS FROM pendiente");
        //$storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Eder Otiniano")
                ->setLastModifiedBy("Eder Otiniano")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'CUSTOMER ID')
                ->setCellValue('B1', 'WORK ORDER ID')
                ->setCellValue('C1', 'WORK ORDER SERVICE ID')
                ->setCellValue('D1', 'STATUS')
                ->setCellValue('E1', 'REGISTERED DATE TIME')
                ->setCellValue('F1', 'TIPO')
                ->setCellValue('G1', 'SERVICIO')
                ->setCellValue('H1', 'FECHA');

// Miscellaneous glyphs, UTF-8

        if ($storedTecnico->num_rows == 0) {
            //print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            foreach ($storedTecnico->result() as $row) {
                $year = substr($row->REGISTEREDDT, 6, 4); //date("Y",$hoy);
                $mes = substr($row->REGISTEREDDT, 3, 2); //date("m",$hoy);
                $dia = substr($row->REGISTEREDDT, 0, 2);
                
                $fecha=date("d/m/y h:i:s a", strtotime($row->REGISTEREDDT));
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($i + 2), $row->CUSTOMERID)
                        ->setCellValue('B' . ($i + 2), $row->WORKORDERID)
                        ->setCellValue('C' . ($i + 2), $row->WORKORDERSERVICEID)
                        ->setCellValue('D' . ($i + 2), $row->STATUS)
                        ->setCellValue('E' . ($i + 2), $row->REGISTEREDDT)
                        ->setCellValue('F' . ($i + 2), $row->TIPO)
                        ->setCellValue('G' . ($i + 2), $row->SERVICIO)
                        ->setCellValue('H' . ($i + 2), $fecha);
                $i = $i + 1;
            }
        }


// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Alerta');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;
                    filename = "OrdenesPorVencer.xlsx"');
        header('Cache-Control: max-age = 0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');


        $objWriter->save('php://output');


                    exit;
                }
            }
?>