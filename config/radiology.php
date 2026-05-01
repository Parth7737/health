<?php

return [
    /*
    | Optional web viewer URL for imaging (OHIF, Weasis, vendor portal).
    | Use placeholders: {accession}, {order_no}, {patient_id}
    | Example: https://pacs.example.com/viewer?accession={accession}
    */
    'pacs_web_viewer_url_template' => env('RADIOLOGY_PACS_WEB_VIEWER_URL', ''),
];
