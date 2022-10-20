<?php
namespace Lego;

use Sabre\HTTP;

class Request extends HTTP\RequestDecorator {

    function getRawFiles() {
        return $_FILES;
    }

    function getFiles() {
        $files = [];
        foreach($_FILES as $field => $file) {
            if(is_array($file)) {
                $tmp = [];
                for($i = 0;$i<count($file["tmp_name"]);$i++) {
                    $tmp[] = new File([
                        "tmp_name" => $file["tmp_name"][$i],
                        "type" => $file["type"][$i],
                        "size" => $file["size"][$i],
                        "full_path" => $file["full_path"][$i],
                    ]);
                }

                $files[$field] = $tmp;
            } else {
                $files[$field] = new File($file);
            }
        }

        return $files;
    }

}