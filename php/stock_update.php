<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 19.11.2017
 * Time: 19.29
 *
 */

require_once('config.php');

// Alpha Vantage API:n käyttöavain
define("apikey", "F07XVL4DE169DFQY");

// haettavien valuuttojen lista
$currency_type_list = array();
$currency_type_list[] = array('symbol' => 'XAU', 'name' => 'Kulta (unssi)');
$currency_type_list[] = array('symbol' => 'XAG', 'name' => 'Hopea (unssi)');
$currency_type_list[] = array('symbol' => 'BTC', 'name' => 'Bitcoin');
$currency_type_list[] = array('symbol' => 'BCH', 'name' => 'Bitcoin-Cash');
$currency_type_list[] = array('symbol' => 'ETH', 'name' => 'Ethereum');
$currency_type_list[] = array('symbol' => 'LTC', 'name' => 'Litecoin');
$currency_type_list[] = array('symbol' => 'XMR', 'name' => 'Monero');
$currency_type_list[] = array('symbol' => 'XRP', 'name' => 'Ripples');
$currency_type_list[] = array('symbol' => 'DASH', 'name' => 'Dash');


echo "OK";

$time_start = microtime(true);

// Helsinki lista
$xhel_list = parseStockData("https://beta.kauppalehti.fi/porssi/kurssit/XHEL", "Helsinki");
// First North lista
$fnfi_list = parseStockData("https://beta.kauppalehti.fi/porssi/kurssit/FNFI", "First North");
// Valuutat
$currencies = getCurrencyData($currency_type_list, "Valuutta");

$time_end = microtime(true);

$total_time = $time_end - $time_start;

// listat yhteen
$full_list = array_merge($xhel_list, $fnfi_list, $currencies);

// päivitetään tietokanta
updateStockDatabase($DBH, $full_list);


/*
echo "Haku vei aikaa " . $total_time . " sekuntia<br>";
echo "<table>";
echo "<tr>";
echo "<th>Symboli</th>";
echo "<th>Yhtiö</th>";
echo "<th>Hinta</th>";
echo "<th>Muutos</th>";
echo "<th>Kategoria</th>";
echo "</tr>";
foreach ($full_list as $entry)
{
    echo "<tr>";
    echo "<td>" . $entry["symbol"] . "</td>";
    echo "<td>" . $entry["company"] . "</td>";
    echo "<td>" . $entry["price"] . "</td>";
    echo "<td>" . $entry["change"] . "</td>";
    echo "<td>" . $entry["category"] . "</td>";
    echo "</tr>";
}
echo "</table>";
*/


/*
 * Parsii pörssitiedot Kauppalehden pörssisivuilta
 */
