<?php

require_once 'image.php';
require_once 'metadata_request.php';

/// <b>A user class to send requests to a MulticolorEngine API.</b>

/// <pre>
/// Adding an image using data:
///     >>> require_once 'image.php';
///     >>> require_once 'multicolorengine_request.php';
///     >>> $api = new MulticolorEngineRequest('http://someengine.tineye.com/name/rest/');
///     >>> $image = new Image('/path/to/image.jpg');
///     >>> $api->add_image(array(image));
///     {u'error': [], u'method': u'add', u'result': ][, u'status': u'ok'}
///
/// Searching for images using colors:
///     >>> $api->search_color(colors=array('255,255,235', '12FA3B');
///     {'error': array(),
///      'method': 'search',
///      'result': [{'filepath': 'path/to/image.jpg',
///                  'score': '13.00'}],
///      'status': 'ok'}
/// </pre>
///
/// \copyright 2013 Idee Inc. All rights reserved worldwide.
class MulticolorEngineRequest extends MetadataRequest
{
    /// Return a human-readable description of the object.
    function __toString()
    {
        return "MulticolorEngineRequest(api_url=$this->api_url, username=$this->username, password=$this->password)";
    }

    /// Do a color search against the collection using image data.

    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `image`, an Image object.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `return_metadata`, metadata to be returned with each match.
    /// - `sort_metadata`, whether the search results are sorted by metadata score.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `filepath`, match image path.
    ///
    function search_image(
        $image, 
        $ignore_background=True, 
        $ignore_interior_background=True,
        $metadata='', 
        $return_metadata='', 
        $sort_metadata=False, 
        $min_score=0,
        $offset=0, 
        $limit=5000)
    {
        if (!get_class($image) == 'Image')
            throw new TinEyeServiceError('Need to pass an Image object');

        $params = array(
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'metadata' => $metadata,
            'return_metadata' => $return_metadata,
            'sort_metadata' => $sort_metadata,
            'min_score' => $min_score,
            'offset' => $offset,
            'limit' => $limit);

        if(function_exists('curl_file_create')) {
            $file_params["image"] = curl_file_create($image->local_filepath);
        } else {
            $file_params["image"] = "@{$image->local_filepath}";
        }

        return $this->request('color_search', $params, $file_params);
    }

    /// Do a color search against the collection using an image already in the collection.
    
    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `filepath`, a filepath string of an image already in the collection.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `return_metadata`, metadata to be returned with each match.
    /// - `sort_metadata`, whether the search results are sorted by metadata score.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `filepath`, match image path.
    ///
    function search_filepath(
        $filepath, 
        $ignore_background=True, 
        $ignore_interior_background=True,
        $metadata='', 
        $return_metadata='', 
        $sort_metadata=False, 
        $min_score=0,
        $offset=0, 
        $limit=5000)
    {
        $params = array(
            'filepath' => $filepath,
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'metadata' => $metadata,
            'return_metadata' => $return_metadata,
            'sort_metadata' => $sort_metadata,
            'min_score' => $min_score,
            'offset' => $offset,
            'limit' => $limit);
                  
        return $this->request('color_search', $params);
    }

    /// Do a color search against the collection using an image URL. 

    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `url`, a URL string pointing to an image.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `return_metadata`, metadata to be returned with each match.
    /// - `sort_metadata`, whether the search results are sorted by metadata score.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `filepath`, match image path.
    ///
    function search_url(
        $url, 
        $ignore_background=True, 
        $ignore_interior_background=True,
        $metadata='', 
        $return_metadata='', 
        $sort_metadata=False, 
        $min_score=0, 
        $offset=0, 
        $limit=5000)
    {
        $params = array(
            'url' => $url,
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'metadata' => $metadata,
            'return_metadata' => $return_metadata,
            'sort_metadata' => $sort_metadata,
            'min_score' => $min_score,
            'offset' => $offset,
            'limit' => $limit);

        return $this->request('color_search', $params);
    }
        
