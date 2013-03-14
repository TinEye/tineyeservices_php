<?php

# Copyright (c) 2013 Idee Inc. All rights reserved worldwide.

require_once '/var/www/image.php';
require_once '/var/www/tineye_service_request.php';

//
// Class to send requests to a TinEye Services API.
//
class MetadataRequest extends TinEyeServiceRequest
{
    function __construct($api_url='http://localhost/rest/', $username=NULL, $password=NULL)
    {
        parent::__construct($api_url, $username, $password);
    }

    //
    // Add images to the collection using data.
    
    // Arguments:
    // - `images`, a list of Image objects.
    // - `ignore_background`, if true, ignore the background color of the images,
    //   if false, include the background color of the images.
          
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    //
    function add_image($images, $ignore_background=true)
    {
        if (!is_array($images))
            throw new TinEyeServiceError('Need to pass a list of Image objects');

        $params = array();
        $file_params = array('ignore_background' => $ignore_background);
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

    //
    // Add images to the collection via URLs.
    //
    // Arguments:
    // - `images`, a list of Image objects.
    // - `ignore_background`, if true, ignore the background color of the images,
    //   if false, include the background color of the images.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    //
    function add_url($images, $ignore_background=true)
    {
        if (!is_array($images))
            throw new TinEyeServiceError('Need to pass a list of Image objects');

        $params = array();
        $file_params = array('ignore_background' => $ignore_background);
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

    //
    // Force a metadata update for images already present in the collection.
    //
    // Arguments:
    // - `filepaths`, a list of filepath strings of an image already in the collection
    //   as returned by a search or list operation.
    // - `metadata`, the metadata to be stored with the image.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    //
    function update_metadata($filepaths, $metadata)
    {
        if (!is_array($filepaths))
            throw new TinEyeServiceError('Need to pass a list of filepaths');
        
        if (!is_array($metadata))
            throw new TinEyeServiceError('Need to pass a list of metadata');
        
        $params = array();
        $counter = 0;
        
        foreach ($filepaths as $filepath)
        {
            $params["filepaths[$counter]"] = $filepath;
            $counter += 1;
        }

        $counter = 0;
        foreach ($metadata as $metadatum)
        {
            $params["metadata[$counter]"] = $metadatum;
            $counter += 1;
        }

        return $this->request('update_metadata', $params);
    }

    //
    // Get associated keywords from the index given a list of image filepaths.
    //
    // Arguments:
    // - `filepaths`, a list of filepath strings of an image already in the collection
    //   as returned by a search or list operation.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    // - `result`, the metadata tree structure that is associated with the given filepath.
    //
    function get_metadata($filepaths)
    {
        $params = array();
        $counter = 0;
        
        if (!is_array($filepaths))
            throw new TinEyeServiceError('Need to pass a list of filepaths');
        
        foreach ($filepaths as $filepath)
        {
            $params["filepaths[$counter]"] = $filepath;
            $counter += 1;
        }

        return $this->request('get_metadata', $params);
    }

    //
    // Get the metadata tree structure that can be searched.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    // - `result`, the tree structure that can be searched along with keyword type
    //   and the number of images from the index containing that keyword.
    //
    function get_search_metadata()
    {
        return $this->request('get_search_metadata');
    }

    //
    // Get the metadata that can be returned by a search method along with each match.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    // - `result`, a list of keywords with data type and the number of images 
    //   from the index containing that keyword.
    //
    function get_return_metadata()
    {
        return $this->request('get_return_metadata');
    }
}
?>
