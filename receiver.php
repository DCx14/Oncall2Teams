<?php

// set UTC FR
setlocale(LC_TIME, 'fr_FR');
date_default_timezone_set('Europe/Paris');

// input request webhook
$entityBody = file_get_contents('php://input');
$headers = getallheaders();


// json encode/decode
$str_json = json_encode($entityBody,JSON_FORCE_OBJECT);
$my_array = json_decode($entityBody,true);

//verify error
if (json_last_error()) {
    $error = "JSON parsing error: " . json_last_error();
    print $error;
    // To-do Add some logging for errors
    return;
}




//get metric in webhook


$severity = $my_array['alert_payload']['alerts'][0]['labels']['severity'];
$alertname = $my_array['alert_payload']['alerts'][0]['labels']['alertname'];
$status = $my_array['alert_payload']['alerts'][0]['status'];
$Description = $my_array['alert_payload']['alerts'][0]['annotations']['description'];
$UrlAlert = $my_array['alert_group']['permalinks']['web'];
$Annotations = $my_array['alert_payload']['alerts'][0]['annotations'];
$WebhookDest = $headers['Webhook-Url'] ; // Get URL destination
$Namespaces = $my_array['alert_payload']['alerts'][0]['labels']['namespace'];
$Pod = $my_array['alert_payload']['alerts'][0]['labels']['pod'];


$event = $my_array['event']['type'];


if (!$Pod){
  $Pod ="N/A";  
}
if (!$Namespaces){
  $Namespaces = "N/A";
}
#if ($status){
#  $status = $status . " " ."🚨";
#}

if ($event == "resolve"){
    $status = "Résolu" . " " ."✅";
    $severity = "resolu";
}else{
    $status = $status . " " ."🚨";
}

$facts = [
    [
        "title" => "Status",
        "value" => $status
    ],
    [
        "title" => "Alert Name",
        "value" => $alertname
    ]
];

// Add annotations to facts array
foreach ($Annotations as $key => $value) {
    $facts[] = [
        "title" => $key,
        "value" => $value
    ];
}


// function for tranform json

function arrayToTextWithNewlines($array) {
    $textArray = [];
    foreach ($array as $key => $value) {
        $textArray[] = "$key => $value";
    }
    return implode("\n", $textArray);
}

// Utilisation de la fonction
$AnnotationsBrut = arrayToTextWithNewlines($Annotations);
$AnnotationsBr = nl2br($AnnotationsBrut);

// condition for change color and images

if ($severity === "critical")
{
    $images = 'https://cdn0.iconfinder.com/data/icons/basic-11/97/9-512.png';
    $color = '#EA0601';
    $style = "attention";

}elseif ($severity === "warning")
{
    $images = 'https://cdn4.iconfinder.com/data/icons/set-1/32/__23-256.png';
    $color = '#E06C10';
    $style = "warning";

}elseif  ($severity === "info")
{
    $images = 'https://cdn0.iconfinder.com/data/icons/small-n-flat/24/678110-sign-info-256.png';
    $color = '#1D6BE0';
    $style = "emphasis";
  
}elseif  ($severity === "resolu")
{
    $images = 'https://cdn1.iconfinder.com/data/icons/color-bold-style/21/34-512.png';
    $color = '#21FA01';
    $style = "emphasis";
  
}

else{

    $images = 'https://cdn1.iconfinder.com/data/icons/web-illustration-1/132/48-256.png';
    $color = '#000000';
}


$data = [
    "type" => "message",
    "attachments" => [
        [
            "contentType" => "application/vnd.microsoft.card.adaptive",
            "content" => [
                "$schema" => "http://adaptivecards.io/schemas/adaptive-card.json",
                "type" => "AdaptiveCard",
                
                "version" => "1.0",
                "msteams" => [
                    "width" => "Full"
                ],
                "body" => [
                    [
                        "type" => "Container",
                        "style" => $style,
                        "items" => [
                            [ 
                                "type" => "ColumnSet",
                                "columns" => [
                                    [
                                        "type" => "Column",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "size" => "large",
                                                "weight" => "bolder",
                                                "text" => "**Alert Grafana**",
                                                "style" => "heading",
                                                "horizontalAlignment" => "center",
                                                "wrap" => true
                                            ]
                                        ],
                                        "width" => "stretch"
                                    ],
                                    [
                                        "type" => "Column",
                                        "items" => [
                                            [
                                                "type" => "Image",
                                                "url" => $images,
                                                "altText" => "Pending",
                                                "height" => "30px"
                                            ]
                                        ],
                                        "width" => "auto"
                                    ]
                                ]
                            ]
                        ],
                        "bleed" => true
                    ],
                    [
                        "type" => "FactSet",
                        "facts" => $facts // Insert the dynamically generated facts here
                    ],
                    [
                        "type" => "TextBlock",
                        "wrap" => true
                    ],
                    [
                        "type" => "TextBlock",
                        "text" => " "
                    ],
                    [
                        "type" => "TextBlock",
                        "text" => "   "
                    ],
                    [
                        "type" => "TextBlock",
                        "text" => "   "
                    ],
                    [
                        "type" => "Table",
                        "columns" => [
                            [
                                "width" => 1
                            ],
                            [
                                "width" => 1
                            ],
                            [
                                "width" => 1
                            ],
                            [
                                "width" => 1
                            ]
                        ],
                        "rows" => [
                            [
                                "type" => "TableRow",
                                "cells" => [
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "Nom du Namespace",
                                                "wrap" => true
                                            ]
                                        ]
                                    ],

                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "Nom du Pod",
                                                "wrap" => true
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "type" => "TableRow",
                                "cells" => [
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => $Namespaces,
                                                "wrap" => true
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => $Pod,
                                                "wrap" => true
                                            ]
                                        ]
                                    ],
                                ]
                            ],
                            [
                                "type" => "TableRow",
                                "cells" => [
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "",
                                                "wrap" => true
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "",
                                                "wrap" => true
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "",
                                                "wrap" => true
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "TableCell",
                                        "items" => [
                                            [
                                                "type" => "TextBlock",
                                                "text" => "",
                                                "wrap" => true
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "showGridLines" => false
                    ],
                    [
                        "type" => "ActionSet",
                        "actions" => [
                            [
                                "type" => "Action.OpenUrl",
                                "title" => "Voir le détail sur grafana",
                                "URL" => $UrlAlert
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$json_data1 = json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

// Send WebHook

$ch = curl_init($WebhookDest);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data1);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec( $ch );
// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
echo $response;
curl_close( $ch );

?>
