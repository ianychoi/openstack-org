<?php

class SummitAdmin extends ModelAdmin
{

    private static $url_segment = 'summits';

    private static $managed_models = array
    (
        'Summit',
        'PresentationTopic',
        'PresentationSpeaker',
    );

    private static $menu_title = 'Summits';
}