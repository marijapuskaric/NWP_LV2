<?php
    //ucitavanje xml datoteke
    $xml = simplexml_load_file('LV2.xml');

    //prolazak kroz svaki record u xml datoteci i ispis podataka
    foreach($xml->record as $record)
    {
        echo "<div style='display:flex; margin:10px;'>
                <p> $record->id. </p>
                <div style='display:flex; align-items: center;'>
                    <div style='margin:10px'>
                        <img src='$record->slika'>
                    </div>
                    <div>
                        <p><b>$record->ime $record->prezime</b></p>
                        <p>Email: $record->email</p>
                        <p>Spol: $record->spol</p>
                        <p>Životopis: $record->zivotopis</p>
                    </div>
                </div>
            </div>";
    }
?>