<?php 
  class Model_login extends CI_Model{
    public function __construct(){
      parent::__construct();
            $this->load->database();
    }
   
    function login($datas,$init=FALSE){
      $this->db->where($datas);
      // $this->db->from('usuario');
      $consulta=$this->db->get('usuario');
      // $consulta = $this->db->query($sql);
      if($consulta->num_rows()> 0){

        $consulta = $consulta->row();
        $response = array("id" => $consulta->usua_id,
                          "intento" => $consulta->usua_intento
                          );
        if($init==TRUE){
         $datos_usa = array("authorized" => $consulta->usua_id,
                              "username" => ucwords(strtolower($consulta->usua_nombres)));
          if($consulta->usua_priv_administrador=="1")
            $datos_usa=array_merge($datos_usa,array("authorizedadmin"=>"1"));

          $this->session->set_userdata($datos_usa);
          return $response;
        }
        return $response;
      }
      else{ 
        return FALSE;
       
      }
    }
    // function check_captcha($where){

    //   $this->db->where($where);
    //   $this->db->limit(1); 
    //   $consulta=$this->db->get('captcha');

    //   if($consulta->num_rows()> 0){
    //     return TRUE;
    //   }
    //   else
    //   {
    //     return FALSE;
    //   }
    // }
    
    function guargar_registro($datas){
      if(isset($datas)){
            $this->db->trans_start();
            $this->db->set($datas); 
            $this->db->insert('usuario');
            // $id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }
        else{
            return FALSE;
        }
    }
     function guargar_edit_registro($datas,$id){
      
      $this->db->trans_start();
      $this->db->where('usua_id', $id);
      $this->db->update('usuario', $datas); 
      // $id = $this->db->insert_id();
      $this->db->trans_complete();
      if ($this->db->trans_status() === FALSE)
      {
          return FALSE;
      }
      else
      {
          return TRUE;
      }
       
    }
    
   


  }
