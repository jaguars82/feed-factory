<?php

namespace App\Console\Commands;

use App\Models\grch\Developer;
use App\Models\grch\NewbuildingComplex;
use App\Models\grch\Newbuilding;
use App\Models\grch\Flat;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateVladisFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vladis:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Array of developer's idies to include in feed
     * 
     * @var array
     */
    protected static $selectedDevelopersIdies = [
        11, // Vybor
        17, // Evrostroy
        19, // VMU-2
        22, // Comfort-Stroy
        24, // Legos
        26, // SU-35
    ];


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dom = new \DOMDocument("1.0", "utf-8");
        $root = $dom->createElement("realty-feed");
        $generationDate = $dom->createElement("generation-date", /*date('c')*/(new \DateTime('now', new \DateTimeZone('Europe/Moscow')))->format('c'));
        $root->appendChild($generationDate);

        // Traverse through developer's idies array and buil the feed
        if (count(self::$selectedDevelopersIdies)) {
            foreach (self::$selectedDevelopersIdies as $developerId) {
                $developer = Developer::find($developerId);
                $complexes = $developer->newbuildingComplexesWithActiveBuildings;
                
                // echo $developer->name; echo PHP_EOL;
                
                // Iterate newbuilding complexes
                foreach ($complexes as $complex) {

                    // echo '-'.$complex->name; echo PHP_EOL;
                    
                    $buildings = $complex->newbuildingsActive;
                    
                    // Iterate newbuildings
                    foreach ($buildings as $building) {
                        
                        // echo '--'.$building->name; echo PHP_EOL;

                        $flats = $building->flatsSale;
                        
                        // Iterate flats && build DOM-node for each flat
                        foreach ($flats as $flat) {
                            $offer = $dom->createElement("offer");
                            $offer->setAttribute("internal-id", $flat->id);

                            // real-estate category
                            $category = $dom->createElement("category", "квартира");
                            $offer->appendChild($category);

                            // Deal type
                            $type = $dom->createElement("type", "продажа");
                            $offer->appendChild($type);

                            // Deal status
                            $dealStatus = $dom->createElement("deal-status", "первичная продажа");
                            $offer->appendChild($dealStatus);

                            // Property type
                            $propertyType = $dom->createElement("property-type", "жилая");
                            $offer->appendChild($propertyType);

                            // Creation date
                            $creationDate = $dom->createElement("creation-date", (new \DateTime($flat->created_at))
                            ->setTimezone(new \DateTimeZone('Europe/Moscow'))
                            ->format(DATE_ATOM));
                            $offer->appendChild($creationDate);

                            // Last update date
                            $lastUpdateDate = $dom->createElement("last-update-date", (new \DateTime($flat->updated_at))
                            ->setTimezone(new \DateTimeZone('Europe/Moscow'))
                            ->format(DATE_ATOM));
                            $offer->appendChild($lastUpdateDate);

                            // Manually added (set default value as in the example)
                            $manuallyAdded = $dom->createElement("manually-added", "да");
                            $offer->appendChild($manuallyAdded);

                            /** Location Section */
                            $location = $dom->createElement("location");

                            // Country
                            $country = $dom->createElement("country", "Россия");
                            $location->appendChild($country);

                            // Region
                            if (!empty($complex->region)) {
                                $region = $dom->createElement("region", $complex->region->name);
                                $location->appendChild($region);
                            }

                            // City/town/village etc.
                            if (!empty($complex->city)) {
                                $localityName = $dom->createElement("locality-name", $complex->city->name);
                                $location->appendChild($localityName);
                            }

                            // District of the city/town etc
                            if (!empty($complex->district)) {
                                $subLocalityName = $dom->createElement("sub-locality-name", $complex->district->name);
                                $location->appendChild($subLocalityName);
                            }

                            // Adress (street and building number)
                            $addressShort = $complex->addressShort();
                            if (!empty($addressShort)) {
                                $address = $dom->createElement("address", $addressShort);
                                $location->appendChild($address);
                            }

                            // Appartment number
                            if (!empty($flat->number)) {
                                $numberAppendix = !empty($flat->number_appendix) ? $flat->number_appendix : '';
                                $apartment = $dom->createElement("apartment", $flat->number.$numberAppendix);
                                $location->appendChild($apartment);
                            }

                            // Geo Coords
                            // (reverse longitude && latitude due to error in grch app)
                            if (!empty($complex->latitude)) {
                                $longitude = $dom->createElement('longitude', $complex->latitude);
                                $location->appendChild($longitude);
                            }
                            if (!empty($complex->longitude)) {
                                $latitude = $dom->createElement('latitude', $complex->longitude);
                                $location->appendChild($latitude);
                            }

                            $offer->appendChild($location);

                            /** Sales Agent Section (Developer) */
                            $salesAgent = $dom->createElement("sales-agent");

                            // Developer's name
                            $developerName = $dom->createElement("name", $developer->name);
                            $salesAgent->appendChild($developerName);

                            $developerCategory = $dom->createElement("category", "застройщик");
                            $salesAgent->appendChild($developerCategory);

                            if (!empty($developer->phone)) {
                                $developerPhone = $dom->createElement("phone", $developer->phone);
                                $salesAgent->appendChild($developerPhone);
                            }

                            $developerOrganization = $dom->createElement("organization", $developer->name);
                            $salesAgent->appendChild($developerOrganization);
                            
                            $offer->appendChild($salesAgent);

                            /** Price */
                            $price =  $dom->createElement("price");
                            $priceValue = $dom->createElement("value", $flat->price_cash);
                            $price->appendChild($priceValue);
                            $priceUnit = $dom->createElement("unit", "RUB");
                            $price->appendChild($priceUnit);
                            $offer->appendChild($price);

                            /** Area */
                            $area =  $dom->createElement("area");
                            $areaValue = $dom->createElement("value", $flat->area);
                            $area->appendChild($areaValue);
                            $areaUnit = $dom->createElement("unit", "кв.м");
                            $area->appendChild($areaUnit);
                            $offer->appendChild($area);

                            // Building Name
                            $buildingName =  $dom->createElement("building-name", $complex->name);
                            $offer->appendChild($buildingName);

                            // Building id
                            $yandexBuildingId =  $dom->createElement("yandex-building-id", $complex->id);
                            $offer->appendChild($yandexBuildingId);

                            // House id
                            $yandexHouseId =  $dom->createElement("yandex-house-id", $building->id);
                            $offer->appendChild($yandexHouseId);

                            // Built year, ready quarter & building state
                            if (!empty($building->deadline)) {
                                // Built year
                                $deadline = new \DateTime($building->deadline);
                                $builtYear = $dom->createElement("built-year", $deadline->format('Y'));
                                $offer->appendChild($builtYear);

                                // Ready quarter
                                $quarter = [
                                    1 => 1,
                                    2 => 2,
                                    3 => 3,
                                    4 => 4,
                                ];
                                $readyQuarter = $dom->createElement("ready-quarter", $quarter[(int)ceil($deadline->format('m') / 3)]);
                                $offer->appendChild($readyQuarter);

                                // Building state
                                if (Carbon::parse($deadline)->isPast()) {
                                    $state = 'finished';
                                } else {
                                    $state = 'unfinished';
                                }
                                $buildingState = $dom->createElement("building-state", $state);
                                $offer->appendChild($buildingState);
                            }

                            $newFlat = $dom->createElement("new-flat", 1);
                            $offer->appendChild($newFlat);

                            $rooms = $dom->createElement("rooms", $flat->rooms);
                            $offer->appendChild($rooms);

                            $floor = $dom->createElement("floor", $flat->floor);
                            $offer->appendChild($floor);

                            // Mortgage
                            $mortgage = $dom->createElement("mortgage", 1);
                            $offer->appendChild($mortgage);

                            // Material (building type)
                            if (!empty($building->material)) {
                                $buildingType = $dom->createElement("building-type", $building->material);
                                $offer->appendChild($buildingType);
                            }

                            // Complex description
                            if (!empty($complex->detail)) {
                                $desc = '<![CDATA[ '.htmlspecialchars($complex->detail).' ]]>';
                                $description = $dom->createElement("description", $desc);
                                $offer->appendChild($description);
                            }

                            // images
                            if ($complex->images !== null) {
                                foreach ($complex->images as $image) {
                                    $img = $dom->createElement("image", 'https://grch.ru/uploads/'.$image->file);
                                    $offer->appendChild($img);
                                }
                            }

                            $root->appendChild($offer);
                        }
                    }
                }
            }
        }

        $dom->appendChild($root);
        $dom->save(storage_path('app/public/feeds/share/yandex_schema.xml'));
        
        return Command::SUCCESS;
    }
}
