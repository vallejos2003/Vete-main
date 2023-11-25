<?php
class Conexion {
    private static $db = null;
    private static function getDatosDb(){
        $nombreArchivo = "./Modelo/base.json";
        if (is_readable($nombreArchivo)){
            $datos = file_get_contents($nombreArchivo);
            $datos = json_decode($datos);
           return $datos;
        }
        return null;
    }
    private function __construct(){
        try {
            $datosDb = self::getDatosDb();
            $dsn = "pgsql:host=$datosDb->host;port=$datosDb->port;dbname=$datosDb->database;user=$datosDb->user;password=$datosDb->password";
            self::$db = new PDO($dsn);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
    }
    public static function getConexion(){
        if (isset(self::$db)) {
            return self::$db;
        } else {
            new self();
            return self::$db;
        }
    }
    public static function query($sql) {
        $pDO = self::getConexion();
        $statement = $pDO->query($sql);
        if ($statement) {
            $resultado = $statement->fetchAll(PDO::FETCH_OBJ);
            return $resultado;
        } else {
            return [];
        }
    }
    public static function ejecutar($sql) {
        $pDO = self::getConexion();
        $pDO->query($sql);
    }
    public static function prepare($sql) {
        $pDO = self::getConexion();
        return $pDO->prepare($sql);
    }
    public static function getLastId() {
        $pDO = self::getConexion();
        $lastId = $pDO->lastInsertId();   
        return $lastId;
    } 
    public static function closeConexion() {
        self::$db = null;
    }
}