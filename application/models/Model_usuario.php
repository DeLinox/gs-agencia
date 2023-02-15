<?php 
  class Model_usuario extends CI_Model{
    public function __construct(){
      parent::__construct();
            $this->load->database();
    }
   
    function getUsuario($where=0){
      if($where!=0){
        $this->db->where($where);
        $consulta=$this->db->get('usuario');
        // $consulta = $this->db->query($sql);
        if($consulta->num_rows()> 0){

          $consulta = $consulta->row();
         
          return $consulta;
        }
        else{ 
          return FALSE;
         
        }
      }else{
        $this->db->select("usua_id,usua_user");
        $this->db->from("usuario");
        $consulta=$this->db->get();
        if($consulta->num_rows()> 0){
          return $consulta->result();
        }
        else{ 
          return FALSE;
        }
      }
    }
    
    //  function guargar_edit_registro($datas,$id){
      
    //   $this->db->trans_start();
    //   $this->db->where('usua_id', $id);
    //   $this->db->update('usuario', $datas); 
    //   // $id = $this->db->insert_id();
    //   $this->db->trans_complete();
    //   if ($this->db->trans_status() === FALSE)
    //   {
    //       return FALSE;
    //   }
    //   else
    //   {
    //       return TRUE;
    //   }
       
    // }
    // function guargar_registro($datas){
    //   if(isset($datas)){
    //         $this->db->trans_start();
    //         $this->db->set($datas); 
    //         $this->db->insert('usuario');
    //         // $id = $this->db->insert_id();
    //         $this->db->trans_complete();
    //         if ($this->db->trans_status() === FALSE)
    //         {
    //             return FALSE;
    //         }
    //         else
    //         {
    //             return TRUE;
    //         }
    //     }
    //     else{
    //         return FALSE;
    //     }
    // }

    function perfil($where){
        $this->db->select("*",false);
        $this->db->from("usuario");
        $this->db->where($where);
        $consulta=$this->db->get();
        // $consulta = $this->db->query($sql);
        if($consulta->num_rows()> 0){

          $consulta = $consulta->row();
         
          return $consulta;
        }
        else{ 
          return FALSE;
         
        }
    }
    function mensajes($id1=false,$id2=false){
        $consulta = $this->db->query("SELECT 
          mens_id,
          mens_user_ini, 
          mens_fechareg, 
          o_ini.ofic_nombre as oficina_ini,
          o_fin.ofic_nombre as oficina_fin,
          u_ini.usua_nombres as usuario_ini,
          u_ini.usua_apellidos as usuario_ini_apellidos,
          u_fin.usua_nombres as usuario_fin,
          mens_mensaje,
          mens_archivo
                FROM mensaje
                JOIN oficina o_ini ON o_ini.ofic_id=mens_ofic_ini
                JOIN oficina o_fin ON o_fin.ofic_id=mens_ofic_fin
                JOIN usuario u_ini ON u_ini.usua_id=mens_user_ini
                JOIN usuario u_fin ON u_fin.usua_id=mens_user_fin
                WHERE 
                (mens_user_ini = $id1 AND
                mens_user_fin = $id2)OR

                 (mens_user_ini = $id2
                AND mens_user_fin = $id1)
                ORDER BY mens_fechareg DESC");
        if($consulta->num_rows()> 0){

          $consulta = $consulta->result_array();
         
          return $consulta;
        }
        else{ 
          return FALSE;
         
        }
    }
    // function check_captcha($captcha){

    //   $this->db->where($captcha);
    //   $this->db->order_by("captcha_id", "desc");
    //   $this->db->limit(1); 
    //   // $this->db->from('usuario');
    //   $consulta=$this->db->get('captcha');

    //   if($consulta->num_rows()> 0){
    //     return TRUE;
    //   }
    //   else
    //   {
    //     return FALSE;
    //   }
    // }
    
    // function existe($datos,$tabla){
    //   $this->db->where($datos);
    //   // $this->db->from('usuario');
    //   $consulta=$this->db->get($tabla);
    //   // $consulta = $this->db->query($sql);
    //   if($consulta->num_rows()> 0){

    //     return TRUE;
    //   }
    //   else{ 
    //     return FALSE;
       
    //   }
    // } 


  }
