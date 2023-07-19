<?php

class Productos_Ctrl
{
  public $M_Producto = null;
 
  public function __construct()
  {
    $this->M_Producto = new M_Productos();
  }


  //Lista Categorias almuerzos o bebidas
  public function listarCategorias($f3){
    $cadenaSql = "SELECT * FROM categorias"; 
    $items=$f3->DB->exec($cadenaSql);
    echo json_encode([
       'cantidad'=>count($items),
       'data'=>[
           'info'=> $items
       ]
    ]); 
  }

  
  public function listarAlmuerzos($f3){
    $id_categoria = 1;
    $cadenaSql = "SELECT * FROM productos WHERE id_categoria=".$id_categoria; 
    $items=$f3->DB->exec($cadenaSql);
    echo json_encode([
       'cantidad'=>count($items),
       'data'=>[
           'info'=> $items
       ]
    ]); 
  }

  public function listarBebidas($f3){
    $id_categoria = 2;
    $cadenaSql = "SELECT * FROM productos WHERE id_categoria=".$id_categoria; 
    $items=$f3->DB->exec($cadenaSql);
    echo json_encode([
       'cantidad'=>count($items),
       'data'=>[
           'info'=> $items
       ]
    ]); 
  }
  
  public function nuevoProducto($f3) {
    $nombre = $f3->get('POST.nombreMenu');
    $id = 0;
    $msg = "";
    $producto = new M_Productos();
    $producto->load(['nombre = ?', $nombre]);

    if ($producto->loaded()) {
        $msg = "Nombre de producto ya registrado";
    } else {


      $imageData = $f3->get('POST.foto');

      // Generar un nombre único para la imagen
      $imageName = uniqid() . '.jpg';
  
      // Directorio de destino para guardar la imagen
      $uploadDirectory = 'uploads/';
  
      // Ruta completa del archivo de destino
      $destinationPath = $uploadDirectory . $imageName;
  
      // Guardar la imagen en el servidor
      file_put_contents($destinationPath, base64_decode($imageData));


        $producto->nombre = $f3->get('POST.nombreMenu');
        $producto->descripcion = $f3->get('POST.descripcion');
        $producto->precio = $f3->get('POST.precio');
        $producto->foto = $destinationPath;
        $producto->id_categoria = $f3->get('POST.id_categoria');
        // Grabar
        $producto->save();
        $id = $producto->id_categoria;
        $msg = "Producto creado con éxito"; 
    }  

    echo json_encode([
        'mensaje' => $imageData,
        'id'=>$destinationPath,
        'data' => [
            'producto' => $producto->cast()
        ] 
    ]); 
}

  public function listarProductosVendidos($f3){
    $cadenaSql = "SELECT p.nombre AS nombre_producto, SUM(dp.cantidad) AS total_vendido
    FROM productos p
    INNER JOIN detallepedido dp ON p.id_producto = dp.idProducto
    GROUP BY p.nombre
    ORDER BY total_vendido DESC
    LIMIT 3"; 
    $items=$f3->DB->exec($cadenaSql);

    $categories = [];
    $seriesData = [];

    foreach ($items as $item) {
      $categories[] = $item['nombre_producto']; 
      $seriesData[] = $item['total_vendido']; 
    }
    

    // Construir el array de opciones del gráfico
    $options = [
      'chart' => [
        'type' => 'bar'
      ],
      'series' => [
        [
          'name' => 'Ventas',
          'data' => $seriesData
        ]
      ],
      'xaxis' => [
        'categories' => $categories
      ]
    ];
    echo json_encode([
       'data'=>$options
    ]); 
  }


  public function fun_ActualizarProducto($f3){
    $msj="";
    $id_producto=$f3->get('POST.id_producto');
    $nombre=$f3->get('POST.nombreMenu');
    $this->M_Producto->load(['id_producto = ?', $id_producto]);  

      
    if($this->M_Producto->loaded() > 0) {  
        //modificar 
        $this->M_Producto->set('nombre', $nombre);
        $this->M_Producto->set('descripcion', $f3->get('POST.descripcion'));
        $this->M_Producto->set('precio', $f3->get('POST.precio'));
        $this->M_Producto->set('id_categoria', $f3->get('POST.id_categoria'));
        $this->M_Producto->save(); 
        $id=$this->M_Producto->get('id_producto');
          $msj = "Producto Actualizado Correctamente";
    }

      echo json_encode([
          'msj'=>$msj,
          'data'=> $this->M_Producto->cast(),
          'id'=>$id
      ]);  
}
 

}

