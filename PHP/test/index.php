<?php
// Kupina unit test
require_once "../src/kupina.php" ;

$k = new Kupina( 256 ) ;

$ret = true ;
$res = $k->digest( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F", "HEX" )
        == "08F4EE6F1BE6903B324C4E27990CB24EF69DD58DBE84813EE0A52F6631239875" ;
$ret &= $res ;
echo "N = 512: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

$res = $k->digest( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E5F606162636465666768696A6B6C6D6E6F707172737475767778797A7B7C7D7E7F", "HEX" )
        == "0A9474E645A7D25E255E9E89FFF42EC7EB31349007059284F0B182E452BDA882" ;
$ret &= $res ;
echo "N = 1024: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

$res = $k->digest( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E5F606162636465666768696A6B6C6D6E6F707172737475767778797A7B7C7D7E7F808182838485868788898A8B8C8D8E8F909192939495969798999A9B9C9D9E9FA0A1A2A3A4A5A6A7A8A9AAABACADAEAFB0B1B2B3B4B5B6B7B8B9BABBBCBDBEBFC0C1C2C3C4C5C6C7C8C9CACBCCCDCECFD0D1D2D3D4D5D6D7D8D9DADBDCDDDEDFE0E1E2E3E4E5E6E7E8E9EAEBECEDEEEFF0F1F2F3F4F5F6F7F8F9FAFBFCFDFEFF", "HEX" )
        == "D305A32B963D149DC765F68594505D4077024F836C1BF03806E1624CE176C08F" ;
$ret &= $res ;
echo "N = 2048: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

$res = $k->digest( "FF", "HEX" )
        == "EA7677CA4526555680441C117982EA14059EA6D0D7124D6ECDB3DEEC49E890F4" ;
$ret &= $res ;
echo "N = 8: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

$res = $k->digest( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E", "HEX" )
        == "1075C8B0CB910F116BDA5FA1F19C29CF8ECC75CAFF7208BA2994B68FC56E8D16" ;
$ret &= $res ;
echo "N = 760: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

$res = $k->digest( "", "HEX" )
        == "CD5101D1CCDF0D1D1F4ADA56E888CD724CA1A0838A3521E7131D4FB78D0F5EB6" ;
$ret &= $res ;
echo "N = 0: " , ( $res ? "passed" : "failed" ) , '<br/>' ;

if( $ret ) {
    echo "All tests passed" ;
}
else {
    echo "Some test(s) failed" ;
}