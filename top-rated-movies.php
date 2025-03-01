<?php
// Include files
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Latest Update SUB
$topratedmovies = $APIbaseURL . $topratedmovie . $api_key . $language;

// Handle pagination
if (isset($_GET['page'])) {
    $topratedmovies = $APIbaseURL . $topratedmovie . $api_key . $language . "&page=" . $_GET['page'];
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

// Fetch top-rated movies using cURL
$ambil = fetchDataWithCurl($topratedmovies);

// Handle errors
if ($ambil === false) {
    include '404.php';
    exit();
}

// Decode JSON response
$tprtdmvs = json_decode($ambil, true);

/*----meta---*/
$canonical = "top-rated-movies";
$metatitle = 'Top Rated Movies - ' . $SiteTitle;
$metadesc = 'Watch and download latest movies and TV Shows for free in HD streaming with multiple language subtitles.';
?>
<?php include_once 'includes/header.php'; ?>
        <div id="container">
            <div class="module">
                <div class="content right full">
                    <h1 class="Featured">Top Rated Movies</h1>
                    <div class="animation-2 items full arch"><?php foreach ($tprtdmvs["results"] as $data) : ?>
                            <article class="item">
                                <div class="poster"><img src="<?php if ($data["poster_path"]) echo "https://image.tmdb.org/t/p/w185".$data["poster_path"]; else echo "/img/noposter.png"; ?>" alt="<?php echo $data["title"]; ?>">
                                    <div class="rating"><i class="fa fa-star"></i> <?php echo Ratingtwo($data["vote_average"]); ?></div>
                                    <div class="mepo"> </div>
                                    <a href="/movies/<?php echo $data["id"]; ?>/<?php echo slugify($data["title"]); ?>">
                                        <div class="see play3"></div>
                                    </a>
                                </div>
                                <div class="data">
                                    <h3><a href="/movies/<?php echo $data["id"]; ?>/<?php echo slugify($data["title"]); ?>"><?php echo $data["title"]; ?></a></h3> <span><?php if ($data["release_date"]) echo $data["release_date"]; else echo "N/A"; ?></span>
                                </div>
                            </article><?php endforeach ?>
                    </div>
                    <div class="pagination">
                        <?php
                        $wrap = "<ul class='pagination'>";
                        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                        $nextpage = $current_page + 1;
                        $prevpage = $current_page - 1;

                        if ($current_page >= 2) {
                            $wrap .= "<li class='previous'><a href='?page=$prevpage' data-page='$prevpage'> < </a></li>";
                        }

                        for ($i = $current_page - 1; $i <= $current_page + 4; $i++) {
                            if ($i == 0) {
                                continue;
                            }
                            $active = "";
                            if ($i == $current_page) {
                                $active = "active";
                            }

                            $wrap .= "<li class='$active'><a href='?page=" . $i . "'>" . $i . "</a><li>";
                        }
                        echo $wrap . "<li class='next'><a href='?page=$nextpage' data-page='$nextpage'> > </a></li></ul>";
                        ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div><!--breadcrumbs-start-->
            <div class="breadcrumb">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li>Top Rated Movies</li>
                </ul>
            </div>
        </div>
<?php include_once 'includes/footer.php'; ?>