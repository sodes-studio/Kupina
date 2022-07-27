<?php
// Kupina hash demo. Use request //host/?data=FF
require_once "../src/kupina.php" ;

$k = new Kupina( 256 ) ;
echo "data: ", $_GET[ 'data' ] ,
    "<br>", 
    "hash as HEX: ", $k->digest( $_GET[ 'data' ], "HEX" ),
    "<br/>",
    "hash as STR(UTF): ", $k->digest( $_GET[ 'data' ], "STR" ) ; 
