<?php

require_once 'Requests/library/Requests.php';
Requests::register_autoloader();

/// \mainpage
/// tineyeservices is a PHP client library for the MatchEngine, MobileEngine, and MulticolorEngine APIs. 
/// 
/// MatchEngine, MobileEngine, and MulticolorEngine are general image-matching engines 
/// that allow you to perform large-scale image comparisons for a variety of tasks. 
/// 
/// See http://services.tineye.com/ for more information.
///
/// \copyright 2013 Idee Inc. All rights reserved worldwide.

/// The base class for all TinEye Services exceptions.
class TinEyeServiceException extends Exception
{}
 
/// A TinEye Services error.
class TinEyeServiceError extends TinEyeServiceException
{}

/// A TinEye Services warning.
class TinEyeServiceWarning extends TinEyeServiceException
{}

function fill_array_params(&$params, $source, $name)
{
    $counter = 0;
    foreach ($source as $item)
    {
        $params["{$name}[$counter]"] = $item;
        $counter += 1;
    }
}

function assert_is_array($item, $name)
{
    if (!is_array($item))
        throw new TinEyeServiceError("Need to pass a list of $name");
}

/// A base class to send requests to any TinEye Servies API.

/// \copyright 2013 Idee Inc. All rights reserved worldwide.
class TinEyeServiceRequest
{
    /// Construct an object to access a particular API.

    /// Arguments:
    /// - `api_url`, the basic URL for access, i.e., everything up to 
    ///              the method name in an actual request.
    ///              E.g., http://someengine.tineye.com/name/rest/
    /// - `username`, the username for the API account.
    /// - `password`, the password for the API account.
    ///
    function __construct($api_url, $username=NULL, $password=NULL)
    {
        $this->api_url      = $api_url;
        $this->username     = $username;
        $this->password     = $password;
    }

    /// Make an http request, return the response as an object.
    
    /// Arguments:
    /// - `method`, the function to execute, eg 'SEARCH'.
    /// - `params`, any parameters required by a GET method. 
    ///             They will be passed as a query part in the URL.
    /// - `file_params`, any parameters required by a POST method. 
    ///                  They will be passed as form data.
    ///
    /// Returned:
    /// an array, containing at least 'status' and 'error', and also 'result' if it applies.
    /// the structure of 'result' depends on the method.
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`,  describes the error if status is not set to ok.
    /// - `result`, an array.
    ///
    protected function request($method, $params=array(), $file_params=NULL)
    {
        # set up basic authentication.
        $options = array();
        if (!is_null($this->username))
            $options['auth'] = array($this->username, $this->password);

        # construct the query URL.
        $url = $this->api_url . $method . '/';

        # if there are file parameters, send them in a POST body.
        # the empty array is extra headers.
        if (is_null($file_params)) {
            $query_string = http_build_query($params);
            $get_url = $url . '?' . $query_string;
            $response = Requests::get($get_url, array(), $options);
        }
        else {
            $response = Requests::post($url, array(), $file_params, $options);
        }

        # Handle any HTTP errors.
        if ($response->status_code != 200)
            throw new TinEyeServiceError("HTTP failure, status code $response->status_code");

        # get the response as an object.
        # despite the name it is PHP data, not a JSON string.
        $response_json = json_decode($response->body);

        # Handle API errors.
        # doing it this way hides multiple errors, say from adding many images at once.
        /*
        if ($response_json->status == 'fail')
            throw new TinEyeServiceError("{$response_json->error[0]}");
        elseif ($response_json->status == 'warn')
            throw new TinEyeServiceWarning("{$response_json->error[0]}");
        */
        
        return $response_json;
    }

    /// Delete images from the collection.
    
    /// Arguments:
    /// - `filepaths`, a list of string filepaths as returned by
    ///    a search or list call.
    /// 
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// 
    function delete($filepaths)
    {
        if (!is_array($filepaths))
            throw new TinEyeServiceError('Need to pass a list of filepaths');

        $params = array();
        $counter = 0;
            
        foreach ($filepaths as $filepath)
        {
            $params["filepaths[$counter]"] = $filepath;
            $counter += 1;
        }

        return $this->request('delete', $params);
    }

    /// Get the number of items currently in the collection.
     
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list containing the number of images in the collection.
    /// 
    function count()
    {
        return $this->request('count');
    }   

    /// List the images present in the collection.
     
    /// Arguments:
    /// - `offset`, offset of results from the start.
    /// - `limit`, maximum number of images that should be returned.
    /// 
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// - `result`, a list of filepaths.
    /// 
    function listing($offset=0, $limit=20)
    {
        return $this->request('list', array('offset' => $offset, 'limit' => $limit));
    }

    /// Check whether the API search server is running.
     
    /// Returned:
    ///    an array containing
    /// - `status`, a string, one of ok, warn, fail.
    /// - `error`, describes the error if status is not set to ok.
    /// 
    function ping()
    {
        return $this->request('ping');
    }
}
?>
