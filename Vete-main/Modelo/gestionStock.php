<?php
require_once('Conexion.php');

class gestionStock{
    private $stock=[];

    public function __construct(){
        $this->cargarStock();
    }

    public function insertarStock($stock) {
        $ID_Producto = $stock->getID_Producto();
        $cantidad = $stock->getCantidad();
        $sql = "INSERT INTO Stock (cantidad, ID_Producto)
                VALUES ('$cantidad', '$ID_Producto')";

        Conexion::ejecutar($sql);
        echo "El stock se actualizo exitosamente\n";
    }

    public function listarStock() {
        $this->cargarStock();
        if (empty($this->stock)) {
            echo "No hay stock disponible.\n";
        } else {
            echo "\nStock:\n";
            foreach ($this->stock as $unStock) {
                echo "ID: " . $unStock->getID_Producto() . ", Nombre: " . $this->obtenerNombreProducto($unStock->getID_Producto()) . ", Cantidad: " . $unStock->getCantidad() . "\n";
            } 
            echo PHP_EOL;
        }
    }
    public function obtenerStock($id){
        if ($id != ""){
            $conexion = Conexion::getConexion();
            $query = $conexion->prepare("SELECT cantidad FROM Stock WHERE ID_Producto = :ID");
            $query->bindParam(':ID', $id);
            $query->execute();
    
            $cantidad = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($cantidad) {
                return $cantidad ['cantidad'];
            }   else {
                return null;
            } 
        }  
    }

    public function modificarStock($id_producto, $cantidad) {
        $conexion = Conexion::getConexion();
        $cantidadNueva = $this->obtenerStock($id_producto) - $cantidad;
        $query = $conexion->prepare("UPDATE Stock SET cantidad = :cantidadNueva WHERE ID_Producto = :id");
        $query->bindParam(':cantidadNueva', $cantidadNueva);
        $query->bindParam(':id', $id_producto);
        $resultado = $query->execute();
        if ($resultado && $query->rowCount() > 0) {
            $this->cargarStock();
            return true;
        }
        return false;
    }

    public function obtenerNombreProducto($id){
        if ($id != ""){
            $conexion = Conexion::getConexion();
            $query = $conexion->prepare("SELECT nombre FROM Producto WHERE ID_Producto = :ID");
            $query->bindParam(':ID', $id);
            $query->execute();
    
            $nombre = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($nombre) {
                return $nombre ['nombre'];
            }   else {
                return null;
            } 
        } 
    }

    public function cargarStock(){
        $this->stock=[];
        $sql = "SELECT * FROM Stock";
        $stocks = Conexion::query($sql);
        foreach ($stocks as $stock) {
            if (is_object($stock)) {
                    $nuevoStock = new Stock(  
                    $stock->id_producto,
                    $stock->cantidad
                );
                $this->stock[] = $nuevoStock;
            } else {
                echo "Los datos del stock no están en el formato esperado.";
            }
        }
    }
}