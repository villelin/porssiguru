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

$json = json_encode($full_list);
echo $json;

function parseStockData($url)
{
    $stock_list = array();

    $stockpage = file_get_contents($url);
    if ($stockpage != false)
    {
        $doc = new DOMDocument();
        if ($doc->loadHTML($stockpage, LIBXML_NOWARNING | LIBXML_NOERROR))
        {
            $a_elements = $doc->getElementsByTagName("a");
            foreach ($a_elements as $element)
            {
                foreach ($element->attributes as $attribute)
                {
                    // löytyykö A-elementti, jossa on pörssidataan täsmäävä class?
                    if ($attribute->name == "class" && $attribute->value == "row mx-0 list-item-header stock-link")
                    {
                        // täällä pitäis olla 3 DIV nodea...
                        if ($element->childNodes->length == 3)
                        {
                            $company_name = "";
                            $price = "";
                            $change = "";

                            // ensimmäisestä DIVistä pitäis löytyä H5, jonka sisällä on SPAN, jossa on yhtiön nimi
                            $company_h5 = $element->childNodes[0]->getElementsByTagName("h5");
                            if ($company_h5->length > 0)
                            {
                                $company_span = $company_h5[0]->getElementsByTagName("span");
                                if ($company_span->length > 0)
                                {
                                    //echo "Yhtiö = " . $company_span[0]->nodeValue . "<br>";
                                    $company_name = $company_span[0]->nodeValue;
                                }
                            }

                            // toisesta DIVistä pitäis löytyä SPAN jossa on osakkeen hinta
                            $price_span = $element->childNodes[1]->getElementsByTagName("span");
                            if ($price_span->length > 0)
                            {
                                //echo "Hinta = " . $price_span[0]->nodeValue . "<br>";
                                $price = $price_span[0]->nodeValue;
                            }

                            // kolmannesta DIVistä pitäis löytyä SPAN jossa on muutosprosentti

                            $change_span = $element->childNodes[2]->getElementsByTagName("span");
                            if ($change_span->length > 0)
                            {
                                //echo "Muutos = " . $change_span[0]->nodeValue . "<br>";
                                $change = $change_span[0]->nodeValue;
                            }

                            $stock_entry = array();
                            $stock_entry["company"] = $company_name;
                            $stock_entry["price"] = $price;
                            $stock_entry["change"] = $change;

                            $stock_list[] = $stock_entry;
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
}


?>