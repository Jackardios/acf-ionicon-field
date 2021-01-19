<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('acf_field_ionicon_cdn_error');
delete_option('acf_field_ionicon_icon_data');
