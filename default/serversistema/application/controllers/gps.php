<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gps extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('gps_model');
    }

    /* public function insertarTecnico()
      {
      $codigo=$_GET["codigo"];
      $nombre=$_GET["nombre"];
      $latitude=$_GET["latitude"];
      $longitude=$_GET["longitude"];
      $validacionlogeo=$this->gps_model->setTecnico($codigo,$nombre,$latitude,$longitude);
      $datos['valor']=$validacionlogeo["valor"];
      $datos['success']=$validacionlogeo["success"];
      $tmp_array['success'] = true;
      $tmp_array['error_code'] = 0;
      $tmp_array['error_msg'] = '';

      responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
      }
      public function obtenerTecnico()
      {
      $validacionlogeo=$this->gps_model->getTecnico();
      $datos["markers"]=$validacionlogeo["markers"];
      $tmp_array['success'] = true;
      $tmp_array['error_code'] = 0;
      $tmp_array['error_msg'] = '';
      responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
      } */

    public function obtenerT() {
        $validacionlogeo = $this->gps_model->getT();
        $datos["tecnicos"] = $validacionlogeo["tecnicos"];
        $datos["auxiliar"] = $validacionlogeo["auxiliar"];
        $datos["success"] = "true";
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }

    public function obtenerTFecha() {
        $fecha_reporte = $_GET['fecha_reporte'];
        $validacionlogeo = $this->gps_model->getTFecha($fecha_reporte);
        $datos["tecnicos"] = $validacionlogeo["tecnicos"];
        $datos["auxiliar"] = $validacionlogeo["auxiliar"];
        $datos["boleanodatos"] = $validacionlogeo["boleanodatos"];
        $datos["success"] = "true";
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }

    public function obtenerTPorFecha() {
        $fecha_reporte_inicio = $_GET['fecha_reporte_inicio'];
        $fecha_reporte_fin = $_GET['fecha_reporte_fin'];
        $validacionlogeo = $this->gps_model->getTPorFecha($fecha_reporte_inicio,$fecha_reporte_fin);
        $datos["tecnicos"] = $validacionlogeo["tecnicos"];
        $datos["auxiliar"] = $validacionlogeo["auxiliar"];
        $datos["boleanodatos"] = $validacionlogeo["boleanodatos"];
        $datos["success"] = "true";
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }

    public function actualizarTecnico() {
        $item = $_GET['item'];
        $codigo_tecnico = $_GET['codigo_tecnico'];
        $codigo_auxiliar = $_GET['codigo_auxiliar'];
        $hora_de_ingreso = $_GET['hora_de_ingreso'];
        $hora_entrega_jobie = $_GET['hora_entrega_jobie'];
        $hora_desp_almacen = $_GET['hora_desp_almacen'];
        $cantidad_de_wo = $_GET['cantidad_de_wo'];
        $hora_llegada_primer_punto = $_GET['hora_llegada_primer_punto'];
        $cantidad_wo_t = $_GET['cantidad_wo_t'];
        $activaciones_optimus = $_GET['activaciones_optimus'];
        $wo_rebote = $_GET['wo_rebote'];
        $fecha_reporte = date("Y-m-d");
        $comentario = $_GET['comentario'];
        $validacionlogeo = $this->gps_model->setT($item, $codigo_tecnico, $codigo_auxiliar, $hora_de_ingreso, $hora_entrega_jobie, $hora_desp_almacen, $cantidad_de_wo, $hora_llegada_primer_punto, $cantidad_wo_t, $activaciones_optimus, $wo_rebote, $fecha_reporte, $comentario);
        if ($validacionlogeo["boleano"]) {
            $datos["success"] = "true";
        } else {
            $datos["success"] = "false";
        }

        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }
    public function obtenerTecnico() {
        $validacionlogeo = $this->gps_model->getTecnico();
        $datos["tecnicos"] = $validacionlogeo["tecnicos"];
        $datos["boleanodatos"] = $validacionlogeo["boleanodatos"];
        $datos["success"] = "true";
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }
    public function consultarSIN() {
        $fecha_creacion = $_GET['fecha_creacion'];
        $ibs = $_GET['ibs'];
        $validacionlogeo = $this->gps_model->getSIN($fecha_creacion,$ibs);
        $datos["antecesor"] = $validacionlogeo["antecesor"];
        $datos["valor"] = $validacionlogeo["valor"];
        $datos["valor7"] = $validacionlogeo["valor7"];
        $datos["boleanodatos"] = $validacionlogeo["boleanodatos"];
        $datos["success"] = "true";
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }
    public function guardarImagen() {
        
        $cod_tecnico = $_POST['codigo'];
        $data = $_POST['base64data'];
        $decoded=base64_decode($data);

        file_put_contents($cod_tecnico.'.png',$data);
        
        
        $datos["success"] = 0;
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        //responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
       // file_put_contents($_SERVER["DOCUMENT_ROOT"]."/asistencia/imagenes/"+$cod_tecnico+".png", $data);
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }
    public function guardarHora() {
        
        $cod_tecnico = $_GET['codigo'];

        $validacionlogeo = $this->gps_model->setHora($cod_tecnico);
        
        
        $datos["success"] = $validacionlogeo["success"];
        $datos["error"] = $validacionlogeo["error"];
        $datos["hora"] = $validacionlogeo["hora"];
        $datos["opcion"] = $validacionlogeo["opcion"];
        $datos["presentar"] = $validacionlogeo["presentar"];
        $tmp_array['success'] = true;
        $tmp_array['error_code'] = 0;
        $tmp_array['error_msg'] = '';
        //responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
       // file_put_contents($_SERVER["DOCUMENT_ROOT"]."/asistencia/imagenes/"+$cod_tecnico+".png", $data);
        responseCode($tmp_array['success'], $tmp_array['error_msg'], $datos, $tmp_array['error_code']);
    }
}

?>