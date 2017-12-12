<?php

date_default_timezone_set("America/Buenos_Aires");

require_once 'AccesoDatos.php';

class operacion{

	public static function Existe($patente){

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 

        $consulta = $objetoAccesoDato->RetornarConsulta("select 1 from operaciones where patente=:patente and salida is null");	

        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);		

        $consulta->execute();

        return $consulta->fetchColumn();

    }

    public static function libre($cochera){
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta = $objetoAccesoDato->RetornarConsulta("select estado from cocheras where nrocochera=:cochera");	
            $consulta->bindValue(':cochera',$cochera, PDO::PARAM_STR);		
            $consulta->execute();
            return $consulta->fetchColumn();
        }



    public static function registrarOperacion($cochera ,$patente,$datosToken){

        $idEmpleado=$datosToken->id;

        $entrada = date("Y-m-d H:i:s"); 

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 

        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into operaciones (idCochera, patente, idEmpleado, entrada)values(:idCochera,:patente,:idEmpleado,:entrada)");

        $consulta->bindValue(':idCochera',$cochera, PDO::PARAM_INT);	

        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	

        $consulta->bindValue(':idEmpleado',$idEmpleado, PDO::PARAM_STR);	

        $consulta->bindValue(':entrada',$entrada, PDO::PARAM_STR);	

        $consulta->execute();



        $objetoAccesoDatos2 = AccesoDatos::dameUnObjetoAcceso(); 

        $consulta2 = $objetoAccesoDatos2->RetornarConsulta("update cocheras set estado='ocupado' WHERE nroCochera = :idCochera");

        $consulta2->bindValue(':idCochera',$cochera, PDO::PARAM_INT);

        $consulta2->execute();



        return $objetoAccesoDato->RetornarUltimoIdInsertado();

    }



    public static function registrarVehiculo($marca,$color ,$patente){

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 

        $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into vehiculos (marca, patente, color)values(:marca,:patente,:color)");

        $consulta->bindValue(':marca',$marca, PDO::PARAM_INT);	

        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	

        $consulta->bindValue(':color',$color, PDO::PARAM_STR);	

        $consulta->execute();

        return $objetoAccesoDato->RetornarUltimoIdInsertado();

    }



    public static function obtenerEntrada($patente){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta = $objetoAccesoDato->RetornarConsulta("select entrada,idCochera from operaciones where patente=:patente and salida is null");	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);		
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function obtenerCosto($segundos){
        //3-Cobro por hora$10 o media estadía $90(12hs) o estadia$170(24hs).
        $horas = $segundos / 60 / 60;
        $costo;
        if($horas<12){
            $costo = $horas * 10;
            if($costo <10){
                $costo =10;
            }
        }elseif ($horas>=24) {
            $costo = $horas/24 * 170;
        }else {
            $costo = $horas/12 * 90;
        }
        return $costo;
    }



    public static function terminarOperacion($patente,$datosToken){
        $salidaStr = date("Y-m-d H:i:s");
        $salida = new DateTime($salidaStr); 
        $datos = operacion::obtenerEntrada($patente);
        $entrada = new DateTime($datos[0]['entrada']);
        $diff = $salida->diff($entrada);
        $segundos = ( ($diff->days * 24 ) * 60 *60) +( $diff->h *60 * 60 )+ ( $diff->i * 60 ) + $diff->s;
        $costo = round(operacion::obtenerCosto($segundos),2);

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $datos = operacion::obtenerEntrada($patente);
        $consulta =$objetoAccesoDato->RetornarConsulta("update operaciones set salida = :salida, costo = :costo where patente=:patente and salida is null");
        $consulta->bindValue(':salida',$salidaStr, PDO::PARAM_STR);	
        $consulta->bindValue(':costo',$costo, PDO::PARAM_STR);	
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	
        $consulta->execute();
        operacion::actualizarCochera($datos[0]['idCochera']);
        return "Ok, se saco la patente: ".$patente." y se cobro: $".$costo;
    }



    public static function actualizarCochera($cochera){

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 

        $consulta =$objetoAccesoDato->RetornarConsulta("update cocheras set estado = 'libre' where nroCochera=:cochera");

        $consulta->bindValue(':cochera',$cochera, PDO::PARAM_STR);	

        $consulta->execute();



        return $objetoAccesoDato->RetornarUltimoIdInsertado();

    }





    public static function confirmarSalidaVehiculo($patente,$datosToken){
        $salidaStr = date("Y-m-d H:i:s");
        $salida = new DateTime($salidaStr); 
        $datos = operacion::obtenerEntrada($patente);
        $entrada = new DateTime($datos[0]['entrada']);
        $diff = $salida->diff($entrada);
        $segundos = ( ($diff->days * 24 ) * 60 *60) +( $diff->h *60 * 60 )+ ( $diff->i * 60 ) + $diff->s;
        $costo = round(operacion::obtenerCosto($segundos),2);
        $horas = round(($segundos / 60 / 60),2);
		$objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDatos->RetornarConsulta("SELECT marca,color,entrada from operaciones, vehiculos where salida is null and operaciones.patente= :patente and vehiculos.patente=operaciones.patente");
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);	
        $consulta->execute();
        $retorno = $consulta->fetchAll(PDO::FETCH_ASSOC);
        array_push($retorno,$horas);
        array_push($retorno,$costo);
        array_push($retorno,$salidaStr);
        //var_dump($retorno);die();
        return $retorno;
	}

    public static function masOcupada(){
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
		$consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) Cantidad,idCochera cochera FROM `operaciones` group by idCochera order by 1 desc ");
		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
        }
        $query = $consulta->fetchAll();
        $max = $query[0]["Cantidad"];

        for ($i=0; $i < count($query); $i++) {
            if($max == $query[$i]["Cantidad"]){
                $retorno[] = $query[$i];
            }else{
                break;
            }
        }
        //var_dump($max);die();
		return $retorno;
    }

    public static function masOcupadaPorFecha($desde,$hasta){

        $fechaDesde = DateTime::createFromFormat('!j-m-Y', $desde);
        $fechaDesdeString = $fechaDesde->format('Y-m-d H:i:s');

        $fechaHasta = DateTime::createFromFormat('!j-m-Y', $hasta);
        $fechaHastaString = $fechaHasta->format('Y-m-d H:i:s');

//        var_dump($fechaDesde->format('Y-m-d H:i:s'));die();
        
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) Cantidad,idCochera cochera FROM `operaciones` where entrada > :fechaDesde and entrada < :fechaHasta group by idCochera order by 1 desc ");
        $consulta->bindValue(':fechaDesde',$fechaDesdeString, PDO::PARAM_STR);
        $consulta->bindValue(':fechaHasta',$fechaHastaString, PDO::PARAM_STR);
		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
        }
        $query = $consulta->fetchAll();
        $max = $query[0]["Cantidad"];

        for ($i=0; $i < count($query); $i++) {
            if($max == $query[$i]["Cantidad"]){
                $retorno[] = $query[$i];
            }else{
                break;
            }
        }
        //var_dump($max);die();
		return $retorno;
    }

    public static function menosOcupada(){
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
		$consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) Cantidad,idCochera cochera FROM `operaciones` group by idCochera order by 1 asc ");
		$consulta->setFetchMode(PDO::FETCH_ASSOC);
		
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
        }
        $query = $consulta->fetchAll();
        $max = $query[0]["Cantidad"];

        for ($i=0; $i < count($query); $i++) {
            if($max == $query[$i]["Cantidad"]){
                $retorno[] = $query[$i];
            }else{
                break;
            }
        }
        //var_dump($max);die();
		return $retorno;
    }
    public static function menosOcupadaPorFecha($desde,$hasta){
        
                $fechaDesde = DateTime::createFromFormat('!j-m-Y', $desde);
                $fechaDesdeString = $fechaDesde->format('Y-m-d H:i:s');
        
                $fechaHasta = DateTime::createFromFormat('!j-m-Y', $hasta);
                $fechaHastaString = $fechaHasta->format('Y-m-d H:i:s');
        
        //        var_dump($fechaDesde->format('Y-m-d H:i:s'));die();
                
                $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
                $consulta = $objetoAccesoDatos->retornarConsulta("SELECT count(*) Cantidad,idCochera cochera FROM `operaciones` where entrada > :fechaDesde and entrada < :fechaHasta group by idCochera order by 1 asc ");
                $consulta->bindValue(':fechaDesde',$fechaDesdeString, PDO::PARAM_STR);
                $consulta->bindValue(':fechaHasta',$fechaHastaString, PDO::PARAM_STR);
                $consulta->setFetchMode(PDO::FETCH_ASSOC);
                
                $consulta->execute();
                if($consulta->rowCount() == 0){
                    return false;   
                }
                $query = $consulta->fetchAll();
                $max = $query[0]["Cantidad"];
        
                for ($i=0; $i < count($query); $i++) {
                    if($max == $query[$i]["Cantidad"]){
                        $retorno[] = $query[$i];
                    }else{
                        break;
                    }
                }
                //var_dump($max);die();
                return $retorno;
            }

    public static function nuncaOcupada(){
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->retornarConsulta("SELECT nroCochera cochera, piso FROM cocheras where nroCochera not in (select idCochera from operaciones)");
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
    }

    public static function nuncaOcupadaPorFecha($desde,$hasta){
        $fechaDesde = DateTime::createFromFormat('!j-m-Y', $desde);
        $fechaDesdeString = $fechaDesde->format('Y-m-d H:i:s');

        $fechaHasta = DateTime::createFromFormat('!j-m-Y', $hasta);
        $fechaHastaString = $fechaHasta->format('Y-m-d H:i:s');
        
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->retornarConsulta("SELECT nroCochera cochera, piso FROM cocheras where nroCochera not in (select idCochera from operaciones where entrada > :fechaDesde and entrada < :fechaHasta)");
        $consulta->bindValue(':fechaDesde',$fechaDesdeString, PDO::PARAM_STR);
        $consulta->bindValue(':fechaHasta',$fechaHastaString, PDO::PARAM_STR);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
    }

    public static function operacionesPorPatente($patente){
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->retornarConsulta("SELECT * from operaciones where patente=:patente");
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
    }

    public static function operacionesPorPatenteConFecha($patente,$desde,$hasta){
        $fechaDesde = DateTime::createFromFormat('!j-m-Y', $desde);
        $fechaDesdeString = $fechaDesde->format('Y-m-d H:i:s');

        $fechaHasta = DateTime::createFromFormat('!j-m-Y', $hasta);
        $fechaHastaString = $fechaHasta->format('Y-m-d H:i:s');
        
        $objetoAccesoDatos = accesoDatos::DameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->retornarConsulta("SELECT * from operaciones where patente=:patente and entrada > :fechaDesde and entrada < :fechaHasta");
        $consulta->bindValue(':patente',$patente, PDO::PARAM_STR);
        $consulta->bindValue(':fechaDesde',$fechaDesdeString, PDO::PARAM_STR);
        $consulta->bindValue(':fechaHasta',$fechaHastaString, PDO::PARAM_STR);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        
		$consulta->execute();
		if($consulta->rowCount() == 0){
			return false;   
		}
		
		return $consulta->fetchAll();
    }

    
}
