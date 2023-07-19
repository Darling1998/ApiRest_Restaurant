<?php
class Usuarios_Ctrl
{
  public $M_Usuario = null;
 
  public function __construct()
  {
    $this->M_Usuario = new M_Usuarios();
  }

  public function login($f3)
  {
    $username = $f3->get('POST.username');
    $contrasena = $f3->get('POST.contrasena');
    $estado = "A";
    $msg = "";
    $items = array();
    $id_usuario = 0;
    $id_perfil = 0;
    $this->M_Usuario->load(['username=? AND contrasena=?', $username, $contrasena]);
    if ($this->M_Usuario->loaded() > 0) {
      $id_usuario = $this->M_Usuario->get('id_usuario');
      $id_perfil = $this->M_Usuario->get('id_perfil');
      $msg = "Usuario encontrado";
      $items = $this->M_Usuario->cast();
    } else {
      $id_usuario = 0;
      $msg = "No existe el Usuario con estos datos";

    }
    echo json_encode([
      'mensaje' => $msg,
      'idUsuario' => $id_usuario,
      'idPerfil' => $id_perfil,
      'info' => ['items' => $items],
    ]);

  }


  
   //registro Cliente
   public function nuevoUsuarioCliente($f3){
    $username = $f3->get('POST.username');
    $cedula = $f3->get('POST.cedula');
    $id=0;
    $msg="";
    $usuario=new M_Usuarios();
    $usuario->load(['cedula=?  AND username=?',$cedula,$username]);
        if($usuario->loaded()>0){
            $msg="Cédula o nombre de usuario ya registrados";
        }else{
            $this->M_Usuario->set('cedula',$f3->get('POST.cedula'));
            $this->M_Usuario->set('username',$f3->get('POST.username'));
            $this->M_Usuario->set('nombres',$f3->get('POST.nombres'));
            $this->M_Usuario->set('apellidos',$f3->get('POST.apellidos'));
            $this->M_Usuario->set('contrasena',$f3->get('POST.contrasena'));
            $this->M_Usuario->set('telefono',$f3->get('POST.telefono'));
            $this->M_Usuario->set('id_perfil',$f3->get('POST.id_perfil'));
            //grabar
            $this->M_Usuario->save();
            $id=$this->M_Usuario->get('id_usuario');
            $msg="Usuario creado con éxito";
        }
        echo json_encode([
            'mensaje'=>$msg,
            'info'=>[
                'id'=>$id
            ]
        ]);
    }


    public function actualizarUsuario($f3){
      $id_usuario=$f3->get('PARAMS.id_usuario');
      $cadenaSql = "SELECT * FROM usuarios WHERE id_usuario='".$id_usuario."'";
      $items=$f3->DB->exec($cadenaSql);
      echo json_encode([
         'cantidad'=>count($items),
         'data'=>[
             'info'=> $items
         ]
      ]); 

  }

   public function actualizarInfoUsuario($f3){
      $msj="";
      $id=0;
      $id_usuario=$f3->get('POST.id_usuario');
      $nombres=$f3->get('POST.nombres');
      $apellidos=$f3->get('POST.apellidos');
      $cedula=$f3->get('POST.cedula');
      $contrasena=$f3->get('POST.contrasena');
      $username=$f3->get('POST.username');
      $telefono=$f3->get('POST.telefono');
      
      $this->M_Usuario->load(['id_usuario = ?', $id_usuario]);
      
      if($this->M_Usuario->loaded() > 0) {  
          $this->M_Usuario->set('cedula', $cedula);
          $this->M_Usuario->set('username', $username);
          $this->M_Usuario->set('nombres', $nombres);
          $this->M_Usuario->set('apellidos', $apellidos);
          $this->M_Usuario->set('contrasena', $contrasena);
          $this->M_Usuario->set('telefono', $telefono);
          $this->M_Usuario->save(); 
          $id=$this->M_Usuario->get('id_usuario');
          $msj = "Perfil Actualizado Correctamente";
      }

      echo json_encode([
          'msj'=>$msj,
          'data'=> $this->M_Usuario->cast(),
          'id'=>$id
      ]);  
  } 


}



