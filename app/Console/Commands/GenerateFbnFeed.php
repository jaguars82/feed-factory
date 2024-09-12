<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\grch\Agency;
use App\Models\grch\User;

class GenerateFbnFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fbn:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML feed for FBN';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("realty-feed");
        $generationDate = $dom->createElement("generation-date", date('c'));
        $root->appendChild($generationDate);

        $agenciesToExport = Agency::all();

        foreach ($agenciesToExport as $agency) {
            if (count($agency->advertisements) < 1) continue;
            foreach ($agency->advertisements as $advertisement) {
                if (count($agency->advertisements) < 1) continue;
                foreach ($advertisement->secondaryRooms as $room) {
                    if (is_null($room->category)) continue;
                    $offer = $dom->createElement("offer");
                    $offer->setAttribute("internal-id", $room->id);
                    switch ($room->category->parent_id) {
                        // Offer for a flat or a room
                        case 1:
                            if ($room->advertisement->deal_type	=== 1) {
                                
                                // Deal type
                                $type = $dom->createElement("type", "продажа");
                                $offer->appendChild($type);
                                
                                // Real estate type
                                $propertyType = $dom->createElement("property-type", "жилая");
                                $offer->appendChild($propertyType);
                                
                                // Object category
                                $category = $dom->createElement("category", $room->category->name === "Комната" ? "комната" : "квартира");
                                $offer->appendChild($category);
                                
                                // Creation date
                                $creationDate = $dom->createElement("creation-date", date('c', strtotime($room->advertisement->creation_date)));
                                $offer->appendChild($creationDate);
                                
                                // Last update date
                                $lastUpdateDate = $dom->createElement("last-update-date", date('c', strtotime($room->advertisement->last_update_date)));
                                $offer->appendChild($lastUpdateDate);
                                
                                // Promo
                                $promo = $dom->createElement("promo", 0);
                                $offer->appendChild($promo);

                                // Location Section
                                $location = $this->processLocationNode($dom, $room);
                                $offer->appendChild($location);

                                // Sales Agent section
                                $salesAgent = $this->processSalesAgentNode($dom, $advertisement);
                                $offer->appendChild($salesAgent);

                                // Price section
                                $price = $this->processPriceNode($dom, $room);
                                $offer->appendChild($price);

                                // Deal Status section
                                $dealStatus = $this->processDealStatusNode($dom, $room);
                                $offer->appendChild($dealStatus);

                                // Area nodes section
                                $areas = $this->processRoomAreaNodes($dom, $room);
                                foreach ($areas as $areaNode) {
                                    $offer->appendChild($areaNode);
                                }

                                // Rooms
                                $rooms = $this->appendIfExists($dom, $offer, 'rooms',  $room->rooms);
                                if (!$rooms) {
                                    $defaultRooms = $dom->createElement("rooms", 1);
                                    $offer->appendChild($defaultRooms);
                                    unset($defaultRooms);
                                }

                                // Studio
                                $studio = $this->appendIfExists($dom, $offer, 'studio',  $room->is_studio);

                                // Floor
                                $floor = $this->appendIfExists($dom, $offer, 'floor',  $room->floor);

                                // Total floors
                                $totalFloors = $this->appendIfExists($dom, $offer, 'floors-total',  $room->total_floors);

                                // Balcony
                                $balcony = $this->processBalconyNode($dom, $room);
                                if ($balcony !== false) {
                                    $offer->appendChild($balcony);
                                }

                                // Window view
                                $windowView = $this->processWindowViewNode($dom, $room);
                                if ($windowView !== false) {
                                    $offer->appendChild($windowView);
                                }

                                // Built year
                                $builtYear = $this->appendIfExists($dom, $offer, 'built-year',  $room->built_year);

                                // Ceiling height
                                $ceilingHeight = $this->appendIfExists($dom, $offer, 'ceiling-height',  $room->ceiling_height);

                                // Rubbish chute
                                $rubbishChute = $this->appendIfExists($dom, $offer, 'rubbish-chute',  $room->rubbish_chute);

                                // Description
                                $description = $dom->createElement('description', preg_replace("/&#?[a-z0-9]+;/i","", htmlentities($room->detail)));
                                $offer->appendChild($description);

                                if (count($room->images) > 0) {
                                    foreach($room->images as $img) {
                                        $imgNode = $this->processImageNode($dom, $img);
                                        if ($imgNode !== false) {
                                            $offer->appendChild($imgNode);
                                        }
                                    }
                                }

                            }
                            break;
                        // Offer for a house
                        case 2:
                            if ($room->advertisement->deal_type	=== 1) {
                                
                                // Deal type
                                $type = $dom->createElement("type", "продажа");
                                $offer->appendChild($type);
                                
                                // Real estate type
                                $propertyType = $dom->createElement("property-type", "жилая");
                                $offer->appendChild($propertyType);
                                
                                // Object category
                                $categoryValue = '';
                                if ($this->custom_mb_in_array($room->category->name, ['таунхаус', 'дом', 'часть дома', 'дача'])) {
                                    $categoryValue = $room->category->name;
                                }
                                $category = $dom->createElement("category", !empty($categoryValue) ? $categoryValue : "дом");
                                $offer->appendChild($category);
                                
                                // Creation date
                                $creationDate = $dom->createElement("creation-date", date('c', strtotime($room->advertisement->creation_date)));
                                $offer->appendChild($creationDate);
                                
                                // Last update date
                                $lastUpdateDate = $dom->createElement("last-update-date", date('c', strtotime($room->advertisement->last_update_date)));
                                $offer->appendChild($lastUpdateDate);
                                
                                // Promo
                                $promo = $dom->createElement("promo", 0);
                                $offer->appendChild($promo);

                                // Location Section
                                $location = $this->processLocationNode($dom, $room);
                                $offer->appendChild($location);

                                // Sales Agent section
                                $salesAgent = $this->processSalesAgentNode($dom, $advertisement);
                                $offer->appendChild($salesAgent);

                                // Price section
                                $price = $this->processPriceNode($dom, $room);
                                $offer->appendChild($price);

                                // Area nodes section
                                $areas = $this->processRoomAreaNodes($dom, $room);
                                foreach ($areas as $areaNode) {
                                    $offer->appendChild($areaNode);
                                }

                                // Rooms
                                $rooms = $this->appendIfExists($dom, $offer, 'rooms',  $room->rooms);

                                // Total floors
                                $totalFloors = $this->appendIfExists($dom, $offer, 'floors-total',  $room->total_floors);

                                 // Built year
                                $builtYear = $this->appendIfExists($dom, $offer, 'built-year',  $room->built_year);

                                // Ceiling height
                                $ceilingHeight = $this->appendIfExists($dom, $offer, 'ceiling-height',  $room->ceiling_height);

                                // Rubbish chute
                                $rubbishChute = $this->appendIfExists($dom, $offer, 'rubbish-chute',  $room->rubbish_chute);

                                // Description
                                $description = $dom->createElement('description', preg_replace("/&#?[a-z0-9]+;/i","", htmlentities($room->detail)));
                                $offer->appendChild($description);

                                if (count($room->images) > 0) {
                                    foreach($room->images as $img) {
                                        $imgNode = $this->processImageNode($dom, $img);
                                        if ($imgNode !== false) {
                                            $offer->appendChild($imgNode);
                                        }
                                    }
                                }

                            }
                            break;
                        // Offer for a land plot
                        case 3:
                            if ($room->advertisement->deal_type	=== 1) {
                                
                                // Deal type
                                $type = $dom->createElement("type", "продажа");
                                $offer->appendChild($type);
                                
                                // Real estate type
                                $propertyType = $dom->createElement("property-type", "жилая");
                                $offer->appendChild($propertyType);
                                
                                // Object category
                                $category = $dom->createElement("category", "участок");
                                $offer->appendChild($category);
                                
                                // Creation date
                                $creationDate = $dom->createElement("creation-date", date('c', strtotime($room->advertisement->creation_date)));
                                $offer->appendChild($creationDate);
                                
                                // Last update date
                                $lastUpdateDate = $dom->createElement("last-update-date", date('c', strtotime($room->advertisement->last_update_date)));
                                $offer->appendChild($lastUpdateDate);
                                
                                // Promo
                                $promo = $dom->createElement("promo", 0);
                                $offer->appendChild($promo);

                                // Location Section
                                $location = $this->processLocationNode($dom, $room);
                                $offer->appendChild($location);

                                // Sales Agent section
                                $salesAgent = $this->processSalesAgentNode($dom, $advertisement);
                                $offer->appendChild($salesAgent);

                                // Price section
                                $price = $this->processPriceNode($dom, $room);
                                $offer->appendChild($price);

                                // Area nodes section
                                $area = $dom->createElement('area');
                                $areaValue = $dom->createElement('value', !empty($room->area ? $room->area : 'не указано'));
                                $area->appendChild($areaValue);
                                $offer->appendChild($area);

                                // Description
                                $description = $dom->createElement('description', preg_replace("/&#?[a-z0-9]+;/i","", htmlentities($room->detail)));
                                $offer->appendChild($description);

                                if (count($room->images) > 0) {
                                    foreach($room->images as $img) {
                                        $imgNode = $this->processImageNode($dom, $img);
                                        if ($imgNode !== false) {
                                            $offer->appendChild($imgNode);
                                        }
                                    }
                                }

                            }
                            break;
                        case 4:
                            // Deal type
                            $type = $dom->createElement("type", $advertisement->deal_type === 2 ? "аренда" : "продажа");
                            $offer->appendChild($type);
                            
                            // Real estate type
                            $propertyType = $dom->createElement("property-type", "коммерческая");
                            $offer->appendChild($propertyType);
                            
                            // Object category
                            $category = $dom->createElement("category", "помещения свободного назначения");
                            $offer->appendChild($category);
                            
                            // Creation date
                            $creationDate = $dom->createElement("creation-date", date('c', strtotime($room->advertisement->creation_date)));
                            $offer->appendChild($creationDate);
                            
                            // Last update date
                            $lastUpdateDate = $dom->createElement("last-update-date", date('c', strtotime($room->advertisement->last_update_date)));
                            $offer->appendChild($lastUpdateDate);
                            
                            // Promo
                            $promo = $dom->createElement("promo", 0);
                            $offer->appendChild($promo);

                            // Location Section
                            $location = $this->processLocationNode($dom, $room);
                            $offer->appendChild($location);

                            // Sales Agent section
                            $salesAgent = $this->processSalesAgentNode($dom, $advertisement);
                            $offer->appendChild($salesAgent);

                            // Price section
                            $price = $this->processPriceNode($dom, $room);
                            $offer->appendChild($price);

                            // Deal status
                            if ($advertisement->deal_type === 2) {
                                $dealStatus = $dom->createElement('deal-status', "аренда");
                                $offer->appendChild($dealStatus);
                                unset($dealStatus);
                            }

                            // Area nodes section
                            $areas = $this->processRoomAreaNodes($dom, $room);
                            foreach ($areas as $areaNode) {
                                $offer->appendChild($areaNode);
                            }

                            // Description
                            $description = $dom->createElement('description', preg_replace("/&#?[a-z0-9]+;/i","", htmlentities($room->detail)));
                            $offer->appendChild($description);

                            if (count($room->images) > 0) {
                                foreach($room->images as $img) {
                                    $imgNode = $this->processImageNode($dom, $img);
                                    if ($imgNode !== false) {
                                        $offer->appendChild($imgNode);
                                    }
                                }
                            }
                            break;
                    }
                    $root->appendChild($offer);
                }
            }
        }
        
        $dom->appendChild($root);
        $dom->save(storage_path('app/public/feeds/secondary/fbn_upload.xml'));

        return Command::SUCCESS;
    }

    private function appendIfExists($dom, $parentNode, $targetNodeName,  $valueToAppend) {
        if (!empty($valueToAppend) && !is_null($valueToAppend)) {
            $node = $dom->createElement($targetNodeName, $valueToAppend);
            $parentNode->appendChild($node);
            return true;
        }
        return false;
    }

    private function appendLocationEntityById($dom, $locationNode, $targetNodeName, $locationEntity)
    {
        if (!empty($locationEntity) && !is_null($locationEntity)) {
            $localityName = $locationEntity->name;
        }
        if (!empty($localityName)) {
            $localityNode = $dom->createElement($targetNodeName, $localityName);
            $locationNode->appendChild($localityNode);
            return true;
        } else {
            return false;
        }
    }

    private function processLocationNode($dom, $room) {
        $location = $dom->createElement("location");

        $location_info = !empty($room->location_info) ? json_decode(json_decode($room->location_info), true) : false;

        // country
        $country = $dom->createElement("country", "Россия");
        $location->appendChild($country);

        // region
        $regionById = $this->appendLocationEntityById($dom, $location, 'region', $room->region);
        
        // region district
        $regionDistrictById = $this->appendLocationEntityById($dom, $location, 'district', $room->regionDistrict);
        
        // city, town etc.
        $cityById = $this->appendLocationEntityById($dom, $location, 'locality-name', $room->city);
        
        // district of a city
        $districtById = $this->appendLocationEntityById($dom, $location, 'sub-locality-name', $room->district);

        // address (street and number)
        if (!empty($room->address) && !is_null($room->address)) {
            $address = $dom->createElement("address", $room->address);
            $location->appendChild($address);
        }

        return $location;
    }

    private function processSalesAgentNode($dom, $advertisement)
    {
        $salesAgent = $dom->createElement("sales-agent");
        // author 
        $author = array();
        if (!empty($advertisement->author_id) && !is_null($advertisement->author_id)) {
            $author = User::getUserById($advertisement->author_id)->toArray(); 
        } elseif (!empty($advertisement->author_info) && !is_null($advertisement->author_info)) {
            $author = json_decode(json_decode($advertisement->author_info), true);
            //var_dump($author); die;
        }
        if (count($author) > 0) {
            // name
            $name_string = 'имя не указано';
            if (array_key_exists("first_name", $author)) {
                $last_name = !empty($author['last_name'] && !is_null($author['last_name'])) ? trim($author['last_name']).' ' : '';
                $middle_name = !empty($author['middle_name'] && !is_null($author['middle_name'])) ? ' '.trim($author['middle_name']) : '';
                $name_string = $last_name.trim($author['first_name']).$middle_name;
            } else {
                $name_string = $author['name'];
            }
            if (!empty($name_string)) {
                $name = $dom->createElement("name", $name_string);
                $salesAgent->appendChild($name);
            }
            // phone
            $phone_string = $advertisement->agency->phone;
            if (array_key_exists("phone", $author)) {
                if (!empty($author['phone']) && !is_null($author['phone'])) {
                    $phone_string = $author['phone'];
                }
            } elseif (array_key_exists("phones", $author)) {
                if (is_array($author['phones']) && count($author['phones']) > 0) {
                    $phone_string = implode(', ', $author['phones']);
                }
            }
            if (!empty($phone_string)) {
                $phone = $dom->createElement("phone", $phone_string);
                $salesAgent->appendChild($phone);
            }
        }
        // organization (agency)
        $this->appendIfExists($dom, $salesAgent, 'organization', $advertisement->agency->name);
        $this->appendIfExists($dom, $salesAgent, 'organization-url', $advertisement->agency->url);
        $this->appendIfExists($dom, $salesAgent, 'organization-phone', $advertisement->agency->phone);

        //$offer->appendChild($salesAgent);
        return $salesAgent;
    }

    private function processPriceNode($dom, $room)
    {
        $price = $dom->createElement('price');
        $value = $dom->createElement('value', !empty($room->price && !is_null($room->price)) ? $room->price : 0 );
        $price->appendChild($value);
        $currency = $dom->createElement('currency', 'RUR');
        $price->appendChild($currency);
        return $price;
    }

    private function processDealStatusNode($dom, $room)
    {
        $statusesSet = ['прямая продажа', 'встречная продажа', 'обмен', 'расселение', 'переуступка'];
        $value = '';
        if ($this->custom_mb_in_array($room->advertisement->deal_status_string, $statusesSet)) {
            $value = $room->advertisement->deal_status_string;
        } else {
            $value = 'прямая продажа';
        }
        $dealStatus = $dom->createElement("deal-status", $value);
        return $dealStatus;
    }

    private function processRoomAreaNodes($dom, $room)
    {
        $areaNodes = array();

        // total area
        $area = $dom->createElement('area');
        $value = $dom->createElement('value', !empty($room->area) && !is_null($room->area) ? $room->area : 0);
        $unit = $dom->createElement('unit', 'кв. м');
        $area->appendChild($value);
        $area->appendChild($unit);
        
        array_push($areaNodes, $area);

        // living area
        if (!empty($room->living_area) && !is_null($room->living_area)) {
            $livingSpace = $dom->createElement('living-space');
            $value = $dom->createElement('value', $room->living_area);
            $unit = $dom->createElement('unit', 'кв. м');
            $livingSpace->appendChild($value);
            $livingSpace->appendChild($unit);

            array_push($areaNodes, $livingSpace);
        }

        // kitchen area
        if (!empty($room->kitchen_area) && !is_null($room->kitchen_area)) {
            $kitchenSpace = $dom->createElement('kitchen-space');
            $value = $dom->createElement('value', $room->kitchen_area);
            $unit = $dom->createElement('unit', 'кв. м');
            $kitchenSpace->appendChild($value);
            $kitchenSpace->appendChild($unit);

            array_push($areaNodes, $kitchenSpace);
        }

        return $areaNodes;
    }

    private function processBalconyNode($dom, $room)
    {
        $balcony_string = '';

        if (
            (!is_null($room->balcony_amount) && $room->balcony_amount)
            || (!is_null($room->loggia_amount) && $room->loggia_amount)
        ) {
            if ($room->balcony_amount === 1 && $room->loggia_amount === 1) {
                $balcony_string = "Балкон и лоджия";
            } elseif ($room->balcony_amount > 0) {
                switch ($room->balcony_amount) {
                    case 2:
                        $balcony_string = "2 балкона";
                        break;
                    case 3:
                        $balcony_string = "3 балкона";
                        break;
                    default:
                        $balcony_string = "Балкон";
                }
            } elseif ($room->loggia_amount > 0) {
                switch ($room->loggia_amount) {
                    case 2:
                        $balcony_string = "2 лоджии";
                        break;
                    default:
                        $balcony_string = "Лоджия";
                }
            }
        } elseif (!empty($room->balcony_info) && !is_null($room->balcony_info)) {
            $valuesSet = ['Балкон', 'Лоджия', 'Нет', 'Терраса', 'Эркер', '2 балкона', '3 балкона', 'Застекленная лоджия', 'Застекленный балкон', '2 лоджии', '2 эркера', 'Веранда', 'Балкон и лоджия'];
            if ($this->custom_mb_in_array($room->balcony_info, $valuesSet)) {
                $balcony_string = $room->balcony_info;
            }
        }

        if (!empty($balcony_string)) {
            $result = $dom->createElement('balcony', $balcony_string);
            return $result;
        }

        return false;
    }

    private function processWindowViewNode($dom, $room)
    {
        $windowview_string = '';

        if (
            (!is_null($room->windowview_street) && $room->windowview_street)
            || (!is_null($room->windowview_yard) && $room->windowview_yard)
        ) {
            if ($room->windowview_street === 1 && $room->windowview_yard === 1) {
                $windowview_string = "На улицу и во двор";
            } elseif ($room->windowview_street === 1) {
                $windowview_string = "На улицу";
            } elseif ($room->windowview_yard === 1) {
                $windowview_string = "Во двор";
            }
        } elseif (!empty($room->windowview_info) && !is_null($room->windowview_info)) {
            $valuesSet = ['На 2 стороны', 'На 3 стороны', 'Во двор', 'На улицу', 'На улицу и во двор'];
            if ($this->custom_mb_in_array($room->windowview_info, $valuesSet)) {
                $windowview_string = $room->windowview_info;
            }
        }

        if (!empty($windowview_string)) {
            $result = $dom->createElement('window-view', $windowview_string);
            return $result;
        }

        return false;
    }

    private function processImageNode($dom, $img)
    {
        $img_url = '';
        if ($img->location_type === 'remote') {
            $img_url = $img->url;
        } elseif ($img->location_type === 'local') {
            $img_url = 'https://grch.ru/uploads/'.$img->filename;
        }
        if (!empty($img_url)) {
            $imgNode = $dom->createElement('image', $img_url);
            return $imgNode;
        }
        return false;
    }

    // utilities
    private function custom_mb_in_array($_needle, array $_hayStack) {
        foreach ($_hayStack as $value) {
            if (mb_strtolower($value) === mb_strtolower($_needle)) {
                return true;
            }
        }
        return false;   
    }
}
