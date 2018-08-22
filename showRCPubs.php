// Retrieves the RC publication list and displays on website

include('restclient.php')

function showRCPubs($params) {

    $token = $params['token'];
    $url = $params['url'];

    $api = new RestClient([
        'base_url' => $url,
        'format' => 'json',
        'headers' => ['Authorization' => 'token ' . $token],
    ]);

    $result = $api->get('api/pubs/', ['facility' => 'rc']);

    if ($result->info->http_code == 200) {

        $pubs = $result->decode_response();
        $out = [];
        $pubstrs = [];
        $currentyear = date('Y');
        $pubcount = 0;
        foreach($pubs as $pub){
            $pubdate = date('Y', strtotime($pub['date']));
            if ($pubdate != $currentyear) {
                $out.push(
                    sprintf(
                        "
                            <h3><strong>%s (%d publications)</strong></h3>\n
                            <ol>\n
                                 <li>%s
                                 </li>
                            </ol>\n
                        ",
                        $pubdate,
                        $pubcount,
                        implode("\n", $pubstrs)
                    )
                );
                $pubcount = 0;
                $pubstrs = [];
            }
            else {
                $pubstrs.push(
                    sprintf(
                        "<li>%s %s. %s <i>%s</i>.</li>",
                        $pub['authors'],
                        $pubdate,
                        $pub['title'],
                        $pub['citation']
                    )
                );
            }
            $pubcount++;
        }
        $out = implode("\n", $out);
        return $out;
    } else {
        error_log('Error getting publications from API.  Error code is ' . $result->info->http_code);
        return '<em>There was an error fetching the publications list from the API.  I know, right?</em>';
    }
}
add_shortcode('show_rc_pubs', 'showRCPubs');
