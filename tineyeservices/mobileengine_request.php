<?php

require_once 'matchengine_request.php';

/// <b>A user class to send requests to a MobileEngine API.</b>

/// <pre>
/// Adding an image using data:
///     >>> require_once 'image.php';
///     >>> require_once 'mobileengine_request.php';
///     >>> $api = new MobileEngineRequest('https://mobileengine.tineye.com/name/rest/');
///     >>> $image = new Image('/path/to/image.jpg');
///     >>> $api->add_image(array(image));
///     {u'error': [], u'method': u'add', u'result': [], u'status': u'ok'}
///
/// Searching for an image using an image URL:
///     >>> $api->search_url('http://www.tineye.com/images/meloncat.jpg');
///     {'error': [],
///      'method': 'search',
///      'result': [{'filepath': 'match1.png',
///                  'score': '97.2',
///                  'overlay': 'overlay/query.png/match1.png[...]'}],
///      'status': 'ok'}
/// </pre>
///
/// \copyright 2013 Idee Inc. All rights reserved worldwide.
class MobileEngineRequest extends MatchEngineRequest
{
    /// Return a human-readable description of the object.
    function __toString()
    {
        return "MobileEngineRequest(api_url=$this->api_url, username=$this->username, password=$this->password)";
    }
}

?>
