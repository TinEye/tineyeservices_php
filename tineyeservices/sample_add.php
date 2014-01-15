<?php
require_once 'image.php';
require_once 'mobileengine_request.php';

//
// This is sample code for adding images to a TinEye services API.  
// It will add up to 1000 images at a time, which minimizes overhead.
// You can adjust it to the particular API which you are using in the setting of $api.
// In the case of MulticolorEngine, you can add metadata to the Image ojects.
//

// This is used to access an API which is authenticated by checking the incoming IP number. 
// Just insert the company name used in your TinEye service URL.
$api = new MobileEngineRequest ('http://mobileengine.tineye.com/<your company>/rest/');

// If you are using the less-secure password authentication, use this form.
#$api = new MobileEngineRequest ('http://mobileengine.tineye.com/<your company>/rest/', 'username', 'password');

// Save the script's directory.
define ("ROOT", dirname(__FILE__));

// How may images to add at a time.  Doing as many as possible, up to 1,000, is recommended.
define ("LIMIT", 1000);

// Create a log file.  Any old log will be overwritten.  Only errors will be logged.
$log = fopen (ROOT . "/failures.txt", 'w') or die("Cannot open file: failures.txt");

$count = 0;
$total = 0;
$images = array();

// We will process all the JPEGs in the script's directory.
$files = glob (ROOT . "/*.jpg");
$limit = count ($files);

foreach ($files as $filename)
{
    // Append an Image object to the list we are accumuating.
    $images[] = new Image($filename);
    $count += 1;
    $total += 1;
    
    if ($count >= LIMIT || $total >= $limit)
    {
        // If we have a thousand images ready, or there are no more, add them to the API.
        try
        {
            $res = $api->add_image ($images);
        }
        catch (Exception $e)
        {
            // This should not happen.  Errors are reported but not thrown.
            fwrite ($log, "caught $e\n");
        }
        
        // Log any error messages.  They should only concern failures to
        // add particular images, usually due to file corruption.
        foreach ($res->error as $error)
        {
            fwrite ($log, "$error\n");
        }
        
        // Start on the next thousand.
        $count = 0;
        $images = array();
    }
}

fclose ($log);
?>
