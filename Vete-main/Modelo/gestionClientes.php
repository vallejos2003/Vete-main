<?php
require_once('Conexion.php');

class gestionClientes{
    private $clientes=[];

    public function __construct(){
       $this->cargarClientes();
    }

    public function agregarCliente($cliente) {
        $nombre = $cliente->getNombre();
        $apellido = $cliente->getApellido();
        $direccion = $cliente->getDireccion();
        $telefono = $cliente->getTelefono();
        $correoelectronico = $cliente->getCorreoElectronico();

        $sql = "INSERT INTO cliente (nombre, apellido, direccion, telefono, correoelectronico)
                VALUES ('$nombre', '$apellido', '$direccion','$telefono','$correoelectronico')";

        Conexion::ejecutar($sql); 
        echo "El cliente se agrego exitosamente\n";
    }
   
    public function eliminarClientePorApellido($apellido) {
        $conexion = Conexion::getConexion();
        try {
            $query = $conexion->prepare("DELETE FROM cliente WHERE apellido = :apellido");
            $query->bindParam(':apellido', $apellido);
            $resultado = $query->execute();
    
            if ($resultado && $query->rowCount() > 0) {
                $this->cargarClientes();
                return true;
            }
    
        return false;
        }catch (PDOException $e) {
            echo 'Error al eliminar Cliente. Podría tener algún dato de venta que hace referencia a él';
 
       }
    }

    public function listarClientes() {
        $this->cargarClientes();
        if (empty($this->clientes)) {
            echo "No hay clientes disponibles.\n";
        } else {
            echo "\nLista de clientes:\n";
            foreach ($this->clientes as $cliente) {
                echo "ID: " . $cliente->getID() . ", Nombre: " . $cliente->getNombre() . ", Apellido: " . $cliente->getApellido(). "\n";
            } 
            echo PHP_EOL;
        }
    }

    public function cargarClientes(){
        $this->clientes=[];
        $sql = "SELECT * FROM Cliente";
        $clientes = Conexion::query($sql);
        foreach ($clientes as $cliente) {
            if (is_object($cliente)) {
                    $nuevoCliente = new Cliente(  
                    $cliente->id_cliente,
                    $cliente->nombre,
                    $cliente->apellido,
                    $cliente->direccion,
                    $cliente->telefono,
                    $cliente->correoelectronico
                );
                $this->clientes[] = $nuevoCliente;
            } else {
                echo "Los datos del cliente no están en el formato esperado.";
            }
                }
    }

    public function buscarClientePorApellido($apellido) {
        if ($apellido != ""){
            $conexion = Conexion::getConexion();
            $query = $conexion->prepare("SELECT * FROM cliente WHERE apellido = :apellido");
            $query->bindParam(':apellido', $apellido);
            $query->execute();
    
            $cliente = $query->fetch(PDO::FETCH_ASSOC);
    
            if ($cliente) {
                return new Cliente(
                    $cliente['nombre'],
                    $cliente['apellido'],
                    $cliente['direccion'],
                    $cliente['telefono'],
                    $cliente['correoelectronico']
                 );
            }   else {
                return null;
            } 
        } 
    }
    

    public function modificarClientePorApellido($apellido, $nuevoNombre, $nuevoApellido, $nuevaDireccion, $nuevoTelefono, $nuevoCorreoElectronico) {
        $conexion = Conexion::getConexion();
        $query = $conexion->prepare("UPDATE Cliente SET nombre = :nuevoNombre, apellido = :nuevoApellido, direccion = :nuevaDireccion, telefono = :nuevoTelefono, CorreoElectronico =:nuevoCorreoElectronico WHERE apellido = :apellido");
        $query->bindParam(':nuevoNombre', $nuevoNombre);
        $query->bindParam(':nuevoApellido', $nuevoApellido);
        $query->bindParam(':nuevaDireccion', $nuevaDireccion);
        $query->bindParam(':nuevoTelefono', $nuevoTelefono);
        $query->bindParam(':nuevoCorreoElectronico', $nuevoCorreoElectronico);
        $query->bindParam(':apellido', $apellido);
        $resultado = $query->execute();
    
        if ($resultado && $query->rowCount() > 0) {
            $this->cargarClientes();
            return true;
        }
        return false;
    }


}