function parseStockData($url, $market)
{
    $stock_list = array();

    $stockpage = file_get_contents($url);
    if ($stockpage != false)
    {
        $doc = new DOMDocument();
        if ($doc->loadHTML($stockpage, LIBXML_NOWARNING | LIBXML_NOERROR))
        {
            // käy läpi kaikki A-elementit...
            $a_elements = $doc->getElementsByTagName("a");
            foreach ($a_elements as $element)
            {
                // ...ja niiden attribuutit
                foreach ($element->attributes as $attribute)
                {
                    // löytyykö A-elementti, jolla on pörssidataan täsmäävä class?
                    //if ($attribute->name == "class" && $attribute->value == "row mx-0 list-item-header stock-link")
                    if ($attribute->name == "class" && strpos($attribute->value, "stock-link") !== false)
                    {
                        $stock_symbol = null;

                        // etsi elementin href
                        foreach ($element->attributes as $attr)
                        {
                            if ($attr->name == "href")
                            {
                                // linkki on muotoa: /porssi/porssikurssit/osake/AFAGR
                                // ota viimeinen osa osakesymboliksi

                                $string_index = strrpos($attr->value, "/");
                                if ($string_index !== false)
                                {
                                    $stock_symbol = substr($attr->value, $string_index+1);
                                }
                            }
                        }

                        // täällä pitäis olla 3 DIV nodea...
                        if ($element->childNodes->length == 3)
                        {
                            $company_name = null;
                            $price = null;
                            $change = null;

                            // ensimmäisestä DIVistä pitäis löytyä H5, jonka sisällä on SPAN, jossa on yhtiön nimi
                            $company_h5 = $element->childNodes[0]->getElementsByTagName("h5");
                            if ($company_h5->length > 0)
                            {
                                $company_span = $company_h5[0]->getElementsByTagName("span");
                                if ($company_span->length > 0)
                                {
                                    //echo "Yhtiö = " . $company_span[0]->nodeValue . "<br>";
                                    $company_name = $company_span[0]->nodeValue;
                                    // UTF8:sta ISO-8859-1:ksi
                                    $company_name = utf8_decode($company_name);
                                }
                            }

                            // toisesta DIVistä pitäis löytyä SPAN jossa on osakkeen hinta
                            $price_span = $element->childNodes[1]->getElementsByTagName("span");
                            if ($price_span->length > 0)
                            {
                                //echo "Hinta = " . $price_span[0]->nodeValue . "<br>";
                                $price = $price_span[0]->nodeValue;

                                // pilkut pisteiksi
                                $price = str_replace(",", ".", $price);
                                // liukuluvuksi ja takaisin
                                $price = (string)floatval($price);
                            }

                            // kolmannesta DIVistä pitäis löytyä SPAN jossa on muutosprosentti
                            $change_span = $element->childNodes[2]->getElementsByTagName("span");
                            if ($change_span->length > 0)
                            {
                                //echo "Muutos = " . $change_span[0]->nodeValue . "<br>";
                                $change = $change_span[0]->nodeValue;

                                // pilkut pisteiksi
                                $change = str_replace(",", ".", $change);
                                // liukuluvuksi ja takaisin
                                $change = (string)floatval($change);
                            }

                            // jos joku on null, jotain meni pieleen
                            if ($company_name != null && $price != null && $change != null && $stock_symbol != null)
                            {
                                // hinnan pitää olla suurempi kuin 0
                                if ($price > 0)
                                {
                                    $stock_entry = array();
                                    $stock_entry["symbol"] = $stock_symbol;
                                    $stock_entry["company"] = $company_name;
                                    $stock_entry["price"] = $price;
                                    $stock_entry["change"] = $change;
                                    $stock_entry["category"] = $market;

                                    $stock_list[] = $stock_entry;
                                }
                            }
                        }
                    }
                }
            }

            return $stock_list;
        }
        else
        {
            echo "HTML ei aukea";
        }
    }
    else
    {
        echo "URL ei aukea";
    }

    return $stock_list;
}


/*
 * Parsii valuuttatiedot Alpha Vantagen API:sta
 */
function getCurrencyData($entry_list, $category)
{
    $currency_list = array();

    foreach ($entry_list as $entry)
    {
        // luodaan Alpha Vantage URL
        // valuutasta euroksi
        $url = "https://www.alphavantage.co/query?function=CURRENCY_EXCHANGE_RATE";
        $url .= "&from_currency=" . $entry["symbol"];
        $url .= "&to_currency=EUR";
        $url .= "&apikey=" . apikey;

        // JSON serveriltä
        $json = file_get_contents($url);
        if ($json != false)
        {
            $data = json_decode($json, true);

            $rate = $data["Realtime Currency Exchange Rate"];

            $price = $rate["5. Exchange Rate"];

            // hinnan pitää olla suurempi kuin 0
            if ($price > 0)
            {
                $currency_entry = array();
                $currency_entry["symbol"] = $entry["symbol"];
                $currency_entry["company"] = $entry["name"];
                $currency_entry["price"] = $price;
                $currency_entry["change"] = "0";        // valuutoille ei ole muutosprosenttia tarjolla
                $currency_entry["category"] = $category;

                $currency_list[] = $currency_entry;
            }
        }
    }

    return $currency_list;
}


/*
 * Lähettää listan tiedot tietokantaan
 */
function updateStockDatabase($dbh, $stock_list)
{
    foreach ($stock_list as $entry)
    {
        $symbol = $entry["symbol"];
        $name = $entry["company"];
        $price = $entry["price"];
        $change = $entry["change"];
        $category = $entry["category"];

        // onko yhtiö/valuutta jo olemassa
        $find_query = "SELECT symbol FROM stock WHERE symbol='$symbol'";

        $sql = $dbh->prepare($find_query);
        $sql->execute();

        // vakiona päivitetään
        $update = true;

        try
        {
            if ($sql->rowCount() == 0)
            {
                // ei löytynyt, lisätään
                $update = false;
            }
        }
        catch (PDOException $e)
        {
            die("VIRHE: " . $e->getMessage());
        }

        $update_query = "";

        // query päivitykselle tai lisäykselle
        if ($update)
        {
            // päivitetään
            $update_query = "UPDATE stock SET price='$price', variety='$change' WHERE symbol='$symbol'";
        }
        else
        {
            // lisätään
            $update_query = "INSERT INTO stock(symbol, company, price, variety)VALUES('$symbol', '$name', '$price', '$change')";
        }

        // tiedot kantaan
        $sql = $dbh->prepare($update_query);
        $sql->execute();
    }
}

?>