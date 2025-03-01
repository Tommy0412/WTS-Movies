<?php
// Include configuration and helper files
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if 'id' parameter is provided
if (empty($_GET['id'])) {
    die('ID parameter is missing.');
}

// Get the TMDB ID from the query string
$id = intval($_GET['id']);

// Build the API URL to fetch videos (trailers)
$apiUrl = $APIbaseURL . $movie . $id . "/videos" . $api_key;

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

// Fetch video data from TMDB API
$response = fetchDataWithCurl($apiUrl);

// Handle errors
if ($response === false) {
    die('Error fetching trailer data.');
}

// Decode JSON response
$data = json_decode($response, true);

// Check if the response contains any trailers
if (empty($data['results'])) {
    die('No trailers available for this movie.');
}

// Find the first YouTube trailer
$trailerKey = null;
foreach ($data['results'] as $video) {
    if ($video['site'] === 'YouTube' && $video['type'] === 'Trailer') {
        $trailerKey = $video['key'];
        break;
    }
}

// If no YouTube trailer is found, show an error
if (empty($trailerKey)) {
    die('No YouTube trailer available for this movie.');
}

// Generate the YouTube embed URL
$youtubeEmbedUrl = "https://www.youtube.com/embed/" . $trailerKey . "?autoplay=1&controls=0&rel=0&modestbranding=1&playsinline=0&fs=1";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trailer - Fullscreen</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <!-- Embed YouTube Trailer -->
    <iframe src="<?php echo htmlspecialchars($youtubeEmbedUrl); ?>" allowfullscreen></iframe>
</body>
</html>