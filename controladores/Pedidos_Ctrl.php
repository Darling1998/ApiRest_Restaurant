<?php

class Pedidos_Ctrl
{
  public $M_Pedido = null;
  public $M_DetallePedido = null;
  public function __construct()
  {
    $this->M_Pedido = new M_Pedidos();
    $this->M_DetallePedido = new M_DetallePedidos();
  }


  public function nuevoPedido($f3) {
    $detallePedido =$f3->get('POST.data');
    $msg="";
    $idPedido=0;

    $pedido = new M_Pedidos();
    $pedido->idCliente = $f3->get('POST.idCliente');
    $pedido->subtotal = $f3->get('POST.subtotal');
    $pedido->iva = $f3->get('POST.iva');
    $pedido->total = $f3->get('POST.total');
    $pedido->idEstado = 1;
    // Grabar
    $pedido->save();
    $idPedido = $pedido->idPedido; 
    $msg = "Pedido creado con éxito";

    if($idPedido ===0){
        $msg = "Ha occurido un error al generar Pedido";
    }else{

        foreach ($detallePedido as $row) {
            $this->M_DetallePedido = new M_DetallePedidos();
            $this->M_DetallePedido->set('idPedido',$idPedido);
            $this->M_DetallePedido->set('idProducto',$row['id_producto']);
            $this->M_DetallePedido->set('cantidad',$row['cant']);
            //grabar
            $this->M_DetallePedido->save();
        }

    }
    echo json_encode([
        'mensaje' => $msg,
        'id'=>$idPedido,
    ]); 

    }

    public function listarPedidosxPersona($f3){
        $idCliente= $f3->get('PARAMS.idCliente');
        $cadenaSql = "SELECT p.idPedido, GROUP_CONCAT(dp.cantidad) AS cantidades, pr.nombre, p.idEstado,ep.descripcion
        FROM pedido p
        INNER JOIN detallepedido dp ON p.idPedido = dp.idPedido
        INNER JOIN productos pr ON dp.idProducto = pr.id_producto
        INNER JOIN estadopedido ep on p.idEstado= ep.idEstadoPedido
        WHERE p.idCliente =".$idCliente."
        GROUP BY p.idPedido, pr.nombre;"; 
        $items=$f3->DB->exec($cadenaSql);

        foreach ($items as $item) {
            $idPedido = $item['idPedido'];
            $idEstado = $item['idEstado'];
            $estadoPedido = $item['descripcion'];
            $cantidades = $item['cantidades'];
            $nombre = $item['nombre'];
        
            if (!isset($pedidosAgrupados[$idPedido])) {
                $pedidosAgrupados[$idPedido] = array(
                    'idPedido' => $idPedido,
                    'estadoPedido' => $estadoPedido,
                    'idEstado' => $idEstado,
                    'productos' => array()
                );
            }
        
            $pedidosAgrupados[$idPedido]['productos'][] = array(
                'cantidades' => $cantidades,
                'nombre' => $nombre,
            );
        }
        
        $resultado = array_values($pedidosAgrupados); // Obtener solo los valores sin las claves
        
        echo json_encode([
            'data' => [
                'info' => $resultado,
            ]
        ]);
        
    }

    //listar diferentes tipos de estados de un pedido
    public function listarEstados($f3){ 
        $cadenaSql = "SELECT * FROM estadopedido"; 
        $items=$f3->DB->exec($cadenaSql);
        echo json_encode([
           'cantidad'=>count($items),
           'data'=>[
               'info'=> $items
           ]
        ]); 
    }

    //lista pedidos para cocina
    public function listarPedidosparacocina($f3){
        $cadenaSql = "SELECT p.idPedido, GROUP_CONCAT(dt.cantidad) AS cantidades,pr.nombre as nombre, p.idEstado as Estado  FROM pedido p
        INNER JOIN detallepedido dt on p.idPedido=dt.idPedido
        inner JOIN productos pr on pr.id_producto=dt.idProducto
        WHERE p.idEstado=1 
        GROUP BY p.idPedido, pr.nombre;"; 
        $items=$f3->DB->exec($cadenaSql);

        $pedidosAgrupados = array();

        foreach ($items as $item) {
            $idPedido = $item['idPedido'];
            $cantidades = $item['cantidades'];
            $nombre = $item['nombre'];
        
            if (!isset($pedidosAgrupados[$idPedido])) {
                $pedidosAgrupados[$idPedido] = array();
            }
        
            $pedidosAgrupados[$idPedido][] = array(
                'cantidades' => $cantidades,
                'nombre' => $nombre,
            );
        }
        
        $resultado = array();
        
        foreach ($pedidosAgrupados as $idPedido => $productos) {
            $resultado[] = array(
                'idPedido' => $idPedido,
                'productos' => $productos,
            );
        }
        

        echo json_encode([
           'data'=>[
               'info'=> $resultado,
           ]
        ]); 

    }

        //lista pedidos para delivery
    public function listarPedidosparaDelivery($f3){
        $cadenaSql = "SELECT p.idPedido, GROUP_CONCAT(dt.cantidad) AS cantidades,pr.nombre as nombre, p.idEstado as Estado,p.latitud,p.longitud  FROM pedido p
        INNER JOIN detallepedido dt on p.idPedido=dt.idPedido
        inner JOIN productos pr on pr.id_producto=dt.idProducto
        WHERE p.idEstado=2 
        GROUP BY p.idPedido, pr.nombre;"; 
        $items=$f3->DB->exec($cadenaSql);

        $pedidosAgrupados = array();

        foreach ($items as $item) {
            $idPedido = $item['idPedido'];
            $latitud = $item['latitud'];
            $longitud = $item['longitud'];
            $cantidades = $item['cantidades'];
            $nombre = $item['nombre'];
        
            if (!isset($pedidosAgrupados[$idPedido])) {
                $pedidosAgrupados[$idPedido] = array();
            }
        
            $pedidosAgrupados[$idPedido][] = array(
                'cantidades' => $cantidades,
                'nombre' => $nombre,
                'latitud' => $latitud,
                'longitud' => $longitud,
            );
        }
        
        $resultado = array();
        
        foreach ($pedidosAgrupados as $idPedido => $productos) {
            $latitud = $productos[0]['latitud'];
            $longitud = $productos[0]['longitud'];
        
            $resultado[] = array(
                'idPedido' => $idPedido,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'productos' => $productos,
            );
        }
        

        echo json_encode([
            'data'=>[
                'info'=> $resultado,
            ]
        ]); 

    }
    


    public function actualizarEstadoPedido($f3){
        $id_pedido=$f3->get('POST.idPedido');
        $this->M_Pedido->load(['idPedido = ?', $id_pedido]);
        
        if($this->M_Pedido->loaded() > 0) {  
            $this->M_Pedido->set('idEstado', 2);
            $this->M_Pedido->save(); 
            $msj = "Pedido Actualizado";
        }
  
        echo json_encode([
            'msj'=>$msj
        ]);  
    }

    public function actualizarEstadoPedidoDelivery($f3){
        $id_pedido=$f3->get('POST.idPedido');
        $this->M_Pedido->load(['idPedido = ?', $id_pedido]);
        
        if($this->M_Pedido->loaded() > 0) {  
            $this->M_Pedido->set('idEstado', 3);
            $this->M_Pedido->save(); 
            $msj = "Pedido Actualizado";
        }
  
        echo json_encode([
            'msj'=>$msj
        ]);  
    }
}

