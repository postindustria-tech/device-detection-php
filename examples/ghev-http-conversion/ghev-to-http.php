<?php
function qq(string $s): string {
    $s = trim($s);
    if (strlen($s) == 0) {
        return $s;
    }
    return '"' . $s . '"';
}

function brandVersionArrayToHTTP(array $clientHints, string $property): string {
    $header = '';
    $brandVersionArray = null;
    if (isset($clientHints[$property])) {
        $brandVersionArray = $clientHints[$property];
    }
    if (is_array($brandVersionArray)) {
        foreach($brandVersionArray as $brandVersion) {
            if (!array_key_exists('brand', $brandVersion) || !array_key_exists('version', $brandVersion)) {
                continue;
            }
            if (strlen($header)) {
                $header .= ', ';
            }
            $header .= sprintf('"%s";v="%s"', $brandVersion['brand'], $brandVersion['version']);
        }
    }
    return $header;
}

function ghevToHTTPHeaders(array $clientHints): array {
    $ua = brandVersionArrayToHTTP($clientHints, 'brands');
    $fullVersionList = brandVersionArrayToHTTP($clientHints, 'fullVersionList');

    return [
        'Sec-CH-UA: ' . $ua,
        'Sec-CH-UA-Arch: ' . qq($clientHints['architecture']),
        'Sec-CH-UA-Bitness: ' . qq($clientHints['bitness']),
        'Sec-CH-UA-Full-Version-List: ' . $fullVersionList,
        'Sec-CH-UA-Mobile: ?' . (int) ($clientHints['mobile'] ?? 0),
        'Sec-CH-UA-Model: ' . qq($clientHints['model']),
        'Sec-CH-UA-Platform: ' . qq($clientHints['platform']),
        'Sec-CH-UA-Platform-Version: ' . qq($clientHints['platformVersion']),
    ];
}

/* 
    see https://developer.mozilla.org/en-US/docs/Web/API/NavigatorUAData/getHighEntropyValues
    you can obtain an example JSON like below by running this code in a Chromium-based browser: 
    `console.log(JSON.stringify(await navigator.userAgentData.getHighEntropyValues(['architecture', 'bitness', 'model', 'platformVersion', 'fullVersionList'])))`
*/

$ghev = json_decode('{"architecture":"x86","bitness":"64","brands":[{"brand":"Chromium","version":"116"},{"brand":"Not)A;Brand","version":"24"},{"brand":"Google Chrome","version":"116"}],"fullVersionList":[{"brand":"Chromium","version":"116.0.5845.187"},{"brand":"Not)A;Brand","version":"24.0.0.0"},{"brand":"Google Chrome","version":"116.0.5845.187"}],"mobile":false,"model":"","platform":"macOS","platformVersion":"13.5.2"}', true);

$headers = ghevToHTTPHeaders($ghev);

echo json_encode($headers, JSON_PRETTY_PRINT) . "\n";