    /// Do a color search against the collection using specified colors.
    
    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `colors`, a list of string of colors in RGB ('255,112,223') or hex ('DF4F23') format.
    /// - `weights`, a list of weights.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `return_metadata`, metadata to be returned with each match.
    /// - `sort_metadata`, whether the search results are sorted by metadata score.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `filepath`, match image path.
    ///
    function search_color(
        $colors, 
        $weights=array(), 
        $ignore_background=True,
        $ignore_interior_background=True, 
        $metadata='',
        $return_metadata='', 
        $sort_metadata=False, 
        $min_score=0, 
        $offset=0, 
        $limit=5000)
    {
        $params = array(
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'metadata' => $metadata,
            'return_metadata' => $return_metadata,
            'sort_metadata' => $sort_metadata,
            'min_score' => $min_score,
            'offset' => $offset,
            'limit' => $limit);

        assert_is_array($colors, "colors");
        assert_is_array($weights, "weights");

        fill_array_params($params, $colors, "colors");
        fill_array_params($params, $weights, "weights");

        return $this->request('color_search', $params);
    }

    /// Do a search against the collection using metadata.

    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `return_metadata`, metadata to be returned with each match.
    /// - `sort_metadata`, whether the search results are sorted by metadata score.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `filepath`, match image path.
    ///
    function search_metadata(
        $metadata, 
        $return_metadata='', 
        $sort_metadata=False,
        $min_score=0, 
        $offset=0, 
        $limit=5000)
    {
        $params = array(
            'metadata' => $metadata,
            'return_metadata' => $return_metadata,
            'sort_metadata' => $sort_metadata,
            'min_score' => $min_score,
            'offset' => $offset,
            'limit' => $limit);

        return $this->request('color_search', $params);
    }

    /// Extract the dominant colors, given image upload data.
    
    /// Arguments:
    /// - `images`, a list of Image objects with local file paths.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background
    ///    regions.
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_image_colors_image(
        $images, 
        $ignore_background=True, 
        $ignore_interior_background=True, 
        $limit=32,
        $color_format='rgb')
    {
        assert_is_array($images, "Image objects");

        $params = array(
            'limit' => $limit,
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'color_format' => $color_format);
        $file_params = array();
        $counter = 0;

        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass an array of Image objects');

            if(function_exists('curl_file_create')) {
                $file_params["images[$counter]"] = curl_file_create($image->local_filepath);
            } else {
                $file_params["images[$counter]"] = "@{$image->local_filepath}";
            }
            $counter += 1;
        }

