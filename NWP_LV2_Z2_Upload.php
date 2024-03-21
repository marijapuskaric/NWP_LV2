<!DOCTYPE html>
<html>
    <body>
        <!-- forma za unos datoteke -->
        <h5>Upload document (pdf, jpeg, png)</h5>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" id="myFile" name="filename">
            <input type="submit" value="Upload">
        </form>
        <?php
            session_start();

            if ($_SERVER["REQUEST_METHOD"] == "POST") 
            {
                $upload_dir = "uploads/";
                $target_file = $upload_dir.basename($_FILES['filename']['name']);
                $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                //provjera vrste dokumenta
                if($file_type != "pdf" && $file_type != "png" && $file_type != "jpeg" && $file_type != "jpg") 
                {
                    echo "Sorry, only PDF, JPG, JPEG & PNG files are allowed.";
                }
                else
                {
                    //stvaranje direktorija u koji ce se spremati uploadane datoteke
                    if (!is_dir($upload_dir))
                    {
                        if(!@mkdir($upload_dir))
                        {
                            die("<p>Directory can't be created.<\p>");
                        }
                    }

                    //podatci za kriptiranje dokumenta
                    $encryption_key = md5('jed4n j4k0 v3l1k1 kljuc');
                    $cipher = "AES-128-CTR";
                    $iv_length = openssl_cipher_iv_length($cipher); 
                    $options = 0;
                    $encryption_iv = openssl_random_pseudo_bytes($iv_length); 
                    $data = file_get_contents($_FILES["filename"]["tmp_name"]);
                    
                    //kriptiranje dokumenta
                    $encrypted_data = openssl_encrypt($data, $cipher, $encryption_key, $options, $encryption_iv);
                    $encrypted_file = $upload_dir . "encrypted_" . basename($_FILES['filename']['name']);

                    //uploadanje kriptiranog dokumenta na server
                    if (file_put_contents($encrypted_file, $encryption_iv . $encrypted_data))
                    {
                        echo "File uploaded successfully.";
                    }
                    else 
                    {
                        echo "Failed.";
                    }
                }     
            }
        ?>
    </body>
</html>