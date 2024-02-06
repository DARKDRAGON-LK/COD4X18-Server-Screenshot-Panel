<?php
////////////////////////////////////////////////////////////////////////
//////////      Edit This Data to Your Details ////////////////////////
////////////////////////////////////////////////////////////////////////

//Set the title of the Panel
$maintitle = "[Your Clan Name] Sreenshot Panel - [Your Server Name]";
//Set the title of the Page
$title = "[Your Clan Name] Screenshot Panel";
//Dirctory where images are stored
$directory = 'ss/';

// Function to remove special characters from a string
function clear_server_name($string) {
    $cleaned_string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    return $cleaned_string;
}

// Function to format map name
function friendly_mapname($mapname) {
    $mp_mapname = str_replace('mp_', '', $mapname);
    $new_mapname = ucfirst($mp_mapname); 
    return $new_mapname;
}

// Function to extract overlay details from image
function getOverlayDetails($image) {
    $filecontent = file_get_contents($image);
    $metapos = strpos($filecontent, "CoD4X");
    $meta = substr($filecontent, $metapos);
    $data = explode("\0", $meta);
    $hostname = $data[1];
    $map = $data[2];
    $playername = $data[3];
    $guid = $data[4];
    $shotnum = $data[5];
    $time = $data[6];
    $author = "DARKDRAGON";
    $authorurl = "https://github.com/DARKDRAGON-LK/COD4X18-Server-Screenshot-Panel";

    // Set the default timezone
    date_default_timezone_set('Asia/Kolkata');

    $datetime = new DateTime($time, new DateTimeZone('UTC'));
    $datetime->setTimezone(new DateTimeZone('Asia/Kolkata')); // Indian Standard Time (IST)
    $sdate = $datetime->format('Y-m-d');
    $stime = $datetime->format('g:i:s A');

    // Format the overlay details
    $overlayDetails = "IGN: " . clear_server_name($playername) . "<br>";
    $overlayDetails .= "MAP: " . friendly_mapname($map) . "<br>";
    $overlayDetails .= "DATE: " . $sdate . "<br>";
    $overlayDetails .= "TIME: " . $stime . "<br>";
    $overlayDetails .= "PLAYER GUID: " . clear_server_name($guid). "<br>";
    $overlayDetails .= "Developed By: <a href=".$authorurl.">" . clear_server_name($author)."</a>";

    return $overlayDetails;
}

function getOverlayName($image) {
    $filecontent = file_get_contents($image);
    $metapos = strpos($filecontent, "CoD4X");
    $meta = substr($filecontent, $metapos);
    $data = explode("\0", $meta);
    $hostname = $data[1];
    $map = $data[2];
    $playername = $data[3];
    $guid = $data[4];
    $shotnum = $data[5];
    $time = $data[6];

    // Format the overlay details
    $overlayName = clear_server_name($playername);

    return $overlayName;
}


// Directory where screenshots are stored
$images = glob($directory . "*.jpg");

// Function to sort images by date
function sortByDate($a, $b) {
    $timeA = strtotime(getOverlayDetails($a));
    $timeB = strtotime(getOverlayDetails($b));
    return $timeB - $timeA;
}

$author = "DARKDRAGON";

// Sort images array
usort($images, 'sortByDate');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title." By ".$author; ?></title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include CSS for lightbox -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css">
    <style>
        /* Add custom CSS */
        body {
            background-color: #222; /* Dark background color */
            color: #fff; /* Light text color */
        }
        .panel-heading {
            background-color: #333; /* Dark panel header background color */
            border-color: #333; /* Dark panel border color */
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-gap: 10px;
        }
        .gallery-item {
            position: relative;
            margin: 10px;
        }
        .gallery-item img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px;
            font-size: 12px;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .gallery-item:hover .image-overlay {
            opacity: 1;
        }
        .popup-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 999;
        }
        .popup-image {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            max-width: 90%;
            max-height: 90%;
        }
        .popup {
            display: none;
        }
        .overlay-details {
            color: #fff;
            position: absolute;
            bottom: 5px;
            right: 5px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 5px;
            font-size: 18px;
            z-index: 1001;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><?php echo $maintitle; ?></h2>
        </div>
        <div class="panel-body">
            <div class="gallery">
                <?php foreach ($images as $image) { ?>
                    <div class="gallery-item">
                        <a href="<?php echo $image; ?>" class="gallery-link" data-caption="<?php echo getOverlayDetails($image); ?>">
                            <img src="<?php echo $image; ?>" class="img-thumbnail" alt=" <?php echo getOverlayName($image) ?>">
                            <div class="image-overlay">
                                <?php echo getOverlayName($image) ?>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- Popup for image -->
<div class="popup" id="popup-image">
    <div class="popup-image-overlay"></div>
    <img src="" class="popup-image" alt="Popup Image">
    <div class="overlay-details">
        <!-- Overlay details will be added dynamically -->
    </div>
</div>

<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Include fancybox JS library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js"></script>
<script>
    $(document).ready(function() {
        // Open popup when gallery item is clicked
        $('.gallery-link').click(function(e) {
            e.preventDefault();
            var imageUrl = $(this).attr('href');
            var caption = $(this).data('caption');
            $('.popup-image').attr('src', imageUrl);
            $('.overlay-details').html(caption);
            $('.popup').fadeIn();
        });

        // Close popup when close button or overlay is clicked
        $('.popup-image-overlay, .popup').click(function() {
            $('.popup').fadeOut();
        });
    });
</script>
</body>
</html>
