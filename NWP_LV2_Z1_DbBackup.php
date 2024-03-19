<?php
    //funkcija za izradu backup-a baze podataka
    //atributi su naziv baze za koju se stvara backup te mjesto na koje ce se backup spremiti na racunalu
    function BackupDB($db_name, $backup_dir)
    {
        //podatci za spajanje na bazu
        $servername = "localhost";
        $username = "root";
        $password = "";

        //naziv txt datoteke u koju ce se spremiti backup
        $backup_file = "$backup_dir/backup_$db_name.txt";
        $backup_file_zip = "$backup_dir/backup_$db_name.zip";

        //pokusaj spajanja na bazu
        $conn = new mysqli($servername, $username, $password, $db_name);
        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }

        //pokusaj stvaranja direktorija za spremanje backup-a
        if (!is_dir($backup_dir))
        {
            if(!@mkdir($backup_dir))
            {
                die("<p>Directory can't be created.<\p>");
            }
        }

        //dohvacanje svih tablica u bazi i spremanje u polje tablica
        $tables_result = $conn->query("SHOW TABLES");
        $tables = [];
        while ($row = $tables_result->fetch_array()) 
        {
            $tables[] = $row[0];
        }

        //provjera ima li baza barem jednu tablicu
        if (count($tables) > 0)
        {
            //otvaranje datoteke za spremanje backup-a baze
            $file = fopen($backup_file, "w");
            if (!$file) 
            {
                die("File can't be opened.");
            }
            //prolazak kroz polje tablica te dohvacanje i spremanje podataka za svaku
            foreach($tables as $table)
            {
                $table_data = $conn->query("SELECT * FROM $table");
                $values = [];
                while($row = $table_data->fetch_assoc())
                {
                    $insert_query = "INSERT INTO $table (";
                    foreach ($row as $column_name => $value)
                    {
                        $insert_query .=$column_name . ', ';
                        $values[] = "'" . $conn->real_escape_string($value) . "'"; 
                    }
                    $insert_query = rtrim($insert_query, ', ') . ") VALUES (" . implode(', ', $values) . ");\n";
                    fwrite($file, $insert_query);
                }  
            }
            echo "Backup for $db_name created. </br>";
            //zatvaranje datoteke za spremanje backup-a baze
            fclose($file);
        }
        else
        {
            echo "<p>Database $db_name has no tables.</p>";
        }

        //zatvaranje konekcije na bazu
        $conn->close();

        //sazimanje txt datoteke
        $zip = new ZipArchive();
        if ($zip->open($backup_file_zip, ZipArchive::CREATE) === TRUE) 
        {
            if ($zip->addFile($backup_file, basename($backup_file))) 
            {
                $zip->close();
                echo "Backup successfully compressed into $backup_file_zip.";
            } 
            else 
            {
                echo "Failed to add file to the ZIP archive.";
            }
        } 
        else 
        {
            echo "Failed to create ZIP archive.";
        }
    }

    //poziv funkcije za izradu backup-a testne baze podataka 
    BackupDB("nwp_lv2_testdb", "D:/Fakultet/4_semestar_diplomski/Napredno web programiranje/LV/LV2/backups");
?>