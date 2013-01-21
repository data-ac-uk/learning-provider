<?php
header( "Status: 303 See Other" );
header( "Location: http://learning-provider.data.ac.uk".$_SERVER["REQUEST_URI"] );

