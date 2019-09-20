<?php
include('restclient.php');

function showRCPubs($params) {

    $token = $params['token'];
    $url = $params['url'];

    if (!isset($token) or trim($token) === ''){
        return '<strong>token parameter must be set</strong>';
    }
    if (!isset($url) or trim($url) === ''){
        return '<strong>url parameter must be set</strong>';
    }

    $api = new RestClient([
        'base_url' => $url,
        'headers' => ['Authorization' => 'Token ' . $token],
    ]);

    $result = $api->get('api/pubs/', ['facility' => 'rc', 'start' => '2008-01-01', 'page' => 1, 'page_size' => 100000]);

    if ($result->info->http_code == 200) {

        $result = $result->decode_response();
        $pubs = $result['results'];
        $out = [];
        $pubstrs = [];
        $currentyear = date('Y');
        $pubcount = 0;
        foreach($pubs as $pub){
            $pubdate = date('Y', strtotime($pub['date']));
            if ($pubdate != $currentyear) {
                array_push($out,
                    sprintf(
                        "
                            <h3><strong>%s (%d publications)</strong></h3>\n
                            <ol>%s</ol>\n
                        ",
                        $currentyear,
                        $pubcount,
                        implode("\n", $pubstrs)
                    )
                );
                $pubcount = 0;
                $pubstrs = [];
                $currentyear = $pubdate;
            }
            array_push($pubstrs,
                sprintf(
                    "<li>%s %s. %s <i>%s</i></li>",
                    $pub['authors'],
                    $pubdate,
                    $pub['title'],
                    $pub['citation']
                )
            );
            $pubcount++;
        }
        array_push($out,
            sprintf(
                "
                    <h3><strong>%s (%d publications)</strong></h3>\n
                    <ol>%s</ol>\n
                ",
                $currentyear,
                $pubcount,
                implode("\n", $pubstrs)
            )
        );
        $out = implode("\n", $out);
        return $out;
    } else {
        error_log('Error getting publications from API.  Error code is ' . $result->info->http_code);
        return '<em>There was an error fetching the publications list from the API.  I know, right?</em>';
    }
}
add_shortcode('show_rc_pubs', 'showRCPubs');

// Gets the activation information for a BuildStack
// Used in applications types page
function getBuildStackActivation($params) {
    $url = $params['url'];
    $build_stack = $params['build_stack'];

    if (!isset($url) or trim($url) === ''){
        return '<strong>url parameter must be set</strong>';
    }
    if (!isset($build_stack) or trim($build_stack) === ''){
        return '<strong>build_stack parameter must be set</strong>';
    }

    $api = new RestClient([
        'base_url' => $url,
    ]);

    $result = $api->get($build_stack . '/', ['format' => 'json']);

    if ($result->info->http_code == 200) {

        $result = $result->decode_response();
        $activation = $result['activation'];
        return $activation;
    } else {
        error_log('Error getting build_stack from API.  Error code is ' . $result->info->http_code);
        return '<em>There was an error fetching the build_stack from the API.  I know, right?</em>';
    }
}
add_shortcode('get_build_stack_activation', 'getBuildStackActivation');

