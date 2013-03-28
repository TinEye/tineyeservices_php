<?php

# Copyright (c) 2013 Idee Inc. All rights reserved worldwide.

require_once 'image.php';
require_once 'tineye_service_request.php';

/// A base class to handle metadata-related requests to a TinEye Services API.
class MetadataRequest extends TinEyeServiceRequest
{
    /// Add images to the collection using data.
    
    /// Arguments:
    /// - `images`, a list of Image objects.
    /// - `ignore_background`, if true, ignore the background region of the images.
    ///    If false, include the background region of the images.
    /// - `ignore_interior_background`, if true, ignore the background region's color 
    ///    in isolated parts of the images. If false, include the background color 
    ///    in those parts of the images.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    ///
    function add_image($images, $ignore_background=true, $ignore_interior_background=true)
    {
        assert_is_array($images, "Image objects");

        $params = array();
        $file_params = array();
        $file_params['ignore_background'] = $ignore_background;
        $file_params['ignore_interior_background'] = $ignore_interior_background;
        $counter = 0;

        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass a list of Image objects');

            $file_params["images[$counter]"] = "@{$image->local_filepath}";
            $file_params["filepaths[$counter]"] = $image->collection_filepath;
            if (!is_null($image->metadata))
                $file_params["metadata[$counter]"] = $image->metadata;

            $counter += 1;
        }

        return $this->request('add', $params, $file_params);
    }

    /// Add images to the collection via URLs.
    
    /// Arguments:
    /// - `images`, a list of Image objects.
    /// - `ignore_background`, if true, ignore the background region of the images.
    ///    If false, include the background color of the images.
    /// - `ignore_interior_background`, if true, ignore the background region's color 
    ///    in isolated parts of the images. If false, include the background color 
    ///    in those parts of the images.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    ///
    function add_url($images, $ignore_background=true, $ignore_interior_background=true)
    {
        assert_is_array($images, "Image objects");

        $params = array();
        $file_params = array();
        $file_params['ignore_background'] = $ignore_background;
        $file_params['ignore_interior_background'] = $ignore_interior_background;
        $counter = 0;

        foreach ($images as $image)
        {
            if (!gettype($image) == 'object' || !get_class($image) == 'Image')
                throw new TinEyeServiceError('Need to pass a list of Image objects');

            $file_params["urls[$counter]"] = $image->url;
            $file_params["filepaths[$counter]"] = $image->collection_filepath;
            if (!is_null($image->metadata))
                $file_params["metadata[$counter]"] = $image->metadata;

            $counter += 1;
        }
        
        return $this->request('add', $params, $file_params);
    }

    /// Force a metadata update for images already present in the collection.
    
    /// Arguments:
    /// - `filepaths`, a list of filepath strings of images already in the collection,
    ///   as returned by a search or list operation.
    /// - `metadata`, a list of metadata updates for the images.
    ///
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    ///
    function update_metadata($filepaths, $metadata)
    {
        assert_is_array($filepaths, "filepaths");
        assert_is_array($metadata,  "metadata");
        
        $params = array();        
        fill_array_params($params, $filepaths, "filepaths");
        fill_array_params($params, $metadata,  "metadata");

        return $this->request('update_metadata', $params);
    }

    /// Get associated keywords from the index given a list of image filepaths.
    
    /// Arguments:
    /// - `filepaths`, a list of filepath strings of images already in the collection,
    ///   as returned by a search or list operation.
    ///
    /// Returned:
    /// - `status`, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the metadata tree structure that is associated with the given filepath.
    ///
    function get_metadata($filepaths)
    {
        assert_is_array($filepaths, "filepaths");

        $params = array();
        fill_array_params($params, $filepaths, "filepaths");

        return $this->request('get_metadata', $params);
    }

    /// Get the metadata tree structure that can be searched.
    
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, the tree structure that can be searched along with keyword type
    ///   and the number of images from the index containing that keyword.
    ///
    function get_search_metadata()
    {
        return $this->request('get_search_metadata');
    }

    /// Get the metadata that can be returned by a search method along with each match.
    
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of keywords with data type and the number of images 
    ///   from the index containing that keyword.
    ///
    function get_return_metadata()
    {
        return $this->request('get_return_metadata');
    }
}
?>
