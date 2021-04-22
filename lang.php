<?php


function lang($key)
{
    $languages = file('langfile.csv',FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lang = 1; //english
    if(getLocale() == "fr")
    {
        $lang = 2; //french
    }

    $key = trim($key);
    foreach($languages as $language)
    {
        $parts = explode(',',$language);
        if(strcasecmp($parts[0], $key) == 0)
        {
            return $parts[$lang];
        }
    }

    return "";
}



function getLocale()
{
    $locale = getHeader('Accept-Language') ?? 'en';
    $parts = explode(';',$locale);
    if(count($parts) > 0)
    {
        $parts =  explode(',',$parts[0]);
        if(count($parts) > 1)
        {
            return $parts[1];
        }
        return $parts[0];
    }

    return $parts[0];
}



function  getHeader($key)
{
      $key = strtolower($key);
      $headers = apache_request_headers();
    
      foreach ($headers as $header => $value) 
      {
        if(strtolower($header) == $key)
        {
            return $value;
        }
      }

      return NULL;
}


if( !function_exists('apache_request_headers') ) 
{
    function apache_request_headers() {
      $arh = array();
      $rx_http = '/\AHTTP_/';
      foreach($_SERVER as $key => $val) {
        if( preg_match($rx_http, $key) ) {
          $arh_key = preg_replace($rx_http, '', $key);
          $rx_matches = array();
          // do some nasty string manipulations to restore the original letter case
          // this should work in most cases
          $rx_matches = explode('_', $arh_key);
          if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
            foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
            $arh_key = implode('-', $rx_matches);
          }
          $arh[$arh_key] = $val;
        }
      }
      return( $arh );
    }

}
