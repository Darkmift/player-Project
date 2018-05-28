<?php
$instructions = array(
    'instructions' => array(
        //Get all playlists
        'Get all playlists' => array(
            'Method' => 'GET',
            'url' => "/playlist",
            'Request' => 'not applicable/required',
            'Response' => array(
                "success" => true,
                "data" => array(
                    "id" => 'integer',
                    "name" => 'string',
                    "image" => 'string',
                ),
            ),
        ),
        //Get existing playlist
        'Get existing playlist' => array(
            'Method' => 'GET',
            'url' => "/playlist/{id}:integer",
            'Request' => 'not applicable/required',
            'Response' => array(
                "success" => true,
                "data" => array(
                    "id" => 'integer',
                    "name" => 'string',
                    "image" => 'string',
                ),
            ),
            'notice' => 'data will output as false if no id does not exist or not found!',
        ),
        //Get playlist songs
        'Get existing playlist' => array(
            'Method' => 'GET',
            'url' => "/playlist/{id}:integer/songs",
            'Request' => 'not applicable/required',
            'Response' => array(
                "success" => true,
                "data" => array(
                    "songs" => array(
                        "name" => 'string',
                        "url" => 'string',
                    ),
                ),
            ),
            'notice' => 'songs will return an epty array if none are fouund(if playlist is empty or not existing or not found',
        ),
        //Create new playlist
        'Create new playlist' => array(
            'Method' => 'POST',
            'url' => "/playlist",
            'Request' => array(
                "name" => 'string',
                "image" => 'string',
                "songs" => array(
                    array('...will output array of songs...'),
                    array(
                        "name" => 'string',
                        "url" => 'string',
                    ),
                ),
            ),
            'Response' => array(
                "success" => true,
                "data" => array(
                    "id" => 'integer',
                ),
            ),
            'notice' => 'will return id of the created playlist',
        ),
        //Update existing playlist info
        'Update existing playlist info' => array(
            'Method' => 'POST',
            'url' => "/playlist/{id}:integer",
            'Request' => array(
                "name" => 'string',
                "image" => 'string',
            ),
            'Response' => array(
                "success" => true,
            ),
            'notice' => 'Request requires at least one field: name and/or image',
        ),
        //Update playlist songs
        'Update playlist songs' => array(
            'Method' => 'POST',
            'url' => "/playlist/{id}:integer/songs",
            'Request' => array(
                "songs" => array(
                    array('...will output array of songs...'),
                    array(
                        "name" => 'string',
                        "url" => 'string',
                    ),
                ),
            ),
            'Response' => array(
                "success" => true,
            ),
            'notice' => 'Request requires at least one song with both name & url',
        ),
        //Delete existing playlist
        'Delete existing playlist' => array(
            'Method' => 'POST',
            'url' => "/playlist/{id}:integer",
            'Request' => 'not applicable/required',
            'Response' => array(
                "success" => true,
            ),
            'notice' => 'success=true indicates delete request was properly processed',
        ),
    ),
);

$GLOBALS['instructions'] = $instructions;
