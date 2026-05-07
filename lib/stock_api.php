<?php

/**
 * Fetch a single anime (or closest match)
 * Demonstrates returning one entity
 */
function fetch_anime($anime)
{
    $data = ["query" => $anime];
    $endpoint = "https://anime-data-scraper-api.p.rapidapi.com/v1/anime/popular";
    $isRapidAPI = true;
    $rapidAPIHost = "anime-data-scraper-api.p.rapidapi.com";

    $result = get($endpoint, "ANIME_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        return [];
    }

    $transformedResult = [];

    // Assume API returns a list → take first match
    if (isset($result["data"]) && count($result["data"]) > 0) {
        $animeItem = $result["data"][0];

        foreach ($animeItem as $k => $v) {
            // clean keys (if needed)
            $k = str_replace(" ", "_", strtolower($k));

            // clean numeric values
            if (is_string($v)) {
                $v = str_replace("%", "", $v);
                if (is_numeric($v)) {
                    $v = strpos($v, ".") !== false ? floatval($v) : intval($v);
                }
            }

            $transformedResult[$k] = $v;
        }

        // Example: remove unnecessary fields
        unset($transformedResult["trailer"]);
    }

    return $transformedResult;
}

/**
 * Search anime (returns multiple results)
 * Demonstrates returning a list of entities
 */
function search_anime($search)
{
    $data = ["query" => $search];
    $endpoint = "https://anime-data-scraper-api.p.rapidapi.com/v1/anime/popular";
    $isRapidAPI = true;
    $rapidAPIHost = "anime-data-scraper-api.p.rapidapi.com";

    $result = get($endpoint, "ANIME_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    error_log("API Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
        $result = json_decode($result["response"], true);
    } else {
        return [];
    }

    $transformedResults = [];

    if (isset($result["data"])) {
        foreach ($result["data"] as $animeItem) {

            // map only needed fields
            $data = [
                "title" => se($animeItem, "title"),
                "episodes" => se($animeItem, "episodes"),
                "status" => se($animeItem, "status"),
                "rating" => se($animeItem, "score"),
                "year" => se($animeItem, "year"),
                "image" => se($animeItem, "image"),
                "is_api" => 1
            ];

            array_push($transformedResults, $data);
        }
    }

    return $transformedResults;
}