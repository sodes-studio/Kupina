<?php

class Kupina {
	private $hash_size ;        // hash (return) size in bits (n)
	private $block_size ;       // state matrix size in bits  (l)
	private $columns ;          // state matrix columns count (c)
	private $Sbox ;             // S-boxes array
    private $iterations ;       // iteration count (t)
	private $shift ;            // shift values for tau-transform
	private $v ;                // v array for psi-transform
	private $iv ;               // IV matrix
	private $state ;            // State matrix
    private $pw2val, $pw2ind ;  // 2^i in GF

    function __construct( $bit_length = 256 ) {
		if( $bit_length < 8 || $bit_length > 1024 ) {
            throw new Exception( "Invalid bit length. Use 8..1024 values" ) ; 
        }
        if( $bit_length <= 256 ) {
            $this->block_size = 512 ;
            $this->columns    = 8 ;
            $this->iterations = 10 ;
        }
        else {
            $this->block_size = 1024 ;
            $this->columns    = 16 ;
            $this->iterations = 14 ;
        }
		
		$this->hash_size  = $bit_length ;

        $this->Sbox = [
		    0 => [ 0xA8, 0x43, 0x5F, 0x06, 0x6B, 0x75, 0x6C, 0x59, 0x71, 0xDF, 0x87, 0x95, 0x17, 0xF0, 0xD8, 0x09, 0x6D, 0xF3, 0x1D, 0xCB, 0xC9, 0x4D, 0x2C, 0xAF, 0x79, 0xE0, 0x97, 0xFD, 0x6F, 0x4B, 0x45, 0x39, 0x3E, 0xDD, 0xA3, 0x4F, 0xB4, 0xB6, 0x9A, 0x0E, 0x1F, 0xBF, 0x15, 0xE1, 0x49, 0xD2, 0x93, 0xC6, 0x92, 0x72, 0x9E, 0x61, 0xD1, 0x63, 0xFA, 0xEE, 0xF4, 0x19, 0xD5, 0xAD, 0x58, 0xA4, 0xBB, 0xA1, 0xDC, 0xF2, 0x83, 0x37, 0x42, 0xE4, 0x7A, 0x32, 0x9C, 0xCC, 0xAB, 0x4A, 0x8F, 0x6E, 0x04, 0x27, 0x2E, 0xE7, 0xE2, 0x5A, 0x96, 0x16, 0x23, 0x2B, 0xC2, 0x65, 0x66, 0x0F, 0xBC, 0xA9, 0x47, 0x41, 0x34, 0x48, 0xFC, 0xB7, 0x6A, 0x88, 0xA5, 0x53, 0x86, 0xF9, 0x5B, 0xDB, 0x38, 0x7B, 0xC3, 0x1E, 0x22, 0x33, 0x24, 0x28, 0x36, 0xC7, 0xB2, 0x3B, 0x8E, 0x77, 0xBA, 0xF5, 0x14, 0x9F, 0x08, 0x55, 0x9B, 0x4C, 0xFE, 0x60, 0x5C, 0xDA, 0x18, 0x46, 0xCD, 0x7D, 0x21, 0xB0, 0x3F, 0x1B, 0x89, 0xFF, 0xEB, 0x84, 0x69, 0x3A, 0x9D, 0xD7, 0xD3, 0x70, 0x67, 0x40, 0xB5, 0xDE, 0x5D, 0x30, 0x91, 0xB1, 0x78, 0x11, 0x01, 0xE5, 0x00, 0x68, 0x98, 0xA0, 0xC5, 0x02, 0xA6, 0x74, 0x2D, 0x0B, 0xA2, 0x76, 0xB3, 0xBE, 0xCE, 0xBD, 0xAE, 0xE9, 0x8A, 0x31, 0x1C, 0xEC, 0xF1, 0x99, 0x94, 0xAA, 0xF6, 0x26, 0x2F, 0xEF, 0xE8, 0x8C, 0x35, 0x03, 0xD4, 0x7F, 0xFB, 0x05, 0xC1, 0x5E, 0x90, 0x20, 0x3D, 0x82, 0xF7, 0xEA, 0x0A, 0x0D, 0x7E, 0xF8, 0x50, 0x1A, 0xC4, 0x07, 0x57, 0xB8, 0x3C, 0x62, 0xE3, 0xC8, 0xAC, 0x52, 0x64, 0x10, 0xD0, 0xD9, 0x13, 0x0C, 0x12, 0x29, 0x51, 0xB9, 0xCF, 0xD6, 0x73, 0x8D, 0x81, 0x54, 0xC0, 0xED, 0x4E, 0x44, 0xA7, 0x2A, 0x85, 0x25, 0xE6, 0xCA, 0x7C, 0x8B, 0x56, 0x80 ] ,
		    1 => [ 0xCE, 0xBB, 0xEB, 0x92, 0xEA, 0xCB, 0x13, 0xC1, 0xE9, 0x3A, 0xD6, 0xB2, 0xD2, 0x90, 0x17, 0xF8, 0x42, 0x15, 0x56, 0xB4, 0x65, 0x1C, 0x88, 0x43, 0xC5, 0x5C, 0x36, 0xBA, 0xF5, 0x57, 0x67, 0x8D, 0x31, 0xF6, 0x64, 0x58, 0x9E, 0xF4, 0x22, 0xAA, 0x75, 0x0F, 0x02, 0xB1, 0xDF, 0x6D, 0x73, 0x4D, 0x7C, 0x26, 0x2E, 0xF7, 0x08, 0x5D, 0x44, 0x3E, 0x9F, 0x14, 0xC8, 0xAE, 0x54, 0x10, 0xD8, 0xBC, 0x1A, 0x6B, 0x69, 0xF3, 0xBD, 0x33, 0xAB, 0xFA, 0xD1, 0x9B, 0x68, 0x4E, 0x16, 0x95, 0x91, 0xEE, 0x4C, 0x63, 0x8E, 0x5B, 0xCC, 0x3C, 0x19, 0xA1, 0x81, 0x49, 0x7B, 0xD9, 0x6F, 0x37, 0x60, 0xCA, 0xE7, 0x2B, 0x48, 0xFD, 0x96, 0x45, 0xFC, 0x41, 0x12, 0x0D, 0x79, 0xE5, 0x89, 0x8C, 0xE3, 0x20, 0x30, 0xDC, 0xB7, 0x6C, 0x4A, 0xB5, 0x3F, 0x97, 0xD4, 0x62, 0x2D, 0x06, 0xA4, 0xA5, 0x83, 0x5F, 0x2A, 0xDA, 0xC9, 0x00, 0x7E, 0xA2, 0x55, 0xBF, 0x11, 0xD5, 0x9C, 0xCF, 0x0E, 0x0A, 0x3D, 0x51, 0x7D, 0x93, 0x1B, 0xFE, 0xC4, 0x47, 0x09, 0x86, 0x0B, 0x8F, 0x9D, 0x6A, 0x07, 0xB9, 0xB0, 0x98, 0x18, 0x32, 0x71, 0x4B, 0xEF, 0x3B, 0x70, 0xA0, 0xE4, 0x40, 0xFF, 0xC3, 0xA9, 0xE6, 0x78, 0xF9, 0x8B, 0x46, 0x80, 0x1E, 0x38, 0xE1, 0xB8, 0xA8, 0xE0, 0x0C, 0x23, 0x76, 0x1D, 0x25, 0x24, 0x05, 0xF1, 0x6E, 0x94, 0x28, 0x9A, 0x84, 0xE8, 0xA3, 0x4F, 0x77, 0xD3, 0x85, 0xE2, 0x52, 0xF2, 0x82, 0x50, 0x7A, 0x2F, 0x74, 0x53, 0xB3, 0x61, 0xAF, 0x39, 0x35, 0xDE, 0xCD, 0x1F, 0x99, 0xAC, 0xAD, 0x72, 0x2C, 0xDD, 0xD0, 0x87, 0xBE, 0x5E, 0xA6, 0xEC, 0x04, 0xC6, 0x03, 0x34, 0xFB, 0xDB, 0x59, 0xB6, 0xC2, 0x01, 0xF0, 0x5A, 0xED, 0xA7, 0x66, 0x21, 0x7F, 0x8A, 0x27, 0xC7, 0xC0, 0x29, 0xD7 ] ,
		    2 => [ 0x93, 0xD9, 0x9A, 0xB5, 0x98, 0x22, 0x45, 0xFC, 0xBA, 0x6A, 0xDF, 0x02, 0x9F, 0xDC, 0x51, 0x59, 0x4A, 0x17, 0x2B, 0xC2, 0x94, 0xF4, 0xBB, 0xA3, 0x62, 0xE4, 0x71, 0xD4, 0xCD, 0x70, 0x16, 0xE1, 0x49, 0x3C, 0xC0, 0xD8, 0x5C, 0x9B, 0xAD, 0x85, 0x53, 0xA1, 0x7A, 0xC8, 0x2D, 0xE0, 0xD1, 0x72, 0xA6, 0x2C, 0xC4, 0xE3, 0x76, 0x78, 0xB7, 0xB4, 0x09, 0x3B, 0x0E, 0x41, 0x4C, 0xDE, 0xB2, 0x90, 0x25, 0xA5, 0xD7, 0x03, 0x11, 0x00, 0xC3, 0x2E, 0x92, 0xEF, 0x4E, 0x12, 0x9D, 0x7D, 0xCB, 0x35, 0x10, 0xD5, 0x4F, 0x9E, 0x4D, 0xA9, 0x55, 0xC6, 0xD0, 0x7B, 0x18, 0x97, 0xD3, 0x36, 0xE6, 0x48, 0x56, 0x81, 0x8F, 0x77, 0xCC, 0x9C, 0xB9, 0xE2, 0xAC, 0xB8, 0x2F, 0x15, 0xA4, 0x7C, 0xDA, 0x38, 0x1E, 0x0B, 0x05, 0xD6, 0x14, 0x6E, 0x6C, 0x7E, 0x66, 0xFD, 0xB1, 0xE5, 0x60, 0xAF, 0x5E, 0x33, 0x87, 0xC9, 0xF0, 0x5D, 0x6D, 0x3F, 0x88, 0x8D, 0xC7, 0xF7, 0x1D, 0xE9, 0xEC, 0xED, 0x80, 0x29, 0x27, 0xCF, 0x99, 0xA8, 0x50, 0x0F, 0x37, 0x24, 0x28, 0x30, 0x95, 0xD2, 0x3E, 0x5B, 0x40, 0x83, 0xB3, 0x69, 0x57, 0x1F, 0x07, 0x1C, 0x8A, 0xBC, 0x20, 0xEB, 0xCE, 0x8E, 0xAB, 0xEE, 0x31, 0xA2, 0x73, 0xF9, 0xCA, 0x3A, 0x1A, 0xFB, 0x0D, 0xC1, 0xFE, 0xFA, 0xF2, 0x6F, 0xBD, 0x96, 0xDD, 0x43, 0x52, 0xB6, 0x08, 0xF3, 0xAE, 0xBE, 0x19, 0x89, 0x32, 0x26, 0xB0, 0xEA, 0x4B, 0x64, 0x84, 0x82, 0x6B, 0xF5, 0x79, 0xBF, 0x01, 0x5F, 0x75, 0x63, 0x1B, 0x23, 0x3D, 0x68, 0x2A, 0x65, 0xE8, 0x91, 0xF6, 0xFF, 0x13, 0x58, 0xF1, 0x47, 0x0A, 0x7F, 0xC5, 0xA7, 0xE7, 0x61, 0x5A, 0x06, 0x46, 0x44, 0x42, 0x04, 0xA0, 0xDB, 0x39, 0x86, 0x54, 0xAA, 0x8C, 0x34, 0x21, 0x8B, 0xF8, 0x0C, 0x74, 0x67 ] ,
		    3 => [ 0x68, 0x8D, 0xCA, 0x4D, 0x73, 0x4B, 0x4E, 0x2A, 0xD4, 0x52, 0x26, 0xB3, 0x54, 0x1E, 0x19, 0x1F, 0x22, 0x03, 0x46, 0x3D, 0x2D, 0x4A, 0x53, 0x83, 0x13, 0x8A, 0xB7, 0xD5, 0x25, 0x79, 0xF5, 0xBD, 0x58, 0x2F, 0x0D, 0x02, 0xED, 0x51, 0x9E, 0x11, 0xF2, 0x3E, 0x55, 0x5E, 0xD1, 0x16, 0x3C, 0x66, 0x70, 0x5D, 0xF3, 0x45, 0x40, 0xCC, 0xE8, 0x94, 0x56, 0x08, 0xCE, 0x1A, 0x3A, 0xD2, 0xE1, 0xDF, 0xB5, 0x38, 0x6E, 0x0E, 0xE5, 0xF4, 0xF9, 0x86, 0xE9, 0x4F, 0xD6, 0x85, 0x23, 0xCF, 0x32, 0x99, 0x31, 0x14, 0xAE, 0xEE, 0xC8, 0x48, 0xD3, 0x30, 0xA1, 0x92, 0x41, 0xB1, 0x18, 0xC4, 0x2C, 0x71, 0x72, 0x44, 0x15, 0xFD, 0x37, 0xBE, 0x5F, 0xAA, 0x9B, 0x88, 0xD8, 0xAB, 0x89, 0x9C, 0xFA, 0x60, 0xEA, 0xBC, 0x62, 0x0C, 0x24, 0xA6, 0xA8, 0xEC, 0x67, 0x20, 0xDB, 0x7C, 0x28, 0xDD, 0xAC, 0x5B, 0x34, 0x7E, 0x10, 0xF1, 0x7B, 0x8F, 0x63, 0xA0, 0x05, 0x9A, 0x43, 0x77, 0x21, 0xBF, 0x27, 0x09, 0xC3, 0x9F, 0xB6, 0xD7, 0x29, 0xC2, 0xEB, 0xC0, 0xA4, 0x8B, 0x8C, 0x1D, 0xFB, 0xFF, 0xC1, 0xB2, 0x97, 0x2E, 0xF8, 0x65, 0xF6, 0x75, 0x07, 0x04, 0x49, 0x33, 0xE4, 0xD9, 0xB9, 0xD0, 0x42, 0xC7, 0x6C, 0x90, 0x00, 0x8E, 0x6F, 0x50, 0x01, 0xC5, 0xDA, 0x47, 0x3F, 0xCD, 0x69, 0xA2, 0xE2, 0x7A, 0xA7, 0xC6, 0x93, 0x0F, 0x0A, 0x06, 0xE6, 0x2B, 0x96, 0xA3, 0x1C, 0xAF, 0x6A, 0x12, 0x84, 0x39, 0xE7, 0xB0, 0x82, 0xF7, 0xFE, 0x9D, 0x87, 0x5C, 0x81, 0x35, 0xDE, 0xB4, 0xA5, 0xFC, 0x80, 0xEF, 0xCB, 0xBB, 0x6B, 0x76, 0xBA, 0x5A, 0x7D, 0x78, 0x0B, 0x95, 0xE3, 0xAD, 0x74, 0x98, 0x3B, 0x36, 0x64, 0x6D, 0xDC, 0xF0, 0x59, 0xA9, 0x4C, 0x17, 0x7F, 0x91, 0xB8, 0xC9, 0x57, 0x1B, 0xE0, 0x61 ] 
        ] ;
		// the same arrays for faster indexing (without i%4)
		$this->Sbox[4]  = $this->Sbox[0] ;  
        $this->Sbox[5]  = $this->Sbox[1] ;
		$this->Sbox[6]  = $this->Sbox[2] ;
		$this->Sbox[7]  = $this->Sbox[3] ;

        //data for linear transformation (psi) 
		$this->v = [ 0x01, 0x01, 0x05, 0x01, 0x08, 0x06, 0x07, 0x04 ] ;

        $this->shift = [ 0, 1, 2, 3, 4, 5, 6, ( $this->block_size == 1024 ? 11 : 7 ) ] ;

        //2^x in GF - values and indexes (for fast multiplication in GF)
		$this->pw2val = [ 1, 2, 4, 8, 16, 32, 64, 128, 29, 58, 116, 232, 205, 135, 19, 38, 76, 152, 45, 90, 180, 117, 234, 201, 143, 3, 6, 12, 24, 48, 96, 192, 157, 39, 78, 156, 37, 74, 148, 53, 106, 212, 181, 119, 238, 193, 159, 35, 70, 140, 5, 10, 20, 40, 80, 160, 93, 186, 105, 210, 185, 111, 222, 161, 95, 190, 97, 194, 153, 47, 94, 188, 101, 202, 137, 15, 30, 60, 120, 240, 253, 231, 211, 187, 107, 214, 177, 127, 254, 225, 223, 163, 91, 182, 113, 226, 217, 175, 67, 134, 17, 34, 68, 136, 13, 26, 52, 104, 208, 189, 103, 206, 129, 31, 62, 124, 248, 237, 199, 147, 59, 118, 236, 197, 151, 51, 102, 204, 133, 23, 46, 92, 184, 109, 218, 169, 79, 158, 33, 66, 132, 21, 42, 84, 168, 77, 154, 41, 82, 164, 85, 170, 73, 146, 57, 114, 228, 213, 183, 115, 230, 209, 191, 99, 198, 145, 63, 126, 252, 229, 215, 179, 123, 246, 241, 255, 227, 219, 171, 75, 150, 49, 98, 196, 149, 55, 110, 220, 165, 87, 174, 65, 130, 25, 50, 100, 200, 141, 7, 14, 28, 56, 112, 224, 221, 167, 83, 166, 81, 162, 89, 178, 121, 242, 249, 239, 195, 155, 43, 86, 172, 69, 138, 9, 18, 36, 72, 144, 61, 122, 244, 245, 247, 243, 251, 235, 203, 139, 11, 22, 44, 88, 176, 125, 250, 233, 207, 131, 27, 54, 108, 216, 173, 71, 142, 1, 2, 4, 8, 16, 32, 64, 128, 29, 58, 116, 232, 205, 135, 19, 38, 76, 152, 45, 90, 180, 117, 234, 201, 143, 3, 6, 12, 24, 48, 96, 192, 157, 39, 78, 156, 37, 74, 148, 53, 106, 212, 181, 119, 238, 193, 159, 35, 70, 140, 5, 10, 20, 40, 80, 160, 93, 186, 105, 210, 185, 111, 222, 161, 95, 190, 97, 194, 153, 47, 94, 188, 101, 202, 137, 15, 30, 60, 120, 240, 253, 231, 211, 187, 107, 214, 177, 127, 254, 225, 223, 163, 91, 182, 113, 226, 217, 175, 67, 134, 17, 34, 68, 136, 13, 26, 52, 104, 208, 189, 103, 206, 129, 31, 62, 124, 248, 237, 199, 147, 59, 118, 236, 197, 151, 51, 102, 204, 133, 23, 46, 92, 184, 109, 218, 169, 79, 158, 33, 66, 132, 21, 42, 84, 168, 77, 154, 41, 82, 164, 85, 170, 73, 146, 57, 114, 228, 213, 183, 115, 230, 209, 191, 99, 198, 145, 63, 126, 252, 229, 215, 179, 123, 246, 241, 255, 227, 219, 171, 75, 150, 49, 98, 196, 149, 55, 110, 220, 165, 87, 174, 65, 130, 25, 50, 100, 200, 141, 7, 14, 28, 56, 112, 224, 221, 167, 83, 166, 81, 162, 89, 178, 121, 242, 249, 239, 195, 155, 43, 86, 172, 69, 138, 9, 18, 36, 72, 144, 61, 122, 244, 245, 247, 243, 251, 235, 203, 139, 11, 22, 44, 88, 176, 125, 250, 233, 207, 131, 27, 54, 108, 216, 173, 71, 142, 1 ] ;
		$this->pw2ind = [ 0, 0, 1, 25, 2, 50, 26, 198, 3, 223, 51, 238, 27, 104, 199, 75, 4, 100, 224, 14, 52, 141, 239, 129, 28, 193, 105, 248, 200, 8, 76, 113, 5, 138, 101, 47, 225, 36, 15, 33, 53, 147, 142, 218, 240, 18, 130, 69, 29, 181, 194, 125, 106, 39, 249, 185, 201, 154, 9, 120, 77, 228, 114, 166, 6, 191, 139, 98, 102, 221, 48, 253, 226, 152, 37, 179, 16, 145, 34, 136, 54, 208, 148, 206, 143, 150, 219, 189, 241, 210, 19, 92, 131, 56, 70, 64, 30, 66, 182, 163, 195, 72, 126, 110, 107, 58, 40, 84, 250, 133, 186, 61, 202, 94, 155, 159, 10, 21, 121, 43, 78, 212, 229, 172, 115, 243, 167, 87, 7, 112, 192, 247, 140, 128, 99, 13, 103, 74, 222, 237, 49, 197, 254, 24, 227, 165, 153, 119, 38, 184, 180, 124, 17, 68, 146, 217, 35, 32, 137, 46, 55, 63, 209, 91, 149, 188, 207, 205, 144, 135, 151, 178, 220, 252, 190, 97, 242, 86, 211, 171, 20, 42, 93, 158, 132, 60, 57, 83, 71, 109, 65, 162, 31, 45, 67, 216, 183, 123, 164, 118, 196, 23, 73, 236, 127, 12, 111, 246, 108, 161, 59, 82, 41, 157, 85, 170, 251, 96, 134, 177, 187, 204, 62, 90, 203, 89, 95, 176, 156, 169, 160, 81, 11, 245, 22, 235, 122, 117, 44, 215, 79, 174, 213, 233, 230, 231, 173, 232, 116, 214, 244, 234, 168, 80, 88, 175 ] ;
		
    }

