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

/**
 * @example cloud/failureToMatch.php
 * 
 * This examples shows how the hasValue function can help make sure that
 * meaningful values are returned when checking properties returned from 
 * the device detection engine.
 *
**/

// First we include the deviceDetectionPipelineBuilder

require(__DIR__ . "/../../vendor/autoload.php");
use fiftyone\pipeline\devicedetection\deviceDetectionPipelineBuilder;

// We then create a pipeline with the builder. To access properties other than 
// IsMobile, create your own recource key for free at https://configure.51degrees.com. 
// To access paid-for properties, you will also need to enter your license key.
$resourceKey = "!!YOUR_RESOURCE_KEY!!";
// Check if there is a resource key in the enviornemnt variable and use
// it if there is one.
$envKey = getenv("RESOURCEKEY");
if($envKey !== false){
    $resourceKey = $envKey;
}

if(substr($resourceKey, 0, 2) == "!!") {
    echo "You need to create a resource key at " .
        "https://configure.51degrees.com and paste it into the code, " .
        "replacing !!YOUR_RESOURCE_KEY!!.";
    echo "</br>";
    echo "Make sure to include the ismobile property " .
        "as it is used by this example.";
}
else 
{
    $builder = new deviceDetectionPipelineBuilder(array(
        "resourceKey" => $resourceKey,
        "restrictedProperties" => array() // All properties by default
    ));

    // Next we build the pipeline. We could additionally add extra engines and/or
    // flowElements here before building.
    $pipeline = $builder->build();

    // Here we create a function that checks if a supplied user agent is a 
    // mobile device

    if(!function_exists("checkIfMobile")){

        function checkIfMobile($userAgent, $pipeline) {

            // We create the flowData object that is used to add evidence to and
            // read data from 
            $flowData = $pipeline->createFlowData();

            // We set the user agent
            $flowData->evidence->set("header.user-agent", $userAgent);

            // Now we process the flowData
            $result = $flowData->process();

            echo "Is user agent " . $userAgent . " a mobile device? \n";

            // First we check if the property we're looking for has a meaningful result

            if($result->device->ismobile->hasValue){

                var_dump($result->device->ismobile->value);

            } else {

                // If it doesn't have a meaningful result, we echo out the reason 
                // why it wasn't meaningful

                echo($result->device->ismobile->noValueMessage);

            }


        };

    }

    // Some example User Agents to test

    $iPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';
    checkIfMobile($iPhoneUA, $pipeline);
    // This User-Agent is from an iPhone. It should match correctly and be 
    // identified as a mobile device

    $modifiediPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 99_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';
    checkIfMobile($modifiediPhoneUA, $pipeline);
    // This User-Agent is from an iPhone but has been modified so it doesn't 
    // match exactly.
}