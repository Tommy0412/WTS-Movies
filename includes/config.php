<?php
//do not display warning messages
error_reporting(E_ERROR);

// API base URL
$APIbaseURL = 'https://api.themoviedb.org/3/';
$api_key = "?api_key=8d6d91941230817f7807d643736e8a49";

//Base_URL
$Domainname = $_SERVER['SERVER_NAME'];  //do not edit this line

// Site name
$SiteTitle = 'WTSmovies';
$FooterTitle = 'WTSmovies';

//Directlink ad
$directlink = '';

// Get content type 
// Edit below only if you know what you are  doing
$movie = "movie/";
$tv = "tv/";
$language = "&language=en-US";
$popular = "popular";
$latest = "now_playing";
$tvlatest = "on_the_air";
$searchmovie = "search/movie";
$searchtv = "search/tv";
$trendingmovieweek = "trending/movie/week";
$trendingtvweek = "trending/tv/week";
$trendingmovieday = "trending/movie/day";
$trendingtvday = "trending/tv/day";
$topratedmovie = "movie/top_rated";
$topratedtv = "tv/top_rated";
$videos = "/videos";
?>