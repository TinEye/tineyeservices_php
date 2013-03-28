<?php

# Copyright (c) 2012 Idee Inc. All rights reserved worldwide.

require_once 'image.php';
require_once 'tineye_service_request.php';

/// <b>A user class to send requests to a MatchEngine API.</b>

/// <pre>
/// Adding an image using data:
///     >>> require_once 'image.php';
///     >>> require_once 'matchengine_request.php';
///     >>> $api = new MatchEngineRequest('http://someengine.tineye.com/name/rest/');
///     >>> $image = new Image('/path/to/image.jpg');
///     >>> $api->add_image(array(image));
///     {u'error': [], u'method': u'add', u'result': [], u'status': u'ok'}
///
/// Searching for an image using an image URL:
///     >>> $api->search_url('http:///www.tineye.com/images/meloncat.jpg');
///     {'error': [],
///      'method': 'search',
///      'result': [{'filepath': 'match1.png',
///                  'score': '97.2',
///                  'overlay': 'overlay/query.png/match1.png[...]'}],
///      'status': 'ok'}
/// </pre>
///
class MatchEngineRequest extends TinEyeServiceRequest
{
    /// Return a human-readable description of the object.
    function __toString()
    {
        return "MatchEngineRequest(api_url=$this->api_url, username=$this->username, password=$this->password)";
    }

    /// Add images to the collection using data.
    
    /// Arguments:
    /// - `images`, a list of Image objects.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    ///
    function add_image($images)
    {
        assert_is_array($images, "Image objects");

        $params = array();
        $file_params = array();
        $counter = 0;

        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass a list of Image objects');

            $file_params["images[$counter]"] = "@$image->local_filepath";
            $file_params["filepaths[$counter]"] = $image->collection_filepath;
            $counter += 1;
        }

        return $this->request('add', $params, $file_params);
    }

    /// Add images to the collection via URLs.
    
    /// Arguments:
    /// - `images`, a list of Image objects.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    ///
    function add_url($images)
    {
        assert_is_array($images, "Image objects");

        $params = array();
        $counter = 0;

        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass a list of Image objects');

            $params["urls[$counter]"] = $image->url;
            $params["filepaths[$counter]"] = $image->collection_filepath;
            $counter += 1;
        }

        return $this->request('add', $params);
    }

    /// Search against the collection using image data.

    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `image`, an Image object.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    /// - `check_horizontal_flip`, whether to incorporate a horizontal flip check.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `overlay`, URL pointing to overlay image.
    ///   + `filepath`, match image path.
    ///
    function search_image($image, $min_score=0, $offset=0, $limit=10, $check_horizontal_flip=false)
    {
        $params = array('min_score'             => $min_score,
                        'offset'                => $offset,
                        'limit'                 => $limit,
                        'check_horizontal_flip' => $check_horizontal_flip);

        $file_params["image"]    = "@{$image->local_filepath}";
        $file_params["filepath"] =    $image->collection_filepath;

        return $this->request('search', $params, $file_params);
    }

    /// Search against the collection using an image already in the collection.

    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `filepath`, a filepath string of an image already in the collection
    ///    as returned by a search or list operation.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    /// - `check_horizontal_flip`, whether to incorporate a horizontal flip check.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `overlay`, URL pointing to overlay image.
    ///   + `filepath`, match image path.
    ///
    function search_filepath($filepath, $min_score=0, $offset=0, $limit=10,
                             $check_horizontal_flip=false)
    {
        $params = array('filepath'              => $filepath,
                        'min_score'             => $min_score,
                        'offset'                => $offset,
                        'limit'                 => $limit,
                        'check_horizontal_flip' => $check_horizontal_flip);

        return $this->request('search', $params);
    }
        
    /// Search against the collection using an image URL. 
 
    /// Return any matches, with corresponding scores.
    ///
    /// Arguments:
    /// - `url`, a URL string pointing to an image.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    /// - `check_horizontal_flip`, whether to incorporate a horizontal flip check.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `overlay`, URL pointing to overlay image.
    ///   + `filepath`, match image path.
    ///
    function search_url($url, $min_score=0, $offset=0, $limit=10,
                        $check_horizontal_flip=false)
    {
        $params = array('url'                   => $url,
                        'min_score'             => $min_score,
                        'offset'                => $offset,
                        'limit'                 => $limit,
                        'check_horizontal_flip' => $check_horizontal_flip);

        return $this->request('search', $params);
    }
        
    /// Given two images, compare them and return the match score if there is a match.
    
    /// Arguments:
    /// - `image_1`, an Image object representing the first image.
    /// - `image_2`, an Image object representing the second image.
    /// - `min_score`, minimum score that should be returned.
    /// - `check_horizontal_flip`, whether to incorporate a horizontal flip check.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `match_percent`, percent of image matching.
    ///
    function compare_image($image_1, $image_2, $min_score=0, $check_horizontal_flip=false)
    {
        $params = array('min_score'             => $min_score,
                        'check_horizontal_flip' => $check_horizontal_flip);

        $file_params["image1"] = "@{$image_1->local_filepath}";
        $file_params["image2"] = "@{$image_2->local_filepath}";

        return $this->request('compare', $params, $file_params);
    }
        
    /// Given two images, compare them and return the match score if there is a match.
    
    /// Arguments:
    /// - `url_1`, a URL string pointing to the first image.
    /// - `url_2`, a URL string pointing to the second image.
    /// - `min_score`, minimum score that should be returned.
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of matches that should be returned.
    /// - `check_horizontal_flip`, whether to incorporate a horizontal flip check.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of dictionaries representing an image match.
    ///
    ///   + `score`, relevance score.
    ///   + `match_percent`, percent of image matching.
    ///
    function compare_url($url_1, $url_2, $min_score=0, $check_horizontal_flip=false)
    {
        $params = array('url1'                  => $url_1,
                        'url2'                  => $url_2,
                        'min_score'             => $min_score,
                        'check_horizontal_flip' => $check_horizontal_flip);

        return $this->request('compare', $params);
    }
}
?>
