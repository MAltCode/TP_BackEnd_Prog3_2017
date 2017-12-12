<?php
class imagenes{

    public static function enmarcar($imagen){
        
    $nombre=$imagen['foto']["name"];
    $im=imagecreatefrompng($imagen['foto']["tmp_name"]);

    // Saco dimensiones de la imagen ppal. Saco 20%
    list($ancho, $alto) = getimagesize($imagen['foto']["tmp_name"]);
    $nuevo_ancho = $ancho * 0.35;
    $nuevo_alto = $alto * 0.35;
    list($ancho2, $alto2) = getimagesize('../Imagenes/marca.png');
    $estampa = imagecreatefrompng('../Imagenes/marca.png');
    $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
    imagecopyresampled($imagen_p, $estampa, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho2, $alto2);
       

    imagepng($imagen_p,"../Imagenes/marcaTemp.png");
    imagedestroy($imagen_p);
    $imagen_p=imagecreatefrompng('../Imagenes/marcaTemp.png');
    
    
    // Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
    $margen_dcho = 10;
    $margen_inf = 10;
    $sx = imagesx($imagen_p);
    $sy = imagesy($imagen_p);
    
    // Copiar la imagen de la estampa sobre nuestra foto usando los índices de márgen y el
    // ancho de la foto para calcular la posición de la estampa. 
    imagecopy($im, $imagen_p, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($imagen_p), imagesy($imagen_p));
    

    imagepng($im,"../temp/".$nombre);
    imagedestroy($im);

    return $nombre;
    
    }

    public static function achicar($imagen){
        list($ancho, $alto) = getimagesize($imagen['foto']["tmp_name"]);
        $im=imagecreatefrompng($imagen['foto']["tmp_name"]);
  
        $porcentaje = 0.5;

        $nuevo_ancho = $ancho * $porcentaje;
        $nuevo_alto = $alto * $porcentaje;

            $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
            imagecopyresampled($imagen_p, $im, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
               
            header('Content-type: image/png');
            imagepng($imagen_p,"../export/asd4.png");
            imagedestroy($im);




        }
}
?>