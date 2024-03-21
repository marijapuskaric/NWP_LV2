<?php
    session_start();

    $encrypted_files_directory = 'uploads/';
    $decrypted_files_directory = 'downloads/';

    //dohvacanje svih kriptiranih datoteka koje su uploadane na server
    $encrypted_files = glob($encrypted_files_directory . 'encrypted_*');

    //provjera je li izabrana datoteka za preuzimanje
    if (isset($_GET['file'])) 
    {
        //stvaranje direktorija gdje se spremaju dekriptirane datoteke na serveru
        if (!is_dir($decrypted_files_directory))
        {
            if(!@mkdir($decrypted_files_directory))
            {
                die("<p>Directory can't be created.<\p>");
            }
        }
        
        $decryption_key = md5('jed4n j4k0 v3l1k1 kljuc');
        $cipher = "AES-128-CTR";  
        $options = 0;
        
        //dohvacanje naziva kriptirane datoteke i vracanje originalnog imena
        $encrypted_file_name = $_GET['file'];
        $decrypted_file_name = substr(basename($encrypted_file_name), strlen('encrypted_'));

        //dohvacanje inicijalizacijskog vektora iz datoteke
        $iv_length = openssl_cipher_iv_length($cipher);
        $iv = substr(file_get_contents($encrypted_file_name), 0, $iv_length);

        //dohvacanje kriptiranih podataka i njihovo dekriptiranje
        $encrypted_data = substr(file_get_contents($encrypted_file_name), $iv_length);
        $decrypted_data = openssl_decrypt($encrypted_data, $cipher, $decryption_key, $options, $iv);

        //spremanje podataka u novu datoteku na serveru
        $decrypted_file_path = $decrypted_files_directory . $decrypted_file_name;
        file_put_contents($decrypted_file_path, $decrypted_data);

        //preuzimanje dekriptirane datoteke s originalnim nazivom
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($decrypted_file_name) . '"');
        echo $decrypted_data;

    } 
    //prikaz linkova za preuzimanje svih kriptiranih datoteka na serveru
    else 
    {
        echo '<h5>Download encrypted files:</h5>';
        echo '<ul>';
        foreach ($encrypted_files as $encrypted_file) {
            $original_file_name = substr(basename($encrypted_file), strlen('encrypted_')); 
            echo '<li><a href="?file=' . urlencode($encrypted_file) . '">' . $original_file_name . '</a></li>';
        }
        echo '</ul>';
    }
?>
