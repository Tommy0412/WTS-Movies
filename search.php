<?php
// Include files
include_once 'includes/config.php';
include_once 'includes/functions.php';

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    if (empty($_GET['keyword'])) {
        $noresult = [
            'content' => ''
        ];
        echo json_encode($noresult);
        die();
    }
}

// Latest Update SUB
$searchquery = $APIbaseURL . $searchmovie . $api_key . "&query=" . urlencode($_GET['keyword']);
$searchquerytv = $APIbaseURL . $searchtv . $api_key . "&query=" . urlencode($_GET['keyword']);

if (isset($_GET['page'])) {
    $searchquery = $APIbaseURL . $searchmovie . $api_key . "&query=" . urlencode($_GET['keyword']) . "&page=" . $_GET['page'];
    $searchquerytv = $APIbaseURL . $searchtv . $api_key . "&query=" . urlencode($_GET['keyword']) . "&page=" . $_GET['page'];
}

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

// Fetch search results for movies using cURL
$ambil = fetchDataWithCurl($searchquery);

// Fetch search results for TV shows using cURL
$ambiltv = fetchDataWithCurl($searchquerytv);

// Handle errors
if ($ambil === false || $ambiltv === false) {
    $noresult = [
        'content' => ''
    ];
    echo json_encode($noresult);
    die();
}

// Decode JSON responses
$searchresults = json_decode($ambil, true);
$searchresultstv = json_decode($ambiltv, true);

/*----meta---*/
$metatitle = $SiteTitle . ' - Search Result';
$metadesc = 'Search movies and TV shows.';
?>
<?php include_once 'includes/header.php'; ?>
        <div id="container">
            <div class="module">
                <div class="content right full">
                    <h1 class="heading-archive">Search results for <?php echo $_GET['keyword']; ?></h1>
                    <div class="animation-2 items full arch">
                        <h2 class="Featured">Movies</h2><?php foreach ($searchresults["results"] as $searchresults) : ?>
                            <article class="item">
                                <div class="poster"><img src="<?php if ($searchresults["poster_path"]) echo "https://image.tmdb.org/t/p/w185".$searchresults["poster_path"]; else echo "/img/noposter.png"; ?>" alt="<?php echo $searchresults["title"]; ?>">
                                    <div class="rating"><i class="fa fa-star"></i> <?php echo Ratingtwo($searchresults["vote_average"]); ?></div>
                                    <div class="mepo"> </div>
                                    <a href="/movies/<?php echo $searchresults["id"]; ?>/<?php echo slugify($searchresults["title"]); ?>">
                                        <div class="see play3"></div>
                                    </a>
                                </div>
                                <div class="data">
                                    <h3><a href="/movies/<?php echo $searchresults["id"]; ?>/<?php echo slugify($searchresults["title"]); ?>"><?php echo $searchresults["title"]; ?></a></h3> <span><?php if ($searchresults["release_date"]) echo $searchresults["release_date"]; else echo "N/A"; ?></span>
                                </div>
                            </article><?php endforeach ?>
                    </div>
                    <div class="animation-2 items full arch">
                        <h2 class="Featured">TV Shows</h2><?php foreach ($searchresultstv["results"] as $searchresultstv) : ?>
                            <article class="item">
                                <div class="poster"><img src="<?php if ($searchresultstv["poster_path"]) echo "https://image.tmdb.org/t/p/w185".$searchresultstv["poster_path"]; else echo "/img/noposter.png"; ?>" alt="<?php echo $searchresultstv["name"]; ?>">
                                    <div class="rating"><i class="fa fa-star"></i> <?php echo Ratingtwo($searchresultstv["vote_average"]); ?></div>
                                    <div class="mepo"> </div>
                                    <a href="/tv/<?php echo $searchresultstv["id"]; ?>/<?php echo slugify($searchresultstv["name"]); ?>">
                                        <div class="see play3"></div>
                                    </a>
                                </div>
                                <div class="data">
                                    <h3><a href="/tv/<?php echo $searchresultstv["id"]; ?>/<?php echo slugify($searchresultstv["name"]); ?>"><?php echo $searchresultstv["name"]; ?></a></h3> <span><?php if ($searchresultstv["first_air_date"]) echo $searchresultstv["first_air_date"]; else echo "N/A"; ?></span>
                                </div>
                            </article><?php endforeach ?>
                    </div>
                    <div class="pagination">
                        <?php
                        $wrap = "<ul class='pagination'>";
                        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $nextpage = $current_page + 1;
                        $prevpage = $current_page - 1;
                        $kywrd = urlencode($_GET['keyword']);

                        if ($current_page >= 2) {
                            $wrap .= "<li class='previous'><a href='$kywrd&page=$prevpage' data-page='$prevpage'> < </a></li>";
                        }

                        for ($i = $current_page - 1; $i <= $current_page + 4; $i++) {
                            if ($i == 0) {
                                continue;
                            }
                            $active = "";
                            if ($i == $current_page) {
                                $active = "active";
                            }

                            $wrap .= "<li class='$active'><a href='$kywrd&page=" . $i . "'>" . $i . "</a><li>";
                        }
                        echo $wrap . "<li class='next'><a href='$kywrd&page=$nextpage' data-page='$nextpage'> > </a></li></ul>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php include_once 'includes/footer.php'; ?>