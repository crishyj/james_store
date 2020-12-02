<?php
    $family = $_GET['family'];
    
    function family_echo_api_temp_view($format, $family, $focus=null,
        $logo_url=null, $logo_width=null, $logo_height=null)
    {
        $params=compact('format', 'family', 'focus',
            'logo_url', 'logo_width', 'logo_height');
        
        $poststring='operation=temp_view';

        foreach ($params as $key => $value)
            if (isset($value))
                $poststring.='&'.urlencode($key).'='.urlencode($value);
                
        $curl=curl_init('http://api.familyecho.com/');

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $poststring);
            
        $result=curl_exec($curl);
            
        curl_close($curl);

        return $result;
    }

   

    $json = json_decode(stripslashes(family_echo_api_temp_view(
        "json", $family )),true);
     //print_r( $json );
     //echo $json['url'];


    header('Location: '.$json['url']);


/*
                <iframe id="familytree"></iframe>
                

                <script>
                var iframe = document.getElementById('familytree'),
                iframedoc = iframe.contentDocument || iframe.contentWindow.document;

                iframedoc.body.innerHTML = 
                    '<div class="tm" style="">'+
                    
                    
                        '<textarea class="tm" name="family" cols="80" rows="8" style="display:none; width:100%"></textarea>'+
                        
                        '<input name="operation" value="temp_view" type="hidden">'+
                        '<input type="hidden" name="format" value="redirect">'+
                            
                        
                        '<p class="td"><input value="Show tree" type="submit"></p>'+
                    
                    '</div>';
                </script>

                    
                
*/
    