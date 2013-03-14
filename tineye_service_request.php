<?php

# Copyright (c) 2013 Idee Inc. All rights reserved worldwide.

require_once '/home/mark/misc-code/Requests/library/Requests.php';
Requests::register_autoloader();

// Base class for all TinEye Services exceptions.
class TinEyeServiceException extends Exception
{}
 
class TinEyeServiceError extends TinEyeServiceException
{}

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

class TinEyeServiceRequest
{
	// A class to send requests to a TinEye servies API.

	function __construct($api_url='http://localhost/rest/', $username=NULL, $password=NULL)
	{
		$this->api_url		= $api_url;
		$this->username 	= $username;
		$this->password 	= $password;
	}

	//
	// make an http request, return the response as an object.
	//
	protected function request($method, $params=array(), $file_params=NULL)
	{
		# set up basic authentication.
		$options = array();
		if (!is_null($this->username))
			$options['auth'] = new Requests_Auth_Basic(array($this->username, $this->password));

		echo "$method<br>";
		echo var_dump($params);
		echo "<br>";
		echo var_dump($file_params);
		echo "<br>";

		# construct the query URL.
		$url = $this->api_url . $method . '/';

		# if there are file parameters, send them in a POST body.
		# the empty array is extra headers.
		if (is_null($file_params))
			$response = Requests::get($url, array(), $params, $options);
		else
			$response = Requests::post($url, array(), $file_params, $options);

		// echo var_dump($response);
		echo "<hr>";

        # Handle any HTTP errors.
        if ($response->status_code != 200)
            throw new TinEyeServiceError("HTTP failure, status code $response->status_code");

		# get the response as an object.
		$response_json = json_decode($response->body);

        echo($response->body);
        echo "<br>";

        # Handle API errors.
        if ($response_json->status == 'fail')
            throw new TinEyeServiceError("{$response_json->error[0]}");
        elseif ($response_json->status == 'warn')
            throw new TinEyeServiceWarning("{$response_json->error[0]}");

        return $response_json;
	}

	//
    // Delete images from the collection.
    //
    // Arguments:
    // - `filepaths`, a list of string filepaths as returned by
    //   a search or list call.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    //
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

	//
	// Get the number of items currently in the collection.
	//
	// Returned:
	// - `status`, one of ok, warn, fail.
	// - `error`, describes the error if status is not set to ok.
	// - `result`, a list containing the number of images in the collection.
	//
    function count()
    {
        return $this->request('count');
    }   

    //
    // List the images present in the collection.
    //
    // Arguments:
    // - `offset`, offset of results from the start.
    // - `limit`, maximum number of images that should be returned.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    // - `result`, a list of filepaths.
    //
    function listing($offset=0, $limit=20)
    {
        return $this->request('list', array('offset' => $offset, 'limit' => $limit));
    }

    //
    // Check whether the API search server is running.
    //
    // Returned:
    // - `status`, one of ok, warn, fail.
    // - `error`, describes the error if status is not set to ok.
    //
    function ping()
    {
    	return $this->request('ping');
	}
}
?>
