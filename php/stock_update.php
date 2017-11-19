<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 19.11.2017
 * Time: 19.29
 *
 * Parsii pörssitiedot Kauppalehden pörssisivuilta
 */



// Helsinki lista
$xhel_list = parseStockData("https://beta.kauppalehti.fi/porssi/kurssit/XHEL");
// First North lista
$fnfi_list = parseStockData("https://beta.kauppalehti.fi/porssi/kurssit/FNFI");

$full_list = array_merge($xhel_list, $fnfi_list);

//$json = json_encode($full_list);
//echo $json;
echo "<table>";
echo "<tr>";
echo "<th>Yhtiö</th>";
echo "<th>Hinta</th>";
echo "<th>Muutos</th>";
echo "</tr>";
foreach ($full_list as $entry)
{
    echo "<tr>";
    echo "<td>" . $entry["company"] . "</td>";
    echo "<td>" . $entry["price"] . "</td>";
    echo "<td>" . $entry["change"] . "</td>";
    echo "</tr>";
}
echo "</table>";


function parseStockData($url)
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
                            if ($company_name != null && $price != null && $change != null)
                            {
                                $stock_entry = array();
                                $stock_entry["company"] = $company_name;
                                $stock_entry["price"] = $price;
                                $stock_entry["change"] = $change;

                                $stock_list[] = $stock_entry;
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
            return null;
        }
    }
    else
    {
        echo "URL ei aukea";
        return null;
    }
}


?>