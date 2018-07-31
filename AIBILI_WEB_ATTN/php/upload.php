<?php
    function uploadFiles($id) {
        
        // Count # of uploaded files in array
        $total = count($_FILES['upload']['name']);

        mkdir("../docs/$id/");

        // Loop through each file
        for($i = 0; $i < $total; $i++) {

            // Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

            // Make sure we have a file path

            if ($tmpFilePath != "") {
            
                // Setup our new file path
                $newFilePath = "../docs/$id/" . $_FILES['upload']['name'][$i];

                // Upload the file into the temp dir
                move_uploaded_file($tmpFilePath, $newFilePath);
            }
        }

        return $_SERVER['DOCUMENT_ROOT'] . "/docs/$id/";
    }