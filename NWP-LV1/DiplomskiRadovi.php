<?php

include "iRadovi.php";

class DiplomskiRadovi implements iRadovi {
    private $naziv_rada = null;
    private $tekst_rada = null;
    private $link_rada = null;
    private $oib_tvrtke = null;

    //konstruktor klase DiplomskiRadovi
    function __construct($data) {
        $this->id_rada = uniqid();
        $this->naziv_rada = $data['naziv_rada'];
        $this->tekst_rada = $data['tekst_rada'];
        $this->link_rada = $data['link_rada'];
        $this->oib_tvrtke = $data['oib_tvrtke'];
    }

    //prepisivanje funkcije create()
    function create($data) {
        self::__construct($data);
    }

    //prepisivanje funkcije save()
    function save() {
        //podaci za spajanje na lokalnu bazu podataka
        $ime_servera = "localhost";
        $korisnicko_ime = "root";
        $sifra = "";
        $ime_baze = "radovi";

        //povezivanje pomocu mysql-a
        $povezivanje = new mysqli($ime_servera, $korisnicko_ime, $sifra, $ime_baze);

        //ako povezivanje nije uspjesno
        if ($povezivanje->connect_error) {
            die("Povezivanje nije uspjesno: " . $povezivanje->connect_error);
        }

        $id_povezivanja = $this->id_rada;
        $naziv_povezivanja = $this->naziv_rada;
        $tekst_povezivanja = $this->tekst_rada;
        $link_povezivanja = $this->link_rada;
        $oib_povezivanja = $this->oib_tvrtke;

        //umetanje u bazu podataka
        $upit = "INSERT INTO `diplomski_radovi` (`id_rada`, `naziv_rada`, `tekst_rada`, `link_rada`, `oib_tvrtke`) VALUES ('$id_povezivanja', '$naziv_povezivanja', '$tekst_povezivanja', '$link_povezivanja', '$oib_povezivanja')";

        //ako umetanje nije uspjesno
        if(!$povezivanje->query($upit)) {
            echo "Umetanje nije uspjesno!" . $upit . $povezivanje->error;
        }

        //zatvaranje povezivanja
        $povezivanje->close();
    }

    //dohvacanje dobivenih radova
    function read() {
         //podaci za spajanje na lokalnu bazu podataka
         $ime_servera = "localhost";
         $korisnicko_ime = "root";
         $sifra = "";
         $ime_baze = "radovi";
 
         //povezivanje pomocu mysql-a
         $povezivanje = new mysqli($ime_servera, $korisnicko_ime, $sifra, $ime_baze);
 
         //ako povezivanje nije uspjesno
         if ($povezivanje->connect_error) {
             die("Povezivanje nije uspjesno: " . $povezivanje->connect_error);
         }
         
         //odabir i prikaz podataka iz tablice
         $upit = "SELECT * FROM `diplomski_radovi`";
         $izlaz = $povezivanje->query($upit);

         //provjera je li tablica puna i dohvacanje redaka iz tablice
         if ($izlaz->num_rows > 0) {
             while($redak = $izlaz->fetch_assoc()) {
                echo "<br><br><br>ID rada: " . $redak["id_rada"] .
                "<br><br>Naziv rada: " . $redak["naziv_rada"] .
                "<br><br>Tekst rada: " . $redak["tekst_rada"] .
                "<br><br>Link rada: " . $redak["link_rada"] .
                "<br><br>OIB tvrtke: " . $redak["oib_tvrtke"] ;
             }
         }

         //zatvaranje povezivanja
         $povezivanje->close();
    }
}
?>