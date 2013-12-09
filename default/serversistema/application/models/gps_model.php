<?php

class gps_model extends CI_Model {

    var $itoa64;
    var $iteration_count_log2;
    var $portable_hashes;
    var $random_state;

    function __construct() {
        parent::__construct();
        //$this->load->model('PasswordHash');
        //$this->load->model('feed_model');
    }

    public function setT($item, $codigo_tecnico, $codigo_auxiliar, $hora_de_ingreso, $hora_entrega_jobie, $hora_desp_almacen, $cantidad_de_wo, $hora_llegada_primer_punto, $cantidad_wo_t, $activaciones_optimus, $wo_rebote, $fecha_reporte, $comentario) {

        $sql = "UPDATE  reporte set 
                codigo_tecnico='$codigo_tecnico', 
                codigo_auxiliar='$codigo_auxiliar' ,
                hora_de_ingreso='$hora_de_ingreso' ,
                hora_entrega_jobie='$hora_entrega_jobie' ,
                hora_desp_almacen='$hora_desp_almacen' ,
                cantidad_de_wo='$cantidad_de_wo' ,
                hora_llegada_primer_punto='$hora_llegada_primer_punto' ,
                cantidad_wo_t='$cantidad_wo_t' ,
                activaciones_optimus='$activaciones_optimus' ,
                wo_rebote='$wo_rebote' ,
                fecha_reporte='$fecha_reporte' ,
                comentario='$comentario' 
                    where item=$item";
        $result = $this->db->query($sql);
        $data["boleano"] = true;
        return $data;
    }

