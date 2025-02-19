<?php

namespace App\DataFixtures;

use App\Entity\Watch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WatchFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $watches = [
            ["Patek Philippe Nautilus", "5711/1A-010", "35,000€", "Une montre sportive emblématique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "120m"], "Bracelet intégré", "https://static.patek.com/images/articles/face_white/350/5811_1G_001_1.jpg", 3],
            ["Audemars Piguet Royal Oak", "15400ST.OO.1220ST.01", "25,000€", "Une montre iconique au design octogonal.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Bracelet intégré", "https://example.com/royaloak.jpg", 5],
            ["Rolex Submariner", "126610LN", "9,000€", "La montre de plongée par excellence.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Oyster", "https://example.com/submariner.jpg", 8],
            ["Vacheron Constantin Overseas", "4500V/110A-B483", "22,000€", "Une montre de voyage élégante.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "150m"], "Bracelet intégré", "https://example.com/overseas.jpg", 4],
            ["IWC Portugieser", "IW500705", "12,500€", "Une montre classique avec un grand mouvement.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/portugieser.jpg", 6],
            ["TAG Heuer Monaco", "CAW2111.FC6183", "6,500€", "Une montre carrée emblématique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir bleu", "https://example.com/monaco.jpg", 10],
            ["Hublot Big Bang", "301.SB.131.RX", "15,000€", "Une montre audacieuse au design moderne.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Caoutchouc noir", "https://example.com/bigbang.jpg", 7],
            ["Breitling Navitimer", "AB0121211B1A1", "8,000€", "Une montre de pilote légendaire.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/navitimer.jpg", 9],
            ["Panerai Luminor", "PAM01312", "9,500€", "Une montre italienne robuste et élégante.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Cuir brun", "https://example.com/luminor.jpg", 5],
            ["Chopard Alpine Eagle", "298600-3001", "13,000€", "Une montre sportive inspirée par la nature.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Bracelet intégré", "https://example.com/alpineagle.jpg", 4],
            ["Zenith El Primero", "03.2040.400/69.C496", "8,500€", "Une montre avec un mouvement légendaire.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir noir", "https://example.com/elprimero.jpg", 6],
            ["Girard-Perregaux Laureato", "81005-11-431-11A", "14,000€", "Une montre sportive au design intemporel.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Bracelet intégré", "https://example.com/laureato.jpg", 3],
            ["Blancpain Fifty Fathoms", "5015-1130-52", "15,000€", "Une montre de plongée historique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Caoutchouc noir", "https://example.com/fiftyfathoms.jpg", 5],
            ["Tudor Black Bay", "79230N-0009", "3,500€", "Une montre vintage inspirée des années 50.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "200m"], "Cuir brun", "https://example.com/blackbay.jpg", 12],
            ["Longines Master Collection", "L2.773.4.78.3", "2,500€", "Une montre classique avec chronographe.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir marron", "https://example.com/mastercollection.jpg", 15],
            ["Montblanc Heritage Chronométrie", "112531", "5,000€", "Une montre élégante avec une grande date.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/heritage.jpg", 8],
            ["Bvlgari Octo Finissimo", "102713", "12,000€", "Une montre ultra-plate au design unique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Bracelet intégré", "https://example.com/octo.jpg", 4],
            ["Richard Mille RM 011", "RM011", "150,000€", "Une montre de haute technologie.", ["mouvement" => "Automatique", "materiau" => "Titane", "etancheite" => "50m"], "Caoutchouc noir", "https://example.com/rm011.jpg", 2],
            ["Ulysse Nardin Marine", "1183-310/43", "9,000€", "Une montre inspirée par la mer.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir bleu", "https://example.com/marine.jpg", 7],
            ["Franck Muller Vanguard", "V 45 SC DT", "18,000€", "Une montre au design audacieux.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Caoutchouc noir", "https://example.com/vanguard.jpg", 3],
            ["Bell & Ross BR 03-92", "BR0392-BL-ST/SST", "3,500€", "Une montre inspirée des instruments de bord.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Caoutchouc noir", "https://example.com/br03.jpg", 9],
            ["Oris Big Crown", "01 754 7741 4065-07 5 20 58", "2,000€", "Une montre pilot avec une grande couronne.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Cuir marron", "https://example.com/bigcrown.jpg", 11],
            ["Nomos Glashütte Tangente", "139", "2,500€", "Une montre minimaliste et élégante.", ["mouvement" => "Manuel", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/tangente.jpg", 6],
            ["Seiko Presage", "SRPB41J1", "500€", "Une montre japonaise au design raffiné.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Cuir marron", "https://example.com/presage.jpg", 20],
            ["Grand Seiko Snowflake", "SBGA211G", "6,000€", "Une montre avec un cadran unique en neige.", ["mouvement" => "Automatique", "materiau" => "Titane", "etancheite" => "100m"], "Bracelet intégré", "https://example.com/snowflake.jpg", 8],
            ["Tissot T-Touch", "T091.420.47.051.00", "1,000€", "Une montre tactile multifonction.", ["mouvement" => "Quartz", "materiau" => "Titane", "etancheite" => "100m"], "Caoutchouc noir", "https://example.com/ttouch.jpg", 15],
            ["Hamilton Khaki Field", "H70555533", "800€", "Une montre militaire robuste.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir marron", "https://example.com/khakifield.jpg", 18],
            ["Rado True Thinline", "R27056702", "1,500€", "Une montre ultra-mince en céramique.", ["mouvement" => "Quartz", "materiau" => "Céramique", "etancheite" => "30m"], "Céramique intégrée", "https://example.com/thinline.jpg", 10]
            
        ];

        foreach ($watches as $watchData) {
            $watch = new Watch();
            $watch->setName($watchData[0]);
            $watch->setReference($watchData[1]);
            $watch->setPrice($watchData[2]);
            $watch->setDescription($watchData[3]);
            $watch->setMovement($watchData[4]['mouvement']);
            $watch->setMaterial($watchData[4]['materiau']);
            $watch->setWaterResistance($watchData[4]['etancheite']);
            $watch->setBracelet($watchData[5]);
            $watch->setImageUrl($watchData[6]);
            $watch->setStock($watchData[7]);

            $manager->persist($watch);
        }

        $manager->flush();
    }
}