        return $this->request('extract_image_colors', $params, $file_params);
    }
        
    /// Extract the dominant colors, given image URLs.
    
    /// Arguments:
    /// - `urls`, a list of URL strings pointing to images.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_image_colors_url(
        $urls, 
        $ignore_background=True, 
        $ignore_interior_background=True, 
        $limit=32, 
        $color_format='rgb')
    {
        assert_is_array($urls, "URL strings");

        $params = array(
            'limit' => $limit,
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background,
            'color_format' => $color_format);
        
        fill_array_params($params, $urls, "urls");

        return $this->request('extract_image_colors', $params);
    }

    /// Generate a counter for each color from the palette.

    /// The palette specifies how many of the input images contain
    /// that color, given image upload data.
    
    /// Arguments:
    /// - `images`, an array of Image objects built from local images, not URLs.
    /// - `count_colors`, a list of colors (palette) which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_image_colors_image(
        $images, 
        $count_colors, 
        $ignore_background=True,
        $ignore_interior_background=True)
    {
        assert_is_array($images, "Image objects");
        assert_is_array($count_colors, "count_colors");

        $params = array(
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background);
        $file_params = array();

        fill_array_params($file_params, $count_colors, "count_colors");

        $counter = 0;
        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass an array of Image objects');

            if(function_exists('curl_file_create')) {
                $file_params["images[$counter]"] = curl_file_create($image->local_filepath);
            } else {
                $file_params["images[$counter]"] = "@{$image->local_filepath}";
            }
            $counter += 1;
        }

        return $this->request('count_image_colors', $params, $file_params);
    }

    /// Generate a counter for each color from the palette.

    /// The palette specifies how many of the input images contain
    /// that color, given image URLs.
    ///
    /// Arguments:
    /// - `urls`, an array of URL strings pointing to images.
    /// - `count_colors`, an array of colors (palette) which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `ignore_background`, if true, ignore the background color of the images,
    ///    if false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore regions that have the same
    ///    color as the background region but that are surrounded by non-background regions.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, an array of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_image_colors_url(
        $urls,
        $count_colors, 
        $ignore_background=True,
        $ignore_interior_background=True)
    {
        assert_is_array($urls, "URL strings");
        assert_is_array($count_colors, "count_colors");
        
        $params = array(
            'ignore_background' => $ignore_background,
            'ignore_interior_background' => $ignore_interior_background);
        fill_array_params($params, $urls, "urls");
        fill_array_params($params, $count_colors, "count_colors");

        return $this->request('count_image_colors', $params);
    }

    /// Extract the dominant colors of your collection.
    
    /// Arguments:
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_collection_colors($limit=32, $color_format='rgb')
    {
        $params = array(
            'limit' => $limit,
            'color_format' => $color_format);

        return $this->request('extract_collection_colors', $params);
    }

    /// Extract the dominant colors of a set of images.

    /// The set is chosen from the collection using metadata.
    ///
    /// Arguments:
    /// - `metadata`, the metadata to be used for filtering.
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_collection_colors_metadata(
        $metadata, 
        $limit=32, 
        $color_format='rgb')
    {
        $params = array(
            'metadata' => $metadata,
            'limit' => $limit,
            'color_format' => $color_format);

        return $this->request('extract_collection_colors', $params);
    }

    /// Extract the dominant colors of a set of images.

    /// The set is chosen from the collection using colors.
    ///
    /// Arguments:
    /// - `colors`, a list of colors to be used for image filtering. 
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `weights`, a list of weights to be used with the colors.
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_collection_colors_colors(
        $colors, 
        $weights=array(), 
        $limit=32, 
        $color_format='rgb')
    {
        assert_is_array($colors, "colors");
        assert_is_array($weights, "weights");

        $params = array(
            'limit' => $limit,
            'color_format' => $color_format);
        fill_array_params($params, $colors, "colors");
        fill_array_params($params, $weights, "weights");

        return $this->request('extract_collection_colors', $params);
    }

    /// Extract the dominant colors of a set of images.

    /// The set is chosen from the collection using filepaths aready in the collection.
    ///
    /// Arguments:
    /// - `filepaths`, a list of string filepaths of images already in the collection.
    /// - `limit`, maximum number of colors that should be returned.
    /// - `color_format`, return RGB or hex formatted colors, can be either 'rgb' or 'hex'.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color with
    ///    associated ranking and weight.
    ///
    function extract_collection_colors_filepath(
        $filepaths, 
        $limit=32, 
        $color_format='rgb')
    {
        assert_is_array($filepaths, "filepaths");

        $params = array(
            'limit' => $limit,
            'color_format' => $color_format);
        fill_array_params($params, $filepaths, "filepaths");

        return $this->request('extract_collection_colors', $params);
    }

    /// Generate a counter for each color from the specified color palette.

    /// The counter shows how many of the collection images contain that color(s).
    ///
    /// Arguments:
    /// - `count_colors`, a list of colors which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_collection_colors($count_colors)
    {
        assert_is_array($count_colors, "count_colors");

        $params = array();
        fill_array_params($params, $count_colors, "count_colors");

        return $this->request('count_collection_colors', $params);
    }

    /// Generate a counter for each color from the specified color palette.

    /// The counter shows how many of the collection images contain that color(s),
    /// given some metadata to filter the collection images.
    ///
    /// Arguments:
    /// - `metadata`, metadata to filter the collection.
    /// - `count_colors`, a list of colors which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_collection_colors_metadata($metadata, $count_colors)
    {
        assert_is_array($count_colors, "count_colors");

        $params = array('metadata' => $metadata);
        fill_array_params($params, $count_colors, "count_colors");

        return $this->request('count_collection_colors', $params);
    }

    /// Generate a counter for each color from the specified color palette.

    /// The counter shows how many of the collection images contain that color(s),
    /// given a list of colors and weights to filter the collection.
    ///
    /// Arguments:
    /// - `colors`, a list of colors to be used for image filtering. 
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `count_colors`, a list of colors which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `weights`, a list of weights to be used with the colors.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_collection_colors_colors($colors, $count_colors, $weights=array())
    {
        assert_is_array($colors, "colors");
        assert_is_array($weights, "weights");
        assert_is_array($count_colors, "count_colors");

        $params = array();
        fill_array_params($params, $colors, "colors");
        fill_array_params($params, $weights, "weights");
        fill_array_params($params, $count_colors, "count_colors");
 
        return $this->request('count_collection_colors', $params);
    }

    /// Generate a counter for each color from the specified color palette.

    /// The counter shows how many of the collection images contain that color(s),
    /// given a list of filepaths of images in the collection.
    ///
    /// Arguments:
    /// - `filepaths`, a list of string filepaths as returned by
    ///    a search or list call.
    /// - `count_colors`, a list of colors which you want to count.
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries, each representing a color.
    ///
    ///   + `color`, the color that was passed in.
    ///   + `num_images_partial_area`, the number of images that partially matched the color.
    ///   + `num_images_full_area`, the number of images that fully matched the color.
    ///
    function count_collection_colors_filepath($filepaths, $count_colors)
    {
        assert_is_array($filepaths, "filepaths");
        assert_is_array($count_colors, "count_colors");

        $params = array();
        fill_array_params($params, $filepaths, "filepaths");
        fill_array_params($params, $count_colors, "count_colors");

        return $this->request('count_collection_colors', $params);
    }

    /// Get a counter for metadata queries specifying how many of the collection images meet that query.
    
    /// Arguments:
    /// - `count_metadata`, a list of metadata queries which you want to count.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the counts associated with the given metadata.
    ///
    function count_metadata($count_metadata)
    {
        assert_is_array($count_metadata, "count_metadata");
        $params = array();
        fill_array_params($params, $count_metadata, "count_metadata");
        return $this->request('count_metadata', $params);
    }

    /// Get a counter for metadata queries specifying how many of the collection images meet that query filtered by metadata.

    /// Arguments:
    /// - `metadata`, metadata to be used for additional filtering.
    /// - `count_metadata`, a list of metadata queries which you want to count.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the counts associated with the given metadata.
    ///
    function count_metadata_metadata($metadata, $count_metadata)
    {
        assert_is_array($count_metadata, "count_metadata");
        $params = array('metadata' => $metadata);
        fill_array_params($params, $count_metadata, "count_metadata");
        return $this->request('count_metadata', $params);
    }

    /// Get a counter for metadata queries specifying how many of the collection images meet that query filtered by a list of colors and weights.

    /// Arguments:
    /// - `colors`, a list of colors to be used for image filtering. 
    ///    Can be RGB "255,255,255" or hex "ffffff" format.
    /// - `weights`, a list of weights to be used with the colors.
    /// - `count_metadata`, a list of metadata queries which you want to count.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the counts associated with the given metadata.
    ///
    function count_metadata_colors($colors, $count_metadata, $weights = array())
    {
        assert_is_array($colors, "colors");
        assert_is_array($weights, "weights");
        assert_is_array($count_metadata, "count_metadata");

        $params = array();
        fill_array_params($params, $colors, "colors");
        fill_array_params($params, $weights, "weights");
        fill_array_params($params, $count_metadata, "count_metadata");

        return $this->request('count_metadata', $params);
    }

    /// Get a counter for metadata queries specifying how many of the collection images meet that query filtered by a list of images from the collection.
    
    /// Arguments:
    /// - `filepaths`, a list of string filepaths as returned by
    ///    a search or list call.
    /// - `count_metadata`, a list of metadata queries which you want to count.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the counts associated with the given metadata.
    ///
    function count_metadata_filepath($filepaths, $count_metadata)
    {
        assert_is_array($filepaths, "filepaths");
        assert_is_array($count_metadata, "count_metadata");

        $params = array();
        fill_array_params($params, $filepaths, "filepaths");
        fill_array_params($params, $count_metadata, "count_metadata");

        return $this->request('count_metadata', $params);
    }
}
?>
