<?php
include_once 'includes/config.php';

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    if (empty($_GET['keyword'])) {
        $noresult = [
            'content' => ''
        ];
        echo json_encode($noresult);
        die();
    }

    // Build API URLs for movie and TV search
    $searchresultmv = $APIbaseURL . $searchmovie . $api_key . "&query=" . urlencode($_GET['keyword']);
    $searchresulttv = $APIbaseURL . $searchtv . $api_key . "&query=" . urlencode($_GET['keyword']);

    // Function to fetch data using cURL
    function fetchDataWithCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL host verification

        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            error_log('cURL error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Check HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            error_log("HTTP error: $httpCode for URL: $url");
            return false;
        }

        return $response;
    }

    // Fetch search results for movies and TV shows using cURL
    $ambil = fetchDataWithCurl($searchresultmv);
    $ambiltv = fetchDataWithCurl($searchresulttv);

    // Handle errors
    if ($ambil === false || $ambiltv === false) {
        $noresult = [
            'content' => ''
        ];
        echo json_encode($noresult);
        die();
    }

    // Decode JSON responses
    $searchresultmv = json_decode($ambil, true);
    $searchresulttv = json_decode($ambiltv, true);

    // Slugify function
    function slugify($text)
    {
        // Replace non-letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // Trim
        $text = trim($text, '-');

        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // Lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    // Generate HTML content for search results
    $div = '<ul style="margin-bottom: 0;">';
    if (!empty($searchresultmv["results"])) {
        foreach ($searchresultmv["results"] as $movie) {
            $div .= '<li><div class="tpe">movie</div><a href="/movies/' . $movie["id"] . '/' . slugify($movie["title"]) . '" class="ss-title">' . htmlspecialchars($movie["title"]) . '</a></li>';
        }
    }
    if (!empty($searchresulttv["results"])) {
        foreach ($searchresulttv["results"] as $tv) {
            $div .= '<li><div class="tpe">tv</div><a href="/tv/' . $tv["id"] . '/' . slugify($tv["name"]) . '" class="ss-title">' . htmlspecialchars($tv["name"]) . '</a></li>';
        }
    }
    $div .= '</ul>';

    // Return JSON response
    $result = [
        'content' => $div
    ];
    echo json_encode($result);
}
?>