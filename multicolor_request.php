<?php

# Copyright (c) 2012 Idee Inc. All rights reserved worldwide.

require_once '/var/www/image.php';
require_once '/var/www/metadata_request.php';

//
// Class to send requests to a MulticolorEngine API. 
//
// Adding an image using data:
//     >>> require_once 'image.php';
//     >>> require_once 'mobileengine_request.php';
//     >>> $api = new MulticolorEngineRequest('http://localhost/rest/')
//     >>> $image = new Image('/path/to/image.jpg')
//     >>> $api->add_image(array(image))
//     {u'error': [], u'method': u'add', u'result': [], u'status': u'ok'}
//
// Searching for an image using colors:
//     >>> $api->.search_color(colors=array('255,255,235', '12FA3B')
//     {'error': [],
//      'method': 'search',
//      'result': [{'filepath': 'path/to/file.jpg',
//                  'score': '13.00'}],
//      'status': 'ok'}
//
class MulticolorEngineRequest extends MetadataRequest
{
    function __toString():
        return "MulticolorEngineRequest(api_url=$this->api_url, username=$this->username, password=$this->password)";
}

//
// Do a color search against the collection using image data
// and return matches with corresponding scores.
//
// Arguments:
// - `image`, an Image object.
// - `ignore_background`, if true, ignore the background color of the images,
//   if false, include the background color of the images.
// - `ignore_interior_background`, if true, ignore regions that have the same
//   color as the background region but that are surrounded by non background
//   regions.
// - `metadata`, metadata to be used for additional filtering.
// - `return_metadata`, metadata to be returned with each match.
// - `sort_metadata`, whether the search results are sorted by metadata score.
// - `min_score`, minimum score that should be returned.
// - `offset`, offset of results from the start.
// - `limit`, maximum number of matches that should be returned.
//
// Returned:
// - `status`, one of ok, warn, fail.
// - `error`, describes the error if status is not set to ok.
// - `result`, a list of dictionaries representing an image match.
//
//   + `score`, relevance score.
//   + `filepath`, match image path.
//
    function search_image($image, $ignore_background=True, $ignore_interior_background=True,
                     	  $metadata='', $return_metadata='', $sort_metadata=False, $min_score=0,
                     	  $offset=0, $limit=5000)
    {
        $params = array('ignore_background'			 => $ignore_background,
                  		'ignore_interior_background' => $ignore_interior_background,
                  		'metadata'					 => $metadata,
                  		'return_metadata'			 => $return_metadata,
                  		'sort_metadata'				 => $sort_metadata,
                  		'min_score'					 => $min_score,
                  		'offset'					 => $offset,
                  		'limit'						 => $limit);

        if (!get_class($image) == 'Image')
            throw new TinEyeServiceError('Need to pass an Image object');

        $file_params["image"] = "@$image->local_filepath";
        $file_params["filepath"] = $image->collection_filepath;

        return $this->request('color_search', $params, $file_params)
    }
?>
