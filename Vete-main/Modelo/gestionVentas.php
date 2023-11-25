<?php
require_once('Conexion.php');
require_once('./Modelo/gestionStock.php');

class gestionVentas{
    private $gestionStock;

    public function __construct(){
        $this->gestionStock = new gestionStock();
    }

    public function vender($venta, $detalleVenta) {
        $cantidad = $detalleVenta->getCantidad();
        $precioUnitario = $detalleVenta->getPrecioUnitario();
        $ID_Producto = $detalleVenta->getID_Producto();
        
        $fechaHoraActual = $venta->getFecha();
        
        $fechaFormateada = date('Y-m-d H:i:s', mktime(
            $fechaHoraActual['hours'],
            $fechaHoraActual['minutes'],
            $fechaHoraActual['seconds'],
            $fechaHoraActual['mon'],
            $fechaHoraActual['mday'],
            $fechaHoraActual['year']
        ));

        $ID_Cliente = $venta->getID_Cliente();

        $sql = "INSERT INTO Compra (FechaCompra, ID_Cliente)
                VALUES ('$fechaFormateada', '$ID_Cliente')"; // esta hardcodeada la fecha, poner la del dia actual

        Conexion::ejecutar($sql);

        $conexion = Conexion::getConexion();
        $query = $conexion->prepare("SELECT ID_Compra FROM Compra ORDER BY ID_Compra DESC");
        $query->execute();
        $id = $query->fetch(PDO::FETCH_ASSOC);
        if ($id) {
            $ID_Compra = $id['id_compra'] ;
        }

        $sql = "INSERT INTO DetalleCompra (Cantidad, PrecioUnitario, ID_Producto, ID_Compra)
                VALUES ('$cantidad', '$precioUnitario', '$ID_Producto','$ID_Compra')";

        Conexion::ejecutar($sql);
        $this->gestionStock->modificarStock($ID_Producto,$cantidad);
        echo "La venta se realizo exitosamente\n";
    }
}