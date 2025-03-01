<?php
// Include files
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Get data
$latestmvs = $APIbaseURL . $trendingmovieday . $api_key . $language;

if (empty($_GET['id'])) {
    die('NOT FOUND');
}

$slug = $_GET['id'];
$getmovie = $APIbaseURL . $movie . $slug . $api_key . $language;

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

// Fetch latest movies using cURL
$ambil = fetchDataWithCurl($latestmvs);

// Handle errors
if ($ambil === false) {
    include '404.php';
    exit();
}

// Fetch current movie details using cURL
$ambilgetmovie = fetchDataWithCurl($getmovie);

// Handle errors
if ($ambilgetmovie === false) {
    include '404.php';
    exit();
}

// Decode JSON responses
$latestmovies = json_decode($ambil, true);
$currentmovie = json_decode($ambilgetmovie, true);

// Define constants
if (!defined('_FILE_')) {
    define("_FILE_", getcwd() . DIRECTORY_SEPARATOR . basename($_SERVER['PHP_SELF']), false);
}
if (!defined('_DIR_')) {
    define("_DIR_", getcwd(), false);
}

// Generate autoembed and trailer URLs
$autoembed = "https://vidsrc.me/embed/movie/" . $currentmovie['id'] . "/";
$trailer = 'https://'.$Domainname.'/trailer_movie.php?id='.$currentmovie['id'].'';

// Number of results to show in Trending
$trendng = 12;

// Trim year from release_date
$year = substr($currentmovie['release_date'], 0, 4);

// Page title
$metatitle = $currentmovie['title'] . " " . $year;

// Canonical URL
$canonical = "movies/" . $slug . "/" . slugify($currentmovie['title']);

// Page description
$pagedesc = "Watch " . $currentmovie['title'] . " " . $year . " full movie online in HD with subtitles, " . RemoveSpecialChar($currentmovie['overview']);

// Trim description to only 150 characters
$metadesc = substr($pagedesc, 0, 150) . "..";

// Page image
$metaimg = "https://image.tmdb.org/t/p/original" . $currentmovie['backdrop_path'];

// Meta schema
$metaschema = '<script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Movie",
            "@id": "https://' . $Domainname . '/movies/' . $slug . '/' . slugify($currentmovie['title']) . '",
            "aggregateRating": {
                "@type": "AggregateRating",
                "bestRating": "10",
                "ratingCount": "' . $currentmovie["vote_count"] . '",
                "ratingValue": "' . Ratingtwo($currentmovie["vote_average"]) . '"
            },
            "description": "' . $currentmovie['overview'] . '",
            "name": "' . $metatitle . '",
            "dateCreated": "' . $currentmovie['release_date'] . '",
            "image": "https://image.tmdb.org/t/p/w185' . $currentmovie['poster_path'] . '"
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "url": "https://' . $Domainname . '/",
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                "@type": "EntryPoint",
                "urlTemplate": "https://' . $Domainname . '/search/{search_term_string}"
                },
                "query-input": "required name=search_term_string"
            }
        }
    </script>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "@id": "https://' . $Domainname . '/movies/' . $slug . '/' . slugify($currentmovie['title']) . '",
            "name": "' . $metatitle . '",
            "itemListElement": [{
                    "@type": "ListItem",
                    "position": 1,
                    "name": "Home",
                    "item": "https://' . $Domainname . '"
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "name": "Movies",
                    "item": "https://' . $Domainname . '/movies"
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "name": "' . $currentmovie['title'] . '",
                    "item": "https://' . $Domainname . '/movies/' . $slug . '/' . slugify($currentmovie['title']) . '"
                }
            ]
        }
    </script>';
?>
<?php include_once 'includes/header.php'; ?> 
        <div id="container">
            <div class="module">
                <div class="content">
                    <div class="video-info-left">
                        <div class="content-more-js" id="rmjs-1">
                            <div class="watch_play">
                                <div class="play-video">
                                    <iframe id="player" src="<?php echo $autoembed; ?>" allowfullscreen="true" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
                                </div>
                                <div class="clr"></div>
                            </div>
                            <div class="dst">
                                <a href="javascript:;" data-src="<?php echo $trailer; ?>" id="trailer" title="<?php echo $currentmovie["title"]; ?> trailer"><i class="fas fa-play-circle"></i> Watch Trailer</a>
                                <a style="display:none"  id="loading">Loading...</a>
                                <a href="javascript:;" data-src="<?php echo $autoembed; ?>" id="watchm" style="display:none" title="<?php echo $currentmovie["title"]; ?> watch now"><i class="fas fa-play-circle"></i> Watch Movie</a>
                            </div>
                            <div class="clr"></div>
                            <div class="rgt">
                                <div class="rgtp">
                                    <h1><?php if ($currentmovie["title"]) echo $currentmovie["title"]; else echo ""; ?> - <?php echo $year ?></h1>
                                    <p><?php echo $currentmovie['release_date']; ?></p>
                                    <ul class="genre"><?php foreach ($currentmovie["genres"] as $genres) : ?><li><?php echo $genres["name"]; ?></li><?php endforeach ?></ul>
                                    <p><?php echo $currentmovie['overview']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="clr"></div>
                        <!---------------banner-ad-------------------->
                        <?php include_once 'includes/banner-ad-code.php'; ?> 
                        <!---------------banner-ad-------------------->
                        <div class="clr"></div>
                    </div>
                    <div class="video-info-right">
                        <h2 class="widget-title">Trending Movies</h2>
                        <div class="animation-2 items"><?php foreach (array_slice($latestmovies["results"], 0, $trendng) as $latestmovies) : ?> 
                            <article class="item movies">
                                <div class="poster"><img src="https://image.tmdb.org/t/p/w185<?php echo $latestmovies["poster_path"]; ?>" alt="<?php echo $latestmovies["title"]; ?>">
                                    <div class="rating"><i class="fa fa-star"></i> <?php echo Ratingtwo($latestmovies["vote_average"]); ?></div>
                                    <div class="mepo"> </div>
                                    <a href="/movies/<?php echo $latestmovies["id"]; ?>/<?php echo slugify($latestmovies["title"]); ?>"><div class="see play3"></div></a>
                                </div>
                                <div class="data">
                                    <h3><a href="/movies/<?php echo $latestmovies["id"]; ?>/<?php echo slugify($latestmovies["title"]); ?>"><?php echo $latestmovies["title"]; ?></a></h3> <span><?php echo $latestmovies["release_date"]; ?></span>
                                </div>
                            </article><?php endforeach ?> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="breadcrumb">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/movies">Movies</a></li>
                    <li><?php echo $currentmovie['title']; ?></li>
                </ul>
            </div>
        </div>
<?php include_once 'includes/footer.php'; ?>