    /**
     * Digest (hash) computing
     * @param message
     * @param mode
     * @return HEX digest
     */
    public function digest( $message, $mode = "STR" ) {
        switch( $mode ) {
            case "STR" :
				return $this->from_utf8( $message ) ;
				break ;
            case "HEX" :
                return $this->from_hex( $message ) ;
                break ;
            default :
                throw new Exception( "Unsupported mode. STR or HEX" ) ; 
        }
    }

    /**
     * Initial assignment
     */
    private function init_state() {
        $this->state = [ ] ;
		$this->iv    = [ ] ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			$this->state[ $j ]  = [ 0, 0, 0, 0, 0, 0, 0, 0 ] ;
			$this->iv[ $j ]     = [ 0, 0, 0, 0, 0, 0, 0, 0 ] ;
		}
		if( $this->block_size == 512 ) {
			$this->iv[0][0] = 0x40 ;
		}
		else {
			$this->iv[0][0] = 0x80 ;
		}
    }

    /** 
     * Multiplication in GF
     * @param a multiplier
     * @param b multiplier
     * @return a x b (mod GF)
     */
	private function gf_mul( $a, $b ) {
		if( $a == 0 || $b == 0 ) {
			return 0 ;
        }
		return $this->pw2val[
            $this->pw2ind[ $a ] + 
            $this->pw2ind[ $b ]
        ] ;
	}

	/**
     * Hash computing considering message to be UTF-8 string
     * @param message input string
     * @return HEX digest
     */
    private function from_utf8( $message ) {
		if( ! is_string( $message ) ) {
            throw new Exception( "Invalid input type. String only" ) ;
        }
        $this->init_state() ;
        $symbol_length = strlen( $message ) ;
		$message_bit_length = $symbol_length * 8 ;
		$position = 0 ;
        $i = 0 ;
        $j = 0 ;
        for( $position = 0; $position < $symbol_length; ++$position ) {
			$this->state[ $j ][ $i ] = ord( $message[ $position ] ) ;
			++$i ;
			if( $i >= 8 ) {
				$i = 0 ;
				++$j ;
				if( $j >= $this->columns ) {
					// block is full, proceed it
					$this->iv = $this->hash_v( $this->state ) ;
					$i = 0 ;
					$j = 0 ;
				}
			}
		}
		// Append "1" bit
		$this->state[ $j ][ $i ] = 0x80 ;
		// zero tail
		$remainder = 0 ;
		++$i ;
		while( $j < $this->columns ) {
			while( $i < 8 ) {
				$this->state[ $j ][ $i ] = 0 ;
				$remainder += 8 ;
				++$i ;
			}
			$i = 0 ;
			++$j ;
		}
		// echo $remainder, "<br>" ;
		if( $remainder < 96 ) {
			$this->iv = $this->hash_v( $this->state ) ;
			for( $j = 0; $j < $this->columns; ++$j ) {
				$this->state[ $j ]  = [ 0, 0, 0, 0, 0, 0, 0, 0 ] ;
			}
		}
		$this->write_size( $message_bit_length ) ;
		// return $this->matrix_to_hex( $this->state, "<br>" ) ; 
		$this->iv = $this->hash_v( $this->state ) ;
		return $this->reduce_ln() ;
	}
    
    /**
     * Hash computing considering message to be HEX string
     * @param message input string
     * @return HEX digest
     */
    private function from_hex( $message ) {
        if( ! is_string( $message ) ) {
            throw new Exception( "Invalid input type. String only" ) ;
        }
        $this->init_state() ;
        $symbol_length = strlen( $message ) ;
        $message_bit_length = $symbol_length * 4 ;
        $full_blocks = 0 ;
        $position = 0 ;
        $i = 0 ;
        $j = 0 ;
        for( $position = 0; $position < $symbol_length; ++$position ) {
            $sym_code = ord( $message[ $position ] ) ;

            if( $sym_code >= 48 && $sym_code <= 57 )       $digit = $sym_code - 48 ;
            else if( $sym_code >= 65 && $sym_code <= 70 )  $digit = $sym_code - 55 ;
            else if( $sym_code >= 97 && $sym_code <= 102 ) $digit = $sym_code - 87 ;
            else throw new Exception( "Invalid symbol (char: '{$message[ $position ]}', code: $sym_code, pos: $position). HEX only" ) ;
            
            if( $position & 1 ) {
                $this->state[ $j ][ $i ] += $digit ;
                ++$i ;
                if( $i >= 8 ) {
                    $i = 0 ;
                    ++$j ;
                    if( $j >= $this->columns ) {
                        // block is full, proceed it
                        $this->iv = $this->hash_v( $this->state ) ;
			//	$full_blocks++ ; if( $full_blocks == 1) $this->matrix_to_hex( $this->transform_0( $this->state ), "<br>" ) ; // return $this->matrix_to_hex( $this->iv, "<br>" ) ;		
						$i = 0 ;
						$j = 0 ;
                    }
                }
            }
            else {
                $this->state[ $j ][ $i ] = $digit << 4 ;
            }
        }

		// Append "1" bit
		if( $position & 1 ) {
			$this->state[ $j ][ $i ] += 0x08 ;
		}
		else {
			$this->state[ $j ][ $i ] = 0x80 ;
		}
		// zero tail
		$remainder = 0 ;
		++$i ;
		while( $j < $this->columns ) {
			while( $i < 8 ) {
				$this->state[ $j ][ $i ] = 0 ;
				$remainder += 8 ;
				++$i ;
			}
			$i = 0 ;
			++$j ;
		}
		// echo $remainder, "<br>" ;
		if( $remainder < 96 ) {
			$this->iv = $this->hash_v( $this->state ) ;
			for( $j = 0; $j < $this->columns; ++$j ) {
				$this->state[ $j ]  = [ 0, 0, 0, 0, 0, 0, 0, 0 ] ;
			}
		}
		$this->write_size( $message_bit_length ) ;
		$this->iv = $this->hash_v( $this->state ) ;
		return $this->reduce_ln() ;
    }

	private function write_size( $bit_length ) {
		// 96 bit length little endian
		$this->state[ $this->columns - 2 ][ 4 ] = ( $bit_length & 0xFF ) ;
		$this->state[ $this->columns - 2 ][ 5 ] = ( $bit_length >> 8  ) & 0xFF ;
		$this->state[ $this->columns - 2 ][ 6 ] = ( $bit_length >> 16 ) & 0xFF ;
		$this->state[ $this->columns - 2 ][ 7 ] = ( $bit_length >> 24 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 0 ] = ( $bit_length >> 32 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 1 ] = ( $bit_length >> 40 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 2 ] = ( $bit_length >> 48 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 3 ] = ( $bit_length >> 56 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 4 ] = ( $bit_length >> 64 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 5 ] = ( $bit_length >> 72 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 6 ] = ( $bit_length >> 80 ) & 0xFF ;
		$this->state[ $this->columns - 1 ][ 7 ] = ( $bit_length >> 88 ) & 0xFF ;
	}

    private function matrix_to_hex( $matrix, $rows_separator = '' ) {
		$ret = "" ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			for( $i = 0; $i < 8; ++$i ) {
				if( $matrix[ $j ][ $i ] < 0x10 )
					$ret .= "0" ;
				$ret .= strtoupper( dechex( $matrix[ $j ][ $i ] ) ) ;
			}
			$ret .= $rows_separator ;
		}
		return $ret ;
	}

    private function transform_0( $matrix ) {
        $matrix1 = [] ;
		$matrix2 = [] ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			$matrix1[ $j ] = [] ;
			$matrix2[ $j ] = [] ;
			for( $i = 0; $i < 8; ++$i ) {
				$matrix1[ $j ][ $i ] = 0 ;
				$matrix2[ $j ][ $i ] = $matrix[ $j ][ $i ] ;
			}
		}
		for( $n = 0; $n < $this->iterations; ++$n ) {
			for( $j = 0; $j < $this->columns; ++$j ) {
				$matrix2[ $j ][ 0 ] ^= ( $j << 4 ) ^ $n ;  // kappa
				for( $i = 0; $i < 8; ++$i ) {
					$matrix1[ ( $j + $this->shift[ $i ] ) & ( $this->columns - 1 ) ][ $i ] = 
                        $this->Sbox[ $i ][ $matrix2[ $j ][ $i ] ] ;						
				}
			}
			// psi
			for( $j = 0; $j < $this->columns; ++$j ) {
				for( $i = 0; $i < 8; ++$i ) {
					$acc = 0 ;
					for( $k = 0; $k < 8; ++$k ) {
						$acc ^= $this->gf_mul(
                            $this->v[ ( $k - $i + 8 ) & 7 ], 
                            $matrix1[ $j ][ $k ] ) ;
					}
					$matrix2[ $j ][ $i ] = ( $acc & 0xFF ) ;
				}
			}
		}
		return $matrix2 ;
    }

    private function transform_1( $matrix ) {
        $matrix1 = [] ;
		$matrix2 = [] ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			$matrix1[ $j ] = [] ;
			$matrix2[ $j ] = [] ;
			for( $i = 0; $i < 8; ++$i ) {
				$matrix1[ $j ][ $i ] = 0 ;
				$matrix2[ $j ][ $i ] = $matrix[ $j ][ $i ] ;
			}
		}
		for( $n = 0; $n < $this->iterations; ++$n ) {
            for( $j = 0; $j < $this->columns; ++$j ) {
                $dz = [ 243, 240, 240, 240, 240, 240, 240, ( ( ( $this->columns - 1 - $j ) << 4 ) ^ $n ) & 0xFF ] ;
                $carry_over = 0 ;
				for( $i = 0; $i < 8; ++$i ) {
					$matrix2[ $j ][ $i ] = $matrix2[ $j ][ $i ] + $dz[ $i ] + $carry_over ;
					if( $matrix2[ $j ][ $i ] > 0xFF ) {
						$matrix2[ $j ][ $i ] &= 0xFF ;
						$carry_over = 1;
					}
					else $carry_over = 0;
				}
            }
            for( $j = 0; $j < $this->columns; ++$j ) {
				for( $i = 0; $i < 8; ++$i ) {
					$matrix1[ ( $j + $this->shift[ $i ] ) & ( $this->columns - 1 ) ][ $i ] = 
                        $this->Sbox[ $i ][ $matrix2[ $j ][ $i ] ] ;						
				}
			}
			// psi
			for( $j = 0; $j < $this->columns; ++$j ) {
				for( $i = 0; $i < 8; ++$i ) {
					$acc = 0 ;
					for( $k = 0; $k < 8; ++$k ) {
						$acc ^= $this->gf_mul(
                            $this->v[ ( $k - $i + 8 ) & 7 ], 
                            $matrix1[ $j ][ $k ] ) ;
					}
					$matrix2[ $j ][ $i ] = ( $acc & 0xFF ) ;
				}
			}
		}
		return $matrix2 ;
    }

    private function hash_v( $matrix ) {
        $h = [] ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			$h[ $j ] = [] ;
			for( $i = 0; $i < 8; ++$i ) {
				$h[ $j ][ $i ] = $this->iv[ $j ][ $i ] ^ $matrix[ $j ][ $i ] ;
			}
		}
		$t0 = $this->transform_0( $h ) ;
		$t1 = $this->transform_1( $this->state ) ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			for( $i = 0; $i < 8; ++$i ) {
				$h[ $j ][ $i ] = $t0[ $j ][ $i ] ^ $t1[ $j ][ $i ] ^ $this->iv[ $j ][ $i ] ;
			}
		}
		return $h ;
    }

	private function reduce_ln() {
		$t0 = $this->transform_0( $this->iv ) ;
		for( $j = 0; $j < $this->columns; ++$j ) {
			for( $i = 0; $i < 8; ++$i ) {
				$this->state[ $j ][ $i ] = $t0[ $j ][ $i ] ^ $this->iv[ $j ][ $i ] ;
			}
		}
		$start_index = ( $this->block_size - $this->hash_size ) / 4 ;
		return substr( $this->matrix_to_hex( $this->state ), $start_index ) ;
	}

}
