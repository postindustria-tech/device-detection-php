<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2019 51 Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY.
 *
 * This Original Work is licensed under the European Union Public Licence (EUPL) 
 * v.1.2 and is subject to its terms as set out below.
 *
 * If a copy of the EUPL was not distributed with this file, You can obtain
 * one at https://opensource.org/licenses/EUPL-1.2.
 *
 * The 'Compatible Licences' set out in the Appendix to the EUPL (as may be
 * amended by the European Commission) shall be deemed incompatible for
 * the purposes of the Work and the provisions of the compatibility
 * clause in Article 5 of the EUPL shall not apply.
 * 
 * If using the Work as, or as part of, a network application, by 
 * including the attribution notice(s) required under Article 5 of the EUPL
 * in the end user terms of the application under an appropriate heading, 
 * such notice(s) shall fulfill the requirements of that article.
 * ********************************************************************* */

 
namespace fiftyone\pipeline\devicedetection;

use fiftyone\pipeline\engines\aspectDataDictionary;
use fiftyone\pipeline\engines\engine;
use fiftyone\pipeline\engines\aspectPropertyValue;

class deviceDetectionCloud extends engine {

    public $dataKey = "device";

    public function processInternal($flowData) {

        if(count($this->properties) === 0){

            $cloudProperties = $flowData->get("cloud")->get("properties");
    
            $cloudProperties = $cloudProperties["Products"]["device"]["Properties"];

            foreach ($cloudProperties as $property){

                $propertyName = strtolower($property["Name"]);

                $this->properties[$propertyName] = $property;

            }

            $this->updatePropertyList();

        }

        $cloudData = $flowData->get("cloud")->get("cloud");

        
        if($cloudData){
            
            $cloudData = \json_decode($cloudData, true);

            $nullValueReasons = $cloudData["nullValueReasons"];

            $result = [];

            foreach ($cloudData["device"] as $key => $value){

                if(isset($cloudData["nullValueReasons"]["device." . $key])){
                    
                    $result[$key] = new aspectPropertyValue($cloudData["nullValueReasons"]["device." . $key]);

                } else {

                    $result[$key] = new aspectPropertyValue(null, $value);

                }

            };

            $deviceData = $result;

            $data = new aspectDataDictionary($this, $deviceData);
            
            $flowData->setElementData($data);

        }

        return;

    }

}

