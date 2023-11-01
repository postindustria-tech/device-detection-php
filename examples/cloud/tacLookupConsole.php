<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2023 51 Degrees Mobile Experts Limited, Davidson House,
 * Forbury Square, Reading, Berkshire, United Kingdom RG1 3EU.
 *
 * This Original Work is licensed under the European Union Public Licence
 * (EUPL) v.1.2 and is subject to its terms as set out below.
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
 * @example cloud/tacLookupConsole.php
 *
 * This example shows how to use the 51Degrees Cloud service to lookup the details of a device 
 * based on a given 'TAC'. More background information on TACs can be found through various online 
 * sources such as <a href="https://en.wikipedia.org/wiki/Type_Allocation_Code">Wikipedia</a>.
 * 
 * This example is available in full on [GitHub](https://github.com/51Degrees/device-detection-php/blob/master/examples/cloud/tacLookupConsole.php). 
 * 
 * @include{doc} example-require-resourcekey.txt
 *
 * Required Composer Dependencies:
 * - 51degrees/fiftyone.devicedetection
 */

require_once(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\core\Logger;
use fiftyone\pipeline\devicedetection\examples\cloud\classes\ExampleUtils;
use fiftyone\pipeline\devicedetection\examples\cloud\classes\TacLookupConsole;

// Only declare and call the main function if this is being run directly.
// This prevents main from being run where examples are run as part of
// PHPUnit tests.
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]))
{
    function main($argv)
    {
        // Use the command line args to get the resource key if present.
        // Otherwise, get it from the environment variable.
        $resourceKey = isset($argv) && count($argv) > 0 ? $argv[0] : ExampleUtils::getResourceKey();
        
        // Configure a logger to output to the console.
        $logger = new Logger("info");

        // Load the configuration file
        $config = json_decode(file_get_contents(__DIR__."/tacLookupConsole.json"), true);
        // Get the resource key setting from the config file. 
        $resourceKeyFromConfig = ExampleUtils::getResourceKeyFromConfig($config);
        $configHasKey = empty($resourceKeyFromConfig) == false && strpos($resourceKeyFromConfig, "!!") !== 0;

        // If no resource key is specified in the config file then override it with the key
        // from the environment variable / command line. 
        if ($configHasKey === false)
        {
            ExampleUtils::setResourceKeyInConfig($config, $resourceKey);
        }

        if (empty($resourceKey) == false)
        {
            (new TacLookupConsole())->run($config, $logger, ['fiftyone\\pipeline\\devicedetection\\examples\\cloud\\classes\\ExampleUtils', 'output']);
        }
        else
        {
            $logger->log("error",
                "No resource key specified on the command line or in " .
                "the environment variable '".ExampleUtils::RESOURCE_KEY_ENV_VAR."'. " .
                "The 51Degrees cloud service is accessed using a 'ResourceKey'. " .
                "For more information see " .
                "http://51degrees.com/documentation/_info__resource_keys.html. " .
                "TAC lookup is not available as a free service. This means " .
                "that you will first need a license key, which can be purchased " .
                "from our pricing page: http://51degrees.com/pricing. Once this is " .
                "done, a resource key with the properties required by this example " .
                "can be created at https://configure.51degrees.com/QKyYH5XT. You " .
                "can now populate the environment variable mentioned at the start " .
                "of this message with the resource key or pass it as the first " .
                "argument on the command line.");
        }
    }

    main(isset($argv) ? array_slice($argv, 1) : null);
}
