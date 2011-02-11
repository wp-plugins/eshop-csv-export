<?php

class CSV {

	var $error = '';
	var $delimiter = ',';
	var $enclosure = '"';

  function fputcsv(&$handle, $fields = array( ) ) {
    $str = '';
    $escape_char = '\\';
    foreach ($fields as $value) {
      if (strpos($value, $this->delimiter) !== false ||
          strpos($value, $this->enclosure) !== false ||
          strpos($value, "\n") !== false ||
          strpos($value, "\r") !== false ||
          strpos($value, "\t") !== false ||
          strpos($value, ' ') !== false) {
        $str2 = $this->enclosure;
        $escaped = 0;
        $len = strlen($value);
        for ($i=0;$i<$len;$i++) {
          if ($value[$i] == $escape_char) {
            $escaped = 1;
          } else if (!$escaped && $value[$i] == $this->enclosure) {
            $str2 .= $this->enclosure;
          } else {
            $escaped = 0;
          }
          $str2 .= $value[$i];
        }
        $str2 .= $this->enclosure;
        $str .= $str2.$this->delimiter;
      } else {
        $str .= $value.$this->delimiter;
      }
    }
    $str = substr($str,0,-1);
    $str .= "\n";
    return fwrite($handle, $str);
  }

	function saveToFile( $csv_data = array( ), $filename = 'csvdata', $path = '/tmp' ) {

		if ( isset( $csv_data[0] ) ) {
			
			$fullpath = $path . '/' . $filename . '.csv';
			$fp = @fopen( $fullpath, 'w' );
			if ( $fp ) {
				foreach ( $csv_data as $cd ) {
					$fwrite = $this->fputcsv( $fp, $cd );
				}
			} else {
				echo "<p><strong>Error: unable to create a new file ($fullpath).  Please check your folder permissions. <a href='http://codex.wordpress.org/Changing_File_Permissions'>More information.</a></strong></p>"; 
			}
			return ( @fclose( $fp ) );
		}
		return FALSE;
	}

}

?>
