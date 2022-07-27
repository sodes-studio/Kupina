package com.sodes.kupina;

import org.junit.jupiter.api.Test;
import static org.junit.jupiter.api.Assertions.*;

/**
 *
 * @author _dns_
 */
public class KupinaTest {
    
    public KupinaTest() {
    }
    
    @Test
    public void testCtor() {
        
        IllegalArgumentException thrown = assertThrows(
                IllegalArgumentException.class,
                () -> new Kupina(-10),
                "Kupina(-10) should throw IllegalArgumentException" ) ;
        
        assertNotNull( 
                thrown.getMessage(),
                "No message in exception thrown by Kupina(-10)" ) ;
        
        thrown = assertThrows(
                IllegalArgumentException.class,
                () -> new Kupina(0),
                "Kupina(0) should throw IllegalArgumentException" ) ;
        
        assertNotNull( 
                thrown.getMessage(),
                "No message in exception thrown by Kupina(0)" ) ;
        
        thrown = assertThrows(
                IllegalArgumentException.class,
                () -> new Kupina(2048),
                "Kupina(2048) should throw IllegalArgumentException" ) ;
        
        assertNotNull( 
                thrown.getMessage(),
                "No message in exception thrown by Kupina(2048)" ) ;
    }
    
    @Test
    public void testDigestHexHex() {
        Kupina k256 = new Kupina( 256 ) ;
        String data = "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F" ;
        String hash = "08F4EE6F1BE6903B324C4E27990CB24EF69DD58DBE84813EE0A52F6631239875" ;
        k256.update( data ) ;
        assertEquals( k256.digestHex(), hash, "512_1" ) ;
        assertEquals( k256.digestHex( data ), hash, "512_2" ) ;
        
        data = "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E5F606162636465666768696A6B6C6D6E6F707172737475767778797A7B7C7D7E7F" ;
        hash = "0A9474E645A7D25E255E9E89FFF42EC7EB31349007059284F0B182E452BDA882" ;
        k256.update( data ) ;
        assertEquals( k256.digestHex(), hash, "1024_1" ) ;
        assertEquals( k256.digestHex( data ), hash, "1024_2" ) ;
        
        k256.update( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E5F606162636465666768696A6B6C6D6E6F707172737475767778797A7B7C7D7E7F808182838485868788898A8B8C8D8E8F909192939495969798999A9B9C9D9E9FA0A1A2A3A4A5A6A7A8A9AAABACADAEAFB0B1B2B3B4B5B6B7B8B9BABBBCBDBEBFC0C1C2C3C4C5C6C7C8C9CACBCCCDCECFD0D1D2D3D4D5D6D7D8D9DADBDCDDDEDFE0E1E2E3E4E5E6E7E8E9EAEBECEDEEEFF0F1F2F3F4F5F6F7F8F9FAFBFCFDFEFF" ) ;
        assertEquals( k256.digestHex(), "D305A32B963D149DC765F68594505D4077024F836C1BF03806E1624CE176C08F", "2048" ) ;
        k256.update( "FF" ) ;
        assertEquals( k256.digestHex(), "EA7677CA4526555680441C117982EA14059EA6D0D7124D6ECDB3DEEC49E890F4", "8" ) ;
        k256.update( "000102030405060708090A0B0C0D0E0F101112131415161718191A1B1C1D1E1F202122232425262728292A2B2C2D2E2F303132333435363738393A3B3C3D3E3F404142434445464748494A4B4C4D4E4F505152535455565758595A5B5C5D5E" ) ;
        assertEquals( k256.digestHex(), "1075C8B0CB910F116BDA5FA1F19C29CF8ECC75CAFF7208BA2994B68FC56E8D16", "760" ) ;
        k256.update( "" ) ;
        assertEquals( k256.digestHex(), "CD5101D1CCDF0D1D1F4ADA56E888CD724CA1A0838A3521E7131D4FB78D0F5EB6", "0" ) ;
    }
    
    @Test
    public void testDigestBinHex() {
        Kupina k256 = new Kupina( 256 ) ;
        byte[] data = { 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0A, 0x0B, 0x0C, 0x0D, 0x0E, 0x0F, 0x10, 0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18, 0x19, 0x1A, 0x1B, 0x1C, 0x1D, 0x1E, 0x1F, 0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x2A, 0x2B, 0x2C, 0x2D, 0x2E, 0x2F, 0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x3A, 0x3B, 0x3C, 0x3D, 0x3E, 0x3F } ;
        String hash = "08F4EE6F1BE6903B324C4E27990CB24EF69DD58DBE84813EE0A52F6631239875" ;
        k256.update( data ) ;
        assertEquals( k256.digestHex( data ), hash, "512" ) ;
    }
    
    @Test
    public void testDigestBinBin() {
        Kupina k256 = new Kupina( 256 ) ;
        byte[] data = { 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0A, 0x0B, 0x0C, 0x0D, 0x0E, 0x0F, 0x10, 0x11, 0x12, 0x13, 0x14, 0x15, 0x16, 0x17, 0x18, 0x19, 0x1A, 0x1B, 0x1C, 0x1D, 0x1E, 0x1F, 0x20, 0x21, 0x22, 0x23, 0x24, 0x25, 0x26, 0x27, 0x28, 0x29, 0x2A, 0x2B, 0x2C, 0x2D, 0x2E, 0x2F, 0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x3A, 0x3B, 0x3C, 0x3D, 0x3E, 0x3F } ;
        byte[] hash = { (byte)0x08, (byte)0xF4, (byte)0xEE, (byte)0x6F, (byte)0x1B, (byte)0xE6, (byte)0x90, (byte)0x3B, (byte)0x32, (byte)0x4C, (byte)0x4E, (byte)0x27, (byte)0x99, (byte)0x0C, (byte)0xB2, (byte)0x4E, (byte)0xF6, (byte)0x9D, (byte)0xD5, (byte)0x8D, (byte)0xBE, (byte)0x84, (byte)0x81, (byte)0x3E, (byte)0xE0, (byte)0xA5, (byte)0x2F, (byte)0x66, (byte)0x31, (byte)0x23, (byte)0x98, (byte)0x75 } ;
        k256.update( data ) ;
        assertArrayEquals( k256.digest( data ), hash, "512" ) ;
    }
}
