<?php
namespace App\Service;

class file_upload {

    public function charger_img($name,$file_dest,array $data){
        $file_name= $_FILES[$name]["name"];
        $file_extension = strrchr($file_name,".");
        $file_temp = $_FILES[$name]["tmp_name"];
        $file_dest = $file_dest.$file_name;
        if(in_array($file_extension, $data)){
            if(move_uploaded_file($file_temp, $file_dest)){
                return $file_name;
            }else{
                echo "On n'a pas pu chargé l'image";
            }
        }else{
            echo "Cette extension n'existe pas supporté par le système";
        }
    }
}    