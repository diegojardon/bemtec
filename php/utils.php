<?php

function encriptar($cadena){
    $key='zgrmobile';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
    $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
    return $encrypted; //Devuelve el string encriptado

}

function desencriptar($cadena){
     $key='zgrmobile';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
     $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode(str_replace(' ', '+', $cadena)), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
    return $decrypted;  //Devuelve el string desencriptado
}

function generaPassword(){
    return "";
}

?>
