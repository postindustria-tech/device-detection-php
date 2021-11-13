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
 * @example hash/userAgentClientHints.php
 * 
 * This example shows how a simple device detection pipeline that checks
 * if a User-Agent is a mobile device
 *
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/userAgentClientHints.php).

 * To run this example, you will need to create a **resource key**.
 * The resource key is used as short-hand to store the particular set of
 * properties you are interested in as well as any associated license keys
 * that entitle you to increased request limits and/or paid-for properties.
 * You can create a resource key using the 51Degrees [Configurator](https://configure.51degrees.com).
 * ```
 * 
 * Expected output:
 * 
 * ```
 * ---------------------------------------
 * This example demonstrates detection using user-agent client hints.
 * The sec-ch-ua value can be used to determine the browser of the connecting device, but not other components such as the hardware.
 * We show this by first performing detection with sec-ch-ua only.
 * We then repeat with the user-agent header set as well. Note that the client hint takes priority over the user-agent.
 * Finally, we use both sec-ch-ua and user-agent.Note that sec-ch-ua takes priority over the user-agent for detection of the browser.
 * ---------------------------------------
 * Sec-CH-UA = '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"'
 * User-Agent = 'NOT_SET'
 *         Browser = Chrome 89
 *         IsMobile = No matching profiles could be found for the supplied evidence.A 'best guess' can be returned by configuring more lenient matching rules.See https://51degrees.com/documentation/_device_detection__features__false_positive_control.html
 *
 * Sec-CH-UA = 'NOT_SET'
 * User-Agent = 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 Chrome/71.0.3578.99 Mobile Safari/537.36'
 *         Browser = Samsung Browser 10.1
 *         IsMobile = True
 *
 * Sec-CH-UA = '"Google Chrome";v="89", "Chromium";v="89", ";Not A Brand";v="99"'
 * User-Agent = 'Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 Chrome/71.0.3578.99 Mobile Safari/537.36'
 *         Browser = Chrome 89
 * ```
 *
 **/

// First we include the deviceDetectionPipelineBuilder

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\devicedetection\DeviceDetectionPipelineBuilder;

// We then create a pipeline with the builder. Create your own resource key for free at https://configure.51degrees.com.

// Check if there is a resource key in the environment variable and use
// it if there is one. You will need to switch this for your own resource key.

if (isset($_ENV["RESOURCEKEY"])) {
    $resourceKey = $_ENV["RESOURCEKEY"];
} else {
    $resourceKey = "!!YOUR_RESOURCE_KEY!!";
}



if ($resourceKey === "!!YOUR_RESOURCE_KEY!!") {
    echo "You need to create a resource key at " .
        "https://configure.51degrees.com and paste it into the code, " .
        "replacing !!YOUR_RESOURCE_KEY!!.";
    echo "\n<br/>";
    echo "Make sure to include the ismobile property " .
        "as it is used by this example.\n<br />";
    return;
}

else {
    echo "---------------------------------------</br>\n";
    echo "This example demonstrates detection " .
                    "using user-agent client hints.</br>\n";
    echo "The sec-ch-ua value can be used to " .
                    "determine the browser of the connecting device, " .
                    "but not other components such as the hardware.</br>\n";
    echo "We show this by first performing " .
                    "detection with sec-ch-ua only.</br>\n";
    echo "We then repeat with the user-agent " .
                    "header set as well. Note that the client hint takes " .
                    "priority over the user-agent.</br>\n";
    echo "Finally, we use both sec-ch-ua and " .
                    "user-agent. Note that sec-ch-ua takes priority " .
                    "over the user-agent for detection of the browser.</br>\n";
    echo "---------------------------------------</br></br>\n";

    $mobileUserAgent = "Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) " . 
                        "AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 " .
                            "Chrome/71.0.3578.99 Mobile Safari/537.36";
    $secchuaValue = "\"Google Chrome\";v=\"89\", \"Chromium\";v=\"89\", \";Not A Brand\";v=\"99\"";

    $builder = new DeviceDetectionPipelineBuilder(array(
        "resourceKey" => $resourceKey
    ));

    // Next we build the pipeline. We could additionally add extra engines and/or
    // flowElements here before building.

    // To stop having to construct the pipeline
    // and re-make cloud API requests used during construction on every page load,
    // we recommend caching the serialized pipeline to a database or disk.
    // Below we are using PHP's serialize and writing to a file if it doesn't exist

    $serializedPipelineFile = __DIR__ . "getting_started_pipeline.pipeline";
    if(!file_exists($serializedPipelineFile)){
        $pipeline = $builder->build();
        file_put_contents($serializedPipelineFile, serialize($pipeline));
    } else {
        $pipeline = unserialize(file_get_contents($serializedPipelineFile));
    }


    // Define function to analyze user-agent/client hints

    function analyzeClientHints($pipeline, $setUserAgent, $setSecChUa){

        $mobileUserAgent = "Mozilla/5.0 (Linux; Android 9; SAMSUNG SM-G960U) " . 
                            "AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/10.1 " .
                                "Chrome/71.0.3578.99 Mobile Safari/537.36";
        $secchuaValue = "\"Google Chrome\";v=\"89\", \"Chromium\";v=\"89\", \";Not A Brand\";v=\"99\"";

        // We create a FlowData object from the pipeline
        // this is used to add evidence to and then process  
        $flowData = $pipeline->createFlowData();

        // Add a value for the user-agent client hints header
        // sec-ch-ua as evidence 
        if ($setSecChUa){
            $flowData->evidence->set("query.sec-ch-ua", $secchuaValue);
        }
        // Also add a standard user-agent if requested 
        if ($setUserAgent){
            $flowData->evidence->set("query.user-agent", $mobileUserAgent);
        }

        // Now we process the flowData
        $result = $flowData->process();

        $device = $result->device;

        $browserName = $device->browsername;
        $browserVersion = $device->browserversion;
        $ismobile = $device->ismobile;

        // Output evidence
        echo "<strong>Evidence Used: </strong></br></n>";
        $secchua = "NOT_SET";
        if ($setSecChUa){
            $secchua = $secchuaValue;
        }
        echo sprintf("Sec-CH-UA = '%s'</br>\n", $secchua);

        $ua = "NOT_SET";
        if ($setUserAgent){
           $ua = $mobileUserAgent;
        }
        echo sprintf("User-Agent = '%s'</br>\n", $ua);


        // Output the Browser
        echo "<strong>Detection Results: </strong></br></n>";
        if ($browserName->hasValue && $browserVersion->hasValue){
            echo sprintf("Browser = %s %s</br>\n", $browserName->value, $browserVersion->value);
        }
        else if ($browserName->hasValue){
            echo sprintf("Browser = %s (version unknown)</br>\n", $browserName->value);
        }
        else{
            echo sprintf("Browser = %s</br>\n", $browserName->noValueMessage);
        }

        // Output the value of the 'IsMobile' property.
        if ($ismobile->hasValue){
            if($ismobile->value){
                $value = "Yes";
            } else {
                $value = "No";
            }
            echo sprintf("IsMobile = %s</br></br>\n", $value);
        }
        else{
            echo sprintf("IsMobile = %s</br></br>\n", $ismobile->noValueMessage);
        }
    }

    // first try with just sec-ch-ua.
    analyzeClientHints($pipeline, false, true);

    // Now with just user-agent.
    analyzeClientHints($pipeline, true, false);

    // Finally, perform detection with both.
    analyzeClientHints($pipeline, true, true);
}
