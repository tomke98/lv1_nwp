<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplomski radovi</title>
</head>
<body>
    <?php
        include "UpraviteljPodacima.php";
        //kreiranje objekta klase UpraviteljPodacima
        $upraviteljPodacima = new UpraviteljPodacima('http://stup.ferit.hr/index.php/zavrsni-radovi/page/5');
        //poziv funkcije kako bi se dohvatili svi potrebni podaci (naslov, tekst, poveznica i OIB)
        $dohvaceniPodaci = $upraviteljPodacima->dohvatiPodatke();

        $diplomskiRad = new DiplomskiRadovi(array(
            'naziv_rada' => "",
            'tekst_rada' => "",
            'link_rada' => "",
            'oib_tvrtke' => "")
        );

        //funkcija parsirajPodatke() vraca vrijednosti kao polje koje se vraca u funkciji dohvacajPodatke()
        //oib_tvrtke: $dohvaceniPodaci[3]
        //naziv_rada: $dohvaceniPodaci[0]
        //tekst_rada: $dohvaceniPodaci[1]
        //link_rada: $dohvaceniPodaci[2]
    
        //za svaki OIB, kreiraju se objekti s ostalim podacima
        for($i=0; $i < count($dohvaceniPodaci[3]); $i++){
            $diplomskiRad->create(array(
                'naziv_rada' => $dohvaceniPodaci[0][$i],
                'tekst_rada' => $dohvaceniPodaci[1][$i],
                'link_rada' => $dohvaceniPodaci[2][$i],
                'oib_tvrtke' => $dohvaceniPodaci[3][$i]
            ));
            //spremanje vrijednosti u bazu podataka
            $diplomskiRad->save();
        }
        //citanje i dohvacanje podataka iz tablice
        $diplomskiRad->read();
    ?>
</body>
</html>