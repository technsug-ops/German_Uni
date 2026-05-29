<?php

/**
 * Cover-image fallback pool per top German city.
 *
 * When a university card has no admin-curated image_url AND its city is in
 * this list, the Blade renderer picks one URL deterministically using
 * crc32($uni->id) % count(pool). Same uni → same image always; different unis
 * in the same city → rotated across the pool. Smaller cities or anything not
 * keyed below falls through to the gradient + initials fallback.
 *
 * Keys are cities.slug (e.g. "berlin-q64"). All URLs are Wikimedia Commons
 * thumb URLs that have been chosen as recognizable city landmarks, not random
 * Wikipedia images.
 */

return [
    'berlin-q64' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Brandenburger_Tor_abends.jpg/1280px-Brandenburger_Tor_abends.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Berlin_reichstag_west_panorama_2.jpg/1280px-Berlin_reichstag_west_panorama_2.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Berlin_Fernsehturm_Lustgarten.jpg/1280px-Berlin_Fernsehturm_Lustgarten.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Berlin_-_Museumsinsel_-_2014.jpg/1280px-Berlin_-_Museumsinsel_-_2014.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/East_Side_Gallery_Berlin.jpg/1280px-East_Side_Gallery_Berlin.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Berliner_Dom_at_night.jpg/1280px-Berliner_Dom_at_night.jpg',
    ],

    'munchen-q1726' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/M%C3%BCnchen_Marienplatz_von_oben.jpg/1280px-M%C3%BCnchen_Marienplatz_von_oben.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Frauenkirche_M%C3%BCnchen_S%C3%BCdseite.jpg/1280px-Frauenkirche_M%C3%BCnchen_S%C3%BCdseite.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Olympiapark_M%C3%BCnchen_Panorama.jpg/1280px-Olympiapark_M%C3%BCnchen_Panorama.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/BMW_Welt_Muenchen_Eingang.jpg/1280px-BMW_Welt_Muenchen_Eingang.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Englischer_Garten_M%C3%BCnchen_Chinaturm.jpg/1280px-Englischer_Garten_M%C3%BCnchen_Chinaturm.jpg',
    ],

    'hamburg-q1055' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Elbphilharmonie_Hamburg.jpg/1280px-Elbphilharmonie_Hamburg.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Speicherstadt_Hamburg_-_Wasserschloss.jpg/1280px-Speicherstadt_Hamburg_-_Wasserschloss.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Hamburg_Rathaus_Innenhof.jpg/1280px-Hamburg_Rathaus_Innenhof.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Hamburg_Hafen_Sonnenaufgang.jpg/1280px-Hamburg_Hafen_Sonnenaufgang.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Aussenalster_Hamburg.jpg/1280px-Aussenalster_Hamburg.jpg',
    ],

    'koln-q365' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/K%C3%B6lner_Dom_-_Hauptportal_2.jpg/1280px-K%C3%B6lner_Dom_-_Hauptportal_2.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Hohenzollernbruecke_Koeln.jpg/1280px-Hohenzollernbruecke_Koeln.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Cologne_Rheinpark.jpg/1280px-Cologne_Rheinpark.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Koeln_Altstadt_und_Rhein.jpg/1280px-Koeln_Altstadt_und_Rhein.jpg',
    ],

    'frankfurt-am-main-q1794' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Frankfurt_Skyline_2015.jpg/1280px-Frankfurt_Skyline_2015.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/R%C3%B6merberg_Frankfurt.jpg/1280px-R%C3%B6merberg_Frankfurt.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Frankfurt_am_Main_Eiserner_Steg.jpg/1280px-Frankfurt_am_Main_Eiserner_Steg.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/Frankfurt_skyline_night.jpg/1280px-Frankfurt_skyline_night.jpg',
    ],

    'stuttgart-q1022' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Stuttgart_Neues_Schloss.jpg/1280px-Stuttgart_Neues_Schloss.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Stuttgart_Schlossplatz_Panorama.jpg/1280px-Stuttgart_Schlossplatz_Panorama.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Mercedes-Benz_Museum_Stuttgart.jpg/1280px-Mercedes-Benz_Museum_Stuttgart.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Stuttgart_Stadtbibliothek.jpg/1280px-Stuttgart_Stadtbibliothek.jpg',
    ],

    'dusseldorf-q1718' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Rheinturm_D%C3%BCsseldorf_am_Abend.jpg/1280px-Rheinturm_D%C3%BCsseldorf_am_Abend.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/MedienHafen_D%C3%BCsseldorf.jpg/1280px-MedienHafen_D%C3%BCsseldorf.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/K%C3%B6nigsallee_D%C3%BCsseldorf.jpg/1280px-K%C3%B6nigsallee_D%C3%BCsseldorf.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Schloss_Benrath_D%C3%BCsseldorf.jpg/1280px-Schloss_Benrath_D%C3%BCsseldorf.jpg',
    ],

    'hannover-q1715' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e2/Neues_Rathaus_Hannover.jpg/1280px-Neues_Rathaus_Hannover.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Herrenhaeuser_Gaerten_Hannover.jpg/1280px-Herrenhaeuser_Gaerten_Hannover.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Hannover_Marktkirche.jpg/1280px-Hannover_Marktkirche.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Maschsee_Hannover.jpg/1280px-Maschsee_Hannover.jpg',
    ],

    'leipzig-q2079' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/V%C3%B6lkerschlachtdenkmal_Leipzig.jpg/1280px-V%C3%B6lkerschlachtdenkmal_Leipzig.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Leipzig_Marktplatz.jpg/1280px-Leipzig_Marktplatz.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/Augustusplatz_Leipzig.jpg/1280px-Augustusplatz_Leipzig.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Nikolaikirche_Leipzig.jpg/1280px-Nikolaikirche_Leipzig.jpg',
    ],

    'dresden-q1731' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Dresden_Frauenkirche.jpg/1280px-Dresden_Frauenkirche.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Zwinger_Dresden.jpg/1280px-Zwinger_Dresden.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Semperoper_Dresden.jpg/1280px-Semperoper_Dresden.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Br%C3%BChlsche_Terrasse_Dresden.jpg/1280px-Br%C3%BChlsche_Terrasse_Dresden.jpg',
    ],

    'bremen-q24879' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6d/Bremen_Rathaus_und_Roland.jpg/1280px-Bremen_Rathaus_und_Roland.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Bremer_Stadtmusikanten.jpg/1280px-Bremer_Stadtmusikanten.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Schnoorviertel_Bremen.jpg/1280px-Schnoorviertel_Bremen.jpg',
    ],

    'karlsruhe-q1040' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Karlsruhe_Schloss.jpg/1280px-Karlsruhe_Schloss.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Karlsruhe_Marktplatz.jpg/1280px-Karlsruhe_Marktplatz.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Karlsruher_Pyramide.jpg/1280px-Karlsruher_Pyramide.jpg',
    ],

    'bonn-q892684' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Bonn_Beethoven_Statue.jpg/1280px-Bonn_Beethoven_Statue.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Bonn_M%C3%BCnster.jpg/1280px-Bonn_M%C3%BCnster.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Bonn_Marktplatz.jpg/1280px-Bonn_Marktplatz.jpg',
    ],
];
