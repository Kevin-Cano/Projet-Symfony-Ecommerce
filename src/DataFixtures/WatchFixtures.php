<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use App\Entity\Watch;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WatchFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $watches = [
            ["Patek Philippe Nautilus", "5711/1A-010", 35000, "Une montre sportive emblématique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "120m"], "Bracelet intégré", "https://static.patek.com/images/articles/face_white/350/5811_1G_001_1.jpg", 3],
            ["Audemars Piguet Royal Oak", "15400ST.OO.1220ST.01", 25000, "Une montre iconique au design octogonal.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Bracelet intégré", "https://www.lmhorlogerie.fr/cdn/shop/files/RoyalOak15500.jpg?v=1685576911", 5],
            ["Rolex Submariner", "126610LN", 9000, "La montre de plongée par excellence.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Oyster", "https://images.watchfinder.co.uk/imgv3/stock/300585/Rolex-Submariner-16800-300585-240830-115851.jpg;quality=90", 8],
            ["Vacheron Constantin Overseas", "4500V/110A-B483", 22000, "Une montre de voyage élégante.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "150m"], "Bracelet intégré", "https://www.chisholmhunter.co.uk/media/catalog/product/cache/1c13339936d9cec19ec06c7ff2acbcef/2/-/2-106-02-0017_p_1a.jpg", 4],
            ["IWC Portugieser", "IW500705", 12500, "Une montre classique avec un grand mouvement.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://content.presspage.com/uploads/1859/800_iw500715-front.jpg?10000", 6],
            ["TAG Heuer Monaco", "CAW2111.FC6183", 6500, "Une montre carrée emblématique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir bleu", "https://example.com/monaco.jpg", 10], #image
            ["Hublot Big Bang", "301.SB.131.RX", 15000, "Une montre audacieuse au design moderne.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Caoutchouc noir", "https://theswisscollector.com/28500/hublot-big-bang-acciaio-ceramica-44-mm.jpg", 7],
            ["Breitling Navitimer", "AB0121211B1A1", 8000, "Une montre de pilote légendaire.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/navitimer.jpg", 9], #image
            ["Panerai Luminor", "PAM01312", 9500, "Une montre italienne robuste et élégante.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Cuir brun", "https://d2j6dbq0eux0bg.cloudfront.net/images/38270005/3563788099.jpg", 5],
            ["Chopard Alpine Eagle", "298600-3001", 13000, "Une montre sportive inspirée par la nature.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Bracelet intégré", "https://specials-images.forbesimg.com/imageserve/5d936b1624fbf10007b89b32/0x800.jpg?cropX1=0&cropX2=1008&cropY1=362&cropY2=1461", 4],
            ["Zenith El Primero", "03.2040.400/69.C496", 8500, "Une montre avec un mouvement légendaire.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir noir", "https://lionel-meylan.ch/wp-content/uploads/2022/04/montre-zenith-chronomaster-sport-el-primero-18.3101.3600-69.M3100-horlogerie-joaillerie-lionel-meylan-vevey.jpg", 6],
            ["Girard-Perregaux Laureato", "81005-11-431-11A", 14000, "Une montre sportive au design intemporel.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Bracelet intégré", "https://monochrome-watches.com/wp-content/uploads/2022/10/Girard-Perregaux-Laureato-38mm-Copper-1.jpg", 3],
            ["Blancpain Fifty Fathoms", "5015-1130-52", 15000, "Une montre de plongée historique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "300m"], "Caoutchouc noir", "https://example.com/fiftyfathoms.jpg", 5], # image 
            ["Tudor Black Bay", "79230N-0009", 3500, "Une montre vintage inspirée des années 50.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "200m"], "Cuir brun", "https://images.lacotedesmontres.com/mesIMG/imgHD/72307.jpg", 12], 
            ["Longines Master Collection", "L2.773.4.78.3", 2500, "Une montre classique avec chronographe.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir marron", "https://lionel-meylan.ch/wp-content/uploads/2015/08/Longines-Master-Collection-L2.257.8.87.3-600x600.jpg", 15],
            ["Montblanc Heritage Chronométrie", "112531", 5000, "Une montre élégante avec une grande date.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://example.com/heritage.jpg", 8], # image
            ["Bvlgari Octo Finissimo", "102713", 12000, "Une montre ultra-plate au design unique.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Bracelet intégré", "https://www.ablogtowatch.com/wp-content/uploads/2022/08/Bulgari-Octo-Finissimo-Skeleton-8-Days-Watch-2-scaled.jpg", 4],
            ["Richard Mille RM 011", "RM011", 150000, "Une montre de haute technologie.", ["mouvement" => "Automatique", "materiau" => "Titane", "etancheite" => "50m"], "Caoutchouc noir", "https://media.richardmille.com/wp-content/uploads/2020/02/20130409/richard-mille-rm-011-automatic-chronograph-felipe-massa-45670.jpg?dpr=3&width=750", 2],
            ["Ulysse Nardin Marine", "1183-310/43", 9000, "Une montre inspirée par la mer.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir bleu", "https://www.montredo.com/fr-fr/wp-content/uploads/2020/06/ulysse-nardin-marine-chronograph-43-mm-1533-150-3e0-27399-2.jpg", 7],
            ["Franck Muller Vanguard", "V 45 SC DT", 18000, "Une montre au design audacieux.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "30m"], "Caoutchouc noir", "https://images.squarespace-cdn.com/content/v1/56a9e12ac647ad08eb4453c7/1463125453692-GX9J8JVTF2CUOXUGAOCG/V32-QZ-cdblanc-chifblanc-filetNIK-brasblanc-OG.png", 3],
            ["Bell & Ross BR 03-92", "BR0392-BL-ST/SST", 3500, "Une montre inspirée des instruments de bord.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Caoutchouc noir", "https://www.bellross.com/image/catalog/product/BR-03/BR03-92-Ceramic-Black-Matte.png", 9],
            ["Oris Big Crown", "01 754 7741 4065-07 5 20 58", 2000, "Une montre pilot avec une grande couronne.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Cuir marron", "https://www.uhrendirect.de/shop/images/products/gallery/11830_1.jpg", 11],
            ["Nomos Glashütte Tangente", "139", 2500, "Une montre minimaliste et élégante.", ["mouvement" => "Manuel", "materiau" => "Acier", "etancheite" => "30m"], "Cuir noir", "https://cdn.nomos-glashuette.com/media/image/ff/68/cf/1600xauto-q80/101-139-2022-2-detail-2.jpg", 6],
            ["Seiko Presage", "SRPB41J1", 500, "Une montre japonaise au design raffiné.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "50m"], "Cuir marron", "https://example.com/presage.jpg", 20], # image
            ["Grand Seiko Snowflake", "SBGA211G", 6000, "Une montre avec un cadran unique en neige.", ["mouvement" => "Automatique", "materiau" => "Titane", "etancheite" => "100m"], "Bracelet intégré", "https://www.subtil-diamant.com/ori-seiko-presage-spb095j1-cadran-blanc-40-60-mm-34891.jpg", 8],
            ["Tissot T-Touch", "T091.420.47.051.00", 1000, "Une montre tactile multifonction.", ["mouvement" => "Quartz", "materiau" => "Titane", "etancheite" => "100m"], "Caoutchouc noir", "https://wiki-des-bijoutiers.fr/wp-content/uploads/sites/4/2013/12/tissot-tissot-t-touch-sailing-touch-blanc-diamants.jpg", 15],
            ["Hamilton Khaki Field", "H70555533", 800, "Une montre militaire robuste.", ["mouvement" => "Automatique", "materiau" => "Acier", "etancheite" => "100m"], "Cuir marron", "https://wiki-des-bijoutiers.fr/wp-content/uploads/sites/4/2013/12/tissot-tissot-t-touch-sailing-touch-blanc-diamants.jpg", 18],
            ["Rado True Thinline", "R27056702", 1500, "Une montre ultra-mince en céramique.", ["mouvement" => "Quartz", "materiau" => "Céramique", "etancheite" => "30m"], "Céramique intégrée", "https://www.rado.com/media/catalog/product/r/g/rgb_cat_true_thinline_420_0742_3_072_3.png", 10]
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
            $watch->setPicture($watchData[6]);
            $stock = new Stock();
            $stock->setWatchStock($watchData[7]);
            $stock->setWatch($watch);
            $watch->setStock($stock);
            $watch->setPublicationDate(new \DateTime());

            $manager->persist($watch);
        }

        $manager->flush();
    }
}
