<?php

include "simple_html_dom.php";
include "DiplomskiRadovi.php";

class UpraviteljPodacima {
    private $url;
    private $htmlParser;
    private $diplomskiRadovi;

    //konstruktor klase UpraviteljPodacima s varijablom $url kao parametar
    function __construct($url) {
        $this->url = $url;
        //postojeci parser za HTML
        $this->htmlParser = new simple_html_dom();
        //stvaranje polja s vrijednostima postavljenim na prazan string
        $this->diplomskiRadovi = new DiplomskiRadovi(array(
            'naziv_rada' => "",
            'tekst_rada' => "",
            'link_rada' => "",
            'oib_tvrtke' => ""));
    }

    //funkcija za dohvacanje podataka pomocu cURL-a
    function dohvatiPodatke() {
        //pokretanje cURL spoja
        $curl = curl_init($this->url);
        //zaustavi ako se dogodi pogreska
        curl_setopt($curl, CURLOPT_FAILONERROR, 1);
        //dozvoli preusmjeravanje
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        //spremi vracene pdoatke u varijablu
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //postavi timeout
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        //izvrsavanje transakcije
        $rezultat = curl_exec($curl);
        //zatvaranje spoja
        curl_close($curl);
        //vracanje funkcije parsirajPodatke()
        return $this->parsirajPodatke($rezultat);
    }

    //privatna funkcija za parsiranje podataka
    private function parsirajPodatke($podaci) {
        $naslovi = [];
        $tekstovi = [];
        $linkovi = [];
        $oibi = [];

        $stranica = $this->htmlParser->load($podaci);

        //dohvacanje OIB-a
        foreach($stranica->find('img') as $slika) {
            //trazenje rijeci logos unutar varijable $slika
            if (strpos($slika, "logos") !== false) {
                //dodavanje vrijednosti u polje $oibi
                //pre_replace sve znamenke koje nisu brojevi mijenja praznim stringom unutar atributa
                //src koji se nalazi u varijabli $slika
                array_push($oibi, preg_replace('/[^0-9]/', '', $slika->src));
            }
        }

        //dohvacanje naslova i linkova
        foreach($stranica->find('article') as $clanak) {          
            foreach($clanak->find('ul.slides img') as $slika) {
            }
            foreach($clanak->find('h2.entry-title a') as $link) {
                //dodavanje vrijednosti unutar polja $linkovi i $naslovi
                    array_push($linkovi, $link->href);
                    array_push($naslovi, $link->plaintext);
            }
        }
        //dohvacanje tekstova
        $tekstovi = $this->dohvatiTekstove($linkovi);

        
        //vracanje polja sa svim parametrima
        return array($naslovi, $tekstovi, $linkovi, $oibi);
    }

    //funkcija za dohvacanje tekstova
    private function dohvatiTekstove($linkovi) {
        $tekstovi = [];
        //za svaku poveznicu je potrebno pronaci odredeni tekst
        foreach($linkovi as $link) {
            //pokretanje cURL spoja
            $curl = curl_init($link);
            //zaustavi ako se dogodi pogreska
            curl_setopt($curl, CURLOPT_FAILONERROR, 1);
            //dozvoli preusmjeravanje
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            //spremi vracene pdoatke u varijablu
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //postavi timeout
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            //izvrsavanje transakcije
            $rezultat = curl_exec($curl);
            //zatvaranje spoja
            curl_close($curl);

            $stranica = $this->htmlParser->load($rezultat);

            foreach($stranica->find('.post-content') as $tekst) {
                //dodavanje vrijednosti unutar polja $tekstovi
                array_push($tekstovi, $tekst->plaintext);
            }
        }
        //vracanje polja $tekstovi
        return $tekstovi;
    }
}

?>