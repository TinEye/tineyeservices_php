<?php

# Copyright (c) 2013 Idee Inc. All rights reserved worldwide.

require_once 'tineye_service_request.php';

/// A class representing an image.

/// <pre>
/// Image on filesystem:
///     >>> require_once 'image.php';
///     >>> $image = new Image('/path/to/image.jpg', '', 'collection.jpg');
///
/// Image URL:
///     >>> $image = new Image('', 'http:///www.tineye.com/images/meloncat.jpg', 'collection.jpg');
///
/// Image with metadata:
///     >>> $metadata = json_encode(array("keywords" => array("dolphin")));
///     >>> $image = new Image('/path/to/image.jpg', '', '', $metadata);
/// </pre>
///
class Image
{
    /// Construct an object describing an image.

    /// Arguments:
    /// - `local_filepath`, the path to the image, if it is to be taken from a file.
    /// - `url`, the URL of the image, if it is to be taken from the web.
    /// - `collection_filepath`, the path to be given to the image when added to the collection.
    /// - `metadata`, the metadata to be stored with the image when added to the collection.
    ///
    function __construct($local_filepath='', $url='', $collection_filepath='', $metadata=null)
    {
        $this->data = null;
        $this->local_filepath = $local_filepath;
        $this->url = $url;

        # If a filepath is specified, read the image and use its path as the collection filepath.
        if ($local_filepath != '')
        {
            $fp = fopen($local_filepath, 'rb');
            if (!$fp)
                throw new TinEyeServiceError('Could not open image file.');
            $this->data = stream_get_contents($fp);
            if ($this->data === false)
                throw new TinEyeServiceError('Could not read image file.');
            fclose($fp);
            $this->collection_filepath = $local_filepath;
        }

        # If no local filepath but a URL is specified, use the basename of the URL
        # as the collection filepath.
        # NB: this requires a URL which ends with something resembling a filepath.
        if (is_null($this->data) && $this->url != '')
            $this->collection_filepath = basename($this->url);

        # If user specified their own collection filepath, then use that instead.
        if ($collection_filepath != '')
            $this->collection_filepath = $collection_filepath;

        # Need to make sure there is at least a file or a URL.
        if (is_null($this->data) && $this->url == '')
            throw new TinEyeServiceError('Image object needs either a file or a URL.');

        $this->metadata = $metadata;
    }

    ///
    /// Return a human-readable description of the object.
    ///
    function __toString()
    {
        return "Image(local_filepath=$this->local_filepath, url=$this->url, collection_filepath=$this->collection_filepath, metadata=$this->metadata)";
    }
}
?>
