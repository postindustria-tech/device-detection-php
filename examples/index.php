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



require(__DIR__ . "/../vendor/autoload.php");

// Use without the device detection pipeline builder

use fiftyone\pipeline\cloudrequestengine\cloudRequestEngine;
use fiftyone\pipeline\devicedetection\deviceDetectionCloud;
use fiftyone\pipeline\core\pipelineBuilder;

$cloud = new cloudRequestEngine();

// To access properties other than IsMobile, create your own recource 
// key for free at https://configure.51degrees.com. 
$cloud->setResourceKey("AQS5HKcy0uPi3zrv1kg");

// To access paid-for properties, you will also need to enter your license key.
//$cloud->setLicenseKey("<YOUR LICENSE KEY>");

$deviceDetection = new deviceDetectionCloud();

$deviceDetection->setRestrictedProperties(array("ismobile"));

$pipeline = new pipelineBuilder();

$pipeline = $pipeline->add($cloud)->add($deviceDetection)->build();

$fd = $pipeline->createFlowData();

$fd->evidence->setFromWebRequest();

$result = $fd->process();

$result->get("device")->get("ismobile");

$result->getFromElement($deviceDetection)->get("ismobile");

$result->getWhere("type", "boolean");

// Using the device detection pipeline builder

use fiftyone\pipeline\devicedetection\deviceDetectionPipelineBuilder;

$deviceDetectionPipeline = new deviceDetectionPipelineBuilder(array(
    // To access properties other than IsMobile, create your own recource 
    // key for free at https://configure.51degrees.com. 
    "resourceKey" => "AQS5HKcy0uPi3zrv1kg",
    // To access paid-for properties, you will also need to enter your license key.
    //"licenseKey" => "<YOUR LICENSE KEY>",
    "restrictedProperties" => array() // All properties by default
));

$deviceDetectionPipeline = $deviceDetectionPipeline->build();

$flowData = $deviceDetectionPipeline->createFlowData();

$flowData->evidence->setFromWebRequest();

$pipelineResult = $flowData->process();

$javascript = $pipelineResult->get("javascriptbundler")->get("javascript");

echo "<script>";
echo $javascript;
echo "</script>";