    public function getT() {
        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        if ($storedTecnico->num_rows == 0) {
            print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            $j = 0;
            $hoy = date("Y-m-d");
            $year = date("Y");
            $mes = date("m");
            $dia = date("d");
            foreach ($storedTecnico->result() as $row) {


                $storedReporte = $this->db->query("SELECT * FROM reporte where codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                if ($storedReporte->num_rows == 0) {
                    $sql = "insert into reporte set 
                            codigo_tecnico='$row->codigo', 
                            codigo_auxiliar='' ,
                            hora_de_ingreso='' ,
                            hora_entrega_jobie='' ,
                            hora_desp_almacen='' ,
                            cantidad_de_wo='' ,
                            hora_llegada_primer_punto='' ,
                            cantidad_wo_t='' ,
                            activaciones_optimus='' ,
                            wo_rebote='' ,
                            fecha_reporte='$hoy' ,
                            comentario=''";
                    $result = $this->db->query($sql);
                }
            }
            foreach ($storedTecnico->result() as $row) {
                $storedReporte = $this->db->query("SELECT * FROM reporte where codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                foreach ($storedReporte->result() as $resultReporte) {
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
                        'tipo_tecnico' => $row->tipo,
                        'tipo_nombre' => "tecnico"
                    );
                }
                $i = $i + 1;
            }
            foreach ($storedAuxiliar->result() as $row) {
                $auxiliar[$j] = array(
                    'codigo_auxiliar' => $row->codigo,
                    'nombre_auxiliar' => $row->nombre,
                    'tipo_auxiliar' => $row->tipo,
                    'tipo_nombre' => "axuliar"
                );
                $j = $j + 1;
            }
            $data["tecnicos"] = $arr;
            $data["auxiliar"] = $auxiliar;
        }
        return $data;
    }

    public function getTFecha($fecha_reporte) {
        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        $arr = "";
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
            foreach ($storedAuxiliar->result() as $row) {
                $auxiliar[$j] = array(
                    'codigo_auxiliar' => $row->codigo,
                    'nombre_auxiliar' => $row->nombre,
                    'tipo_auxiliar' => $row->tipo,
                    'tipo_nombre' => "axuliar"
                );
                $j = $j + 1;
            }
            if ($arr == "") {
                $data["tecnicos"] = $arr;
                $data["boleanodatos"] = "false";
                $data["auxiliar"] = $auxiliar;
            } else {
                $data["tecnicos"] = $arr;
                $data["boleanodatos"] = "true";
                $data["auxiliar"] = $auxiliar;
            }
        }
        return $data;
    }

    public function getTPorFecha($fecha_reporte_inicio, $fecha_reporte_fin) {
        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        $storedAuxiliar = $this->db->query("SELECT * FROM tecnico where tipo='2'");
        $arr = "";
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
                $storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and (fecha_reporte between '$fecha_reporte_inicio' and '$fecha_reporte_fin')");
                //$storedReporte = $this->db->query("SELECT * FROM reporte,tecnico where reporte.codigo_tecnico=tecnico.codigo and codigo_tecnico='$row->codigo' and Year(fecha_reporte) = '$year' AND Month(fecha_reporte) = '$mes' and Day(fecha_reporte)='$dia' ");
                foreach ($storedReporte->result() as $resultReporte) {
                    $j = 0;
                    $nombre_auxiliar = "";
                    foreach ($storedAuxiliar->result() as $row2) {
                        if ($resultReporte->codigo_auxiliar == $row2->codigo) {
                            $nombre_auxiliar = $row2->nombre;
                        }
                        $j = $j + 1;
                    }
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
            foreach ($storedAuxiliar->result() as $row) {
                $auxiliar[$j] = array(
                    'codigo_auxiliar' => $row->codigo,
                    'nombre_auxiliar' => $row->nombre,
                    'tipo_auxiliar' => $row->tipo,
                    'tipo_nombre' => "axuliar"
                );
                $j = $j + 1;
            }
            if ($arr == "") {
                $data["tecnicos"] = $arr;
                $data["boleanodatos"] = "false";
                $data["auxiliar"] = $auxiliar;
            } else {
                $data["tecnicos"] = $arr;
                $data["boleanodatos"] = "true";
                $data["auxiliar"] = $auxiliar;
            }
        }
        return $data;
    }

    public function getTecnico() {
        $storedTecnico = $this->db->query("SELECT * FROM tecnico where tipo='1'");
        if ($storedTecnico->num_rows == 0) {
            print_r($storedTecnico);
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            foreach ($storedTecnico->result() as $row) {
                $arr[$i] = array(
                    'codigo_tecnico' => $row->codigo,
                    'nombre_tecnico' => $row->nombre,
                    'tipo_tecnico' => $row->tipo,
                    'tipo_nombre' => "tecnico"
                );
                $i = $i + 1;
            }
            /* foreach ($storedAuxiliar->result() as $row) {
              $auxiliar[$j] = array(
              'codigo_auxiliar' => $row->codigo,
              'nombre_auxiliar' => $row->nombre,
              'tipo_auxiliar' => $row->tipo,
              'tipo_nombre' => "axuliar"
              );
              $j = $j + 1;
              } */
            $data["boleanodatos"] = "true";
            $data["tecnicos"] = $arr;
        }
        return $data;
    }

    public function getSIN($fecha_creacion, $ibs) {
        $storedTecnico = $this->db->query("SELECT * FROM registro where CodCliente='$ibs' order by FechaCreacion");
        if ($storedTecnico->num_rows() <= 0) {
            //print_r($storedTecnico);
            $data["valor"] = "";
            $data["valor7"] = "";
            $data["boleanodatos"] = "false";
            $data["antecesor"] = "";
            //$data["markers"]=$stored[0]->user_pass;
        } else {
            $i = 0;
            foreach ($storedTecnico->result() as $row) {
                $datetime1 = new DateTime($fecha_creacion);
                //print_r($fecha_creacion);
                //$datetime2=DateTime.ParseExact($row->FechaAtencion, "dd/mm/yyyy hh:mm", null);

                $year = substr($row->FechaAtencion, 6, 4); //date("Y",$hoy);
                $mes = substr($row->FechaAtencion, 3, 2); //date("m",$hoy);
                $dia = substr($row->FechaAtencion, 0, 2); //date("d",$hoy);
                $hora = substr($row->FechaAtencion, 11, 5); //date("d",$hoy);

                $datetime2 = new DateTime($year . "-" . $mes . "-" . $dia . " " . $hora);

                $interval = $datetime2->diff($datetime1);
                $interval2 = $interval->format('%R%a');
                //print_r($interval2);
                $arr[$i] = array(
                    'FechaCreacion' => $row->FechaCreacion,
                    'FechaAtencion' => $row->FechaAtencion,
                    'CodCliente' => $row->CodCliente,
                    'CodServicio2' => $row->CodServicio2,
                    'CodTipoServicio' => $row->CodTipoServicio,
                    'NomCatServicio' => $row->NomCatServicio,
                    'Barrio' => $row->Barrio,
                    'OPERADOR' => $row->OPERADOR
                );
                if ($interval2 <= 7) {
                    $arr3[$i] = "true";
                    $arr2[$i] = "false";
                } else {
                    if ($interval2 <= 30) {
                        $arr2[$i] = "true";
                        $arr3[$i] = "false";
                    } else {
                        $arr2[$i] = "false";
                        $arr3[$i] = "false";
                    }
                }

                $i = $i + 1;
            }
            $data["valor"] = $arr2;
            $data["valor7"] = $arr3;
            $data["boleanodatos"] = "true";
            $data["antecesor"] = $arr;
        }
        return $data;
    }

    public function setHora($codigo) {
        date_default_timezone_set("America/Lima");
        $hoy = date("Y-m-d");
        $fecha_hoy = date("Y-m-d H:i:s");
        $fecha_presentacion = date("d/m/Y h:i:s A");
        $year = date("Y");
        $mes = date("m");
        $dia = date("d");
        $directorio = $dia.$mes.$year;
        $storedRegistro = $this->db->query("SELECT * FROM registro where cod_tecnico='$codigo' and fecha_registro='$hoy'");
        if ($storedRegistro->num_rows() <= 0) {
            $storedTecnico = $this->db->query("SELECT * FROM responsable where cod_responsable='$codigo'");
            if ($storedTecnico->num_rows() <= 0) {
                $datos["success"] = "false";
                $datos["error"] = "codigo";
                $datos["hora"] = "";
                $datos["opcion"] = "";
                $datos["presentar"] = "";
            } else {
                $datos["success"] = "true";
                $datos["error"] = "codigo";
                $datos["hora"] = $fecha_hoy;
                $datos["opcion"] = "llegada";
                $datos["presentar"] = $fecha_presentacion;
                $sql = "insert into registro set 
                        cod_tecnico='$codigo', 
                        hora_llegada='$fecha_hoy',
                        fecha_registro='$hoy',
                        img_tecnico='".$_SERVER["DOCUMENT_ROOT"]."/asistencia/imagenes/".$directorio."/".$codigo.".png"."',
                        hora_salida=''" ;
                $result = $this->db->query($sql);
            }
        } else {
            $datos["success"] = "true";
            $datos["error"] = "codigo";
            $datos["hora"] = $fecha_hoy;
            $datos["opcion"] = "salida";
            $datos["presentar"] = $fecha_presentacion;
            
            foreach ($storedRegistro->result() as $row) {
                if($row->hora_salida=="0000-00-00 00:00:00"){
                    $sql = "update registro set 
                        hora_salida='$fecha_hoy' where cod_tecnico='$codigo' and fecha_registro='$hoy'" ;
                    $result = $this->db->query($sql);
                }else{
                    $datos["opcion"] = "salle";
                }
            }
        }
        return $datos;
    }

}

?>