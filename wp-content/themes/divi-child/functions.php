<?php
function dt_enqueue_styles()
{
    $parenthandle = 'divi-style';
    $theme = wp_get_theme();
    wp_enqueue_style(
        $parenthandle,
        get_template_directory_uri() . '/style.css',
        array(),
        $theme->parent()->get('Version')
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        array($parenthandle),
        $theme->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'dt_enqueue_styles');

function enqueue_custom_js()
{
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-js', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('canvas', 'https://cdn.canvasjs.com/canvasjs.min.js', array('jquery'), '1.0', true);
    wp_enqueue_style('br-custom-fonts', 'https://fonts.cdnfonts.com/css/satoshi', false);
    wp_enqueue_style( 'output', get_stylesheet_directory_uri() . '/dist/output.css', array() );
}
add_action('wp_enqueue_scripts', 'enqueue_custom_js');


// $simple_html_dom_path = get_stylesheet_directory_uri()  . '/php-simple-html-dom-parser-master/Src/Sunra/PhpSimple/simplehtmldom_1_5/simple_html_dom.php';

// mock milistream api
// Define a function to create the custom database table
function create_milistream_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'milistream_data';

    // Drop the table if it already exists
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    $charset_collate = $wpdb->get_charset_collate();

    // SQL query to create the custom table
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        content text NOT NULL,
        image_url varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action("after_switch_theme", 'create_milistream_table');

// Define a function to insert mock data into the custom database table
function insert_mock_data_into_milistream_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'milistream_data';

    // Define an array of mock data
    $mock_data = array(
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'https://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image1.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image2.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image3.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image4.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image5.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image6.png',
        ),
        array(
            'title' => 'Geopolitik',
            'content' => 'KONSUMENTVERKET STOPPAR CYKELHJÄLMAR',
            'image_url' => 'http://localhost/aktier/wp-content/themes/divi-child/uploads/mock-images/image7.png',
        ),
    );

    // Insert mock data into the custom table
    foreach ($mock_data as $data) {
        $wpdb->insert(
            $table_name,
            $data
        );
    }
}

// Hook the function to run during plugin activation (optional)
add_action("after_switch_theme", 'insert_mock_data_into_milistream_table');

// Define the shortcode function for milistream data
function milistream_shortcode()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'milistream_data';

    // Retrieve mock data from the milistream_data table
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    $output = '';

    // Display the retrieved data
    $output .= '<div class="et_pb_module milistream">';
    $output .= '<div class="et_pb_ajax_pagination_container">';

    // Display all items initially
    $count = 0;
    foreach ($results as $result) {
        $output .= '<article class="et_pb_post clearfix et_pb_blog_item_1_0 post-296 post type-post status-publish format-standard has-post-thumbnail hentry milistream-item">';
        $output .= '<a href="#" class="entry-featured-image-url"><img loading="lazy" decoding="async" src="' . esc_url($result->image_url) . '" alt="' . esc_html($result->title) . '" class="" width="1080" height="675"></a>';
        $output .= '<h2 class="entry-title"><a href="#">' . esc_html($result->title) . '</a></h2>';
        $output .= '<div class="post-content"><div class="post-content-inner"><p>' . esc_html($result->content) . '</p></div></div>';
        $output .= '</article>';
        $count++;
        if ($count == 6)
            break;
    }

    $output .= '</div>';
    $output .= '</div>';

    return $output;
}
add_shortcode('milistream', 'milistream_shortcode');


// Define the shortcode function for discord data on front page
function discords_shortcode()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'realtime_discords';
    $results = $wpdb->get_results("SELECT * FROM $table_name");
    $output = '';
    $output .= '<div class="discords">';
    $count = 0;
    foreach ($results as $result) {
        $members =  $result->member_count;
        $output .= $result->img_src ? '<div class="discord"><div class="discord-col-l"><div class="discord-image"><a href="' . esc_url($result->url) . '"><img src="' . $result->img_src . '" alt="' . $result->name . '" class="" width="1080" height="675"></a></div>'
            : '<div class="discord"><div class="discord-col-l"><div class="discord-image"><a href="' . esc_url($result->url) . '"><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/question-icon.jpg" alt="' . $result->name . '" class="" width="1080" height="675"></a></div>';
        $output .= '<div class="discord-tc"><div class="discord-title"><a href="#">' . esc_html($result->name) . '</a></div><div class="discord-content"><p>' . esc_html($result->name) . '</p></div></div></div>';
        $output .= '<div class="discord-col-r"><p class="discord-members">' . esc_html($members) . '</p></div></div>';
        $count++;
        if ($count == 6) break;
    }
    $output .= '</div>';
    return $output;
}
add_shortcode('discords', 'discords_shortcode');

function get_discords_data($paged = 1, $per_page = 10, $search_term = '')
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'realtime_discords';
    $offset = ($paged - 1) * $per_page;

    $query = "SELECT * FROM $table_name";

    if (!empty($search_term)) {
        $query .= $wpdb->prepare(" WHERE name LIKE '%%%s%%'", $search_term);
    }

    $query .= $wpdb->prepare(" LIMIT %d, %d", $offset, $per_page);

    return $wpdb->get_results($query);
}

function discord_pagination_table_shortcode($atts)
{
    ob_start();

    $atts = shortcode_atts(array(
        'per_page' => 2,
    ), $atts);

?>
    <div class="discord-header">
        <div class="discord-header-title">OENIGHETER</div>
        <div class="discord-sp">
            <form id="discord-search-form" class="search" method="post" action="">
                <div class="search-img"><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/search.png" /></div>
                <input type="text" name="s" class="search-input" id="discord-search-input" value="" placeholder="Söktvister">
            </form>
            <div class="discord-sel-container">
                <p class="discord-sel-label">SHOW</p>
                <select class="discord-sel-main" id="per_page">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>
        </div>
    </div>
    <div>
        <div id="site-table-container">
            <table class="site-table" style="position: relative;">
                <thead class="site-thead">
                    <tr>
                        <th style="width: 5%;">NEJ.</th>
                        <th style="width: 25%;">DISCORD NAMN</th>
                        <th style="width: 35%;">BESKRIVNING</th>
                        <th style="width: 35%;">MEDLEMMAR</th>
                        <th>HANDLING</th>
                    </tr>
                </thead>
                <tbody id="discord-tbody" class="site-tbody" style="height: 450px">
                </tbody>
                <div class="loader"></div>
            </table>
        </div>
        <div id="pagination-buttons"></div>
    </div>
<?php

    return ob_get_clean();
}
add_shortcode('discord_pagination_table', 'discord_pagination_table_shortcode');

function discord_ajax_search()
{
    global $wpdb;

    $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

    $paged = max(1, isset($_POST['paged']) ? intval($_POST['paged']) : 1);
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 2;

    $query = $wpdb->prepare(
        "SELECT COUNT(*) 
        FROM {$wpdb->prefix}realtime_discords
        WHERE name LIKE %s",
        '%' . $wpdb->esc_like($search_term) . '%',
    );

    // Get the total number of records matching the search criteria
    $total_records = $wpdb->get_var($query);
    $total_pages = ceil($total_records / $per_page);

    $discords = get_discords_data($paged, $per_page, $search_term);

    $response = array(
        'discords' => $discords,
        'pageInformation' => array(
            'perPage' => $per_page,
            'totalPage' => $total_pages,
            'paged' => $paged
        )
    );
    if ($discords) {
        wp_send_json_success($response);
    } else {
        wp_send_json_error('No data found.');
    }

    // Make sure to exit after sending the JSON response
    exit;
}
add_action('wp_ajax_discord_ajax_search', 'discord_ajax_search');
add_action('wp_ajax_nopriv_discord_ajax_search', 'discord_ajax_search');

function stocks_table_shortcode($atts)
{
    ob_start();

    $atts = shortcode_atts(array(
        'per_page' => 2,
    ), $atts);

?>
    <div class="stocks-row">
        <div class="stocks-col-left">
            <div class="stocks-title bg-slate-900 text-amber-100">TOPPLISTA ÖVER SVENSKA AKTIER</div>
            <div class="stocks-content">Listan nedan visar de aktier där minst antal affärsnoter skapas i förhållande till hur många som handlat aktien det senaste året.</div>
            <div style="display:flex; justify-content: space-between;">
                <div id="up_diff1dprc"><span>1 dag </span> <img width="20" /><span></span></div>
                <div span id="up_diff3mprc"><span>3 månader</span><img width="20" /><span></span></div>
                <div id="up_diffqtdprc"><span>fjärdedel</span><img width="20" /><span></span></div>
                <div id="up_diff1yprc"><span>3 år</span><img width="20" /><span></span></div>
            </div>
        </div>
        <div class="stocks-col-right">
            <div class="stocks-rising-img">
                <img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/rising.png" />
            </div>
            <div class="search" method="post" action="">
                <div class="search-img"><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/search.png" /></div>
                <input type="text" class="search-input" id="up-search-input" value="" placeholder="Sök efter alla webbplatsaktier">
            </div>
        </div>
    </div>
    <div>
        <div>
            <div class="prc-btn-group" id="up_prc_btns">
                <a id="up_diff1dprc_btn">1 DAG </a>
                <a id="up_diff3mprc_btn">3 MÅNADER </a>
                <a id="up_diffqtdprc_btn">FJÄRDEDEL </a>
                <a id="up_diff1yprc_btn">1 ÅR </a>
            </div>
            <table class="site-table">
                <thead class="site-thead">
                    <tr>
                        <th>NEJ.</th>
                        <th class="order" orderby="stock_name"><span>FÖRETAGSNAMN</span><img width="10px" height="auto"></th>
                        <th class="order" orderby="scr"><span id="up-prc-label">1 DAG</span> <img width="10px" height="auto"></th>
                        <th class="order" orderby="latestprice"><span>SENAST BETALD</span><img width="10px" height="auto"></th>
                        <th class="order" orderby="numberofshares"><span>SÄGARE PÅ AWANZA</span> <img width="10px" height="auto"></th>
                        <th class="order">HANDLING</th>
                    </tr>
                </thead>
                <tbody id="up-stocks-tbody" class="site-tbody">
                </tbody>
            </table>
            <div style="display:flex; justify-content: center;"><a id="up-view-btn" class="view-btn">+ Visa fler </a></div>
        </div>
    </div>
<?php

    return ob_get_clean();
}
add_shortcode('stocks_table', 'stocks_table_shortcode');

function down_stocks_table_shortcode($atts)
{
    ob_start();

    $atts = shortcode_atts(array(
        'per_page' => 2,
    ), $atts);

?>
    <div class="stocks-row" style="margin-top: 64px;">
        <div class="stocks-col-left">
            <div class="stocks-title">LÅGPRESTERANDE LISTA ÖVER SVENSKA AKTIER</div>
            <div class="stocks-content">Listan nedan visar de aktier där minst antal affärsnoter skapas i förhållande till hur många som handlat aktien det senaste året.</div>
            <div style="display:flex; justify-content: space-between;">
                <div id="down_diff1dprc"><span>1 dag </span> <img width="20" /><span></span></div>
                <div span id="down_diff3mprc"><span>3 månader</span><img width="20" /><span></span></div>
                <div id="down_diffqtdprc"><span>fjärdedel</span><img width="20" /><span></span></div>
                <div id="down_diff1yprc"><span>3 år</span><img width="20" /><span></span></div>
            </div>
        </div>
        <div class="stocks-col-right">
            <div class="stocks-rising-img">
                <img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/down.png" />
            </div>
            <div class="search" method="post" action="">
                <div class="search-img"><img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/search.png" /></div>
                <input type="text" class="search-input" id="down-search-input" value="" placeholder="Sök efter alla webbplatsaktier">
            </div>
        </div>
    </div>
    <div>
        <div id="site-table-container">
            <div class="prc-btn-group" id="down_prc_btns">
                <a id="down_diff1dprc_btn">1 DAG </a>
                <a id="down_diff3mprc_btn">3 MÅNADER </a>
                <a id="down_diffqtdprc_btn">FJÄRDEDEL </a>
                <a id="down_diff1yprc_btn">1 ÅR </a>
            </div>
            <table class="site-table">
                <thead class="site-thead">
                    <tr>
                        <th>NEJ.</th>
                        <th class="order" orderby="stock_name"><span>FÖRETAGSNAMN</span><img width="10px" height="auto"></th>
                        <th class="order" orderby="scr"><span id="down-prc-label">1 DAG</span> <img width="10px" height="auto"></th>
                        <th class="order" orderby="latestprice"><span>SENAST BETALD</span><img width="10px" height="auto"></th>
                        <th class="order" orderby="numberofshares"><span>SÄGARE PÅ AWANZA</span> <img width="10px" height="auto"></th>
                        <th class="order">HANDLING</th>
                    </tr>
                </thead>
                <tbody id="down-stocks-tbody" class="site-tbody">
                </tbody>
            </table>
            <div style="display:flex; justify-content: center;"><a id="down-view-btn" class="view-btn down">+ Visa fler </a></div>
        </div>
    </div>
<?php

    return ob_get_clean();
}
add_shortcode('down_stocks_table', 'down_stocks_table_shortcode');

// function get_stocks($paged = 1, $per_page = 10, $search_term = '')
// {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'stocks';

//     $query = "SELECT * FROM $table_name";

//     if (!empty($search_term)) {
//         $query .= $wpdb->prepare(" WHERE company_name LIKE '%%%s%%' OR latest_pay LIKE '%%%s%%' OR agare LIKE '%%%s%%'", $search_term, $search_term, $search_term);
//     }

//     // $query .= $wpdb->prepare(" LIMIT %d, %d", $offset, $per_page);

//     return $wpdb->get_results($query);
// }


// function stocks_ajax_search()
// {
//     $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';

//     $paged = max(1, isset($_POST['paged']) ? intval($_POST['paged']) : 1);
//     $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 2;

//     $stocks = get_stocks($paged, $per_page, $search_term);

//     $response = array(
//         'stocks' => $stocks,
//     );
//     if ($stocks) {
//         wp_send_json_success($response);
//     } else {
//         wp_send_json_error('No stocks found.');
//     }

//     // Make sure to exit after sending the JSON response
//     exit;
// }
// add_action('wp_ajax_stocks_ajax_search', 'stocks_ajax_search');
// add_action('wp_ajax_nopriv_stocks_ajax_search', 'stocks_ajax_search');

//stock detail page

function stock_summary_shortcode()
{
    ob_start();
    $insref = isset($_GET['insref']) ? sanitize_text_field($_GET['insref']) : '';
?>
    <div class="stock-summary">
        <div class="stock-summary-left" id="stock-img">
        </div>
        <div class="stock-summary-right">
            <div style="display:flex; gap: 14px">
                <p id="stock-name"></p>
                <div id="stock-status-img"></div>
            </div>
            <div style="display: flex; gap: 24px; align-items:end; margin-top: 23px;">
                <div id="last-price"></div>
                <p class="diff"><span id="diff"></span> - <span id="diffprc"></span></p>
            </div>
            <p id="latest-report"></p>
        </div>
    </div>
    <script>
        const insref = '<?php echo $insref; ?>';
        var today = new Date()
        today.setDate(today.getDate() - 1)
        var chartType = 'spline';
        var checkedInsrefs = [];
        var defaultInsref = insref;
        var startDate = getStartDate('diff1dprc');
        var compress = 1;
        var cmd = 'trades';
        var isTimeFormat = true;

        function getStartDate(prcVal) {
            switch (prcVal) {
                case 'diff1dprc':
                    return formatDate(today);
                    break;
                case 'diff1wprc':
                    return getDateNDaysAgo(today, 7)
                    break;
                case 'diff1mprc':
                    return getDateNMonthsAgo(today, 1)
                    break;
                case 'diff3mprc':
                    return getDateNMonthsAgo(today, 3)
                    break;
                case 'diff6mprc':
                    return getDateNMonthsAgo(today, 6)
                    break;
                case 'diff1yprc':
                    return getDateNYearsAgo(today, 1)
                    break;
                case 'diff3yprc':
                    return getDateNYearsAgo(today, 3)
                    break;
                case 'diff5yprc':
                    return getDateNYearsAgo(today, 5)
                    break;
                default:
                    break;
            }
        }

        function formatDate(date) {
            const yyyy = date.getFullYear().toString();
            const mm = (date.getMonth() + 1).toString().padStart(2, '0');
            const dd = date.getDate().toString().padStart(2, '0');
            return yyyy + '-' + mm + '-' + dd;
        }

        function getDateNDaysAgo(date, days) {
            var result = new Date(date);
            result.setDate(result.getDate() - days);

            return formatDate(result);
        }

        function getDateNMonthsAgo(date, months) {
            var result = new Date(date);
            result.setMonth(result.getMonth() - months);

            return formatDate(result);
        }

        function getDateNYearsAgo(date, years) {
            var result = new Date(date);
            result.setFullYear(result.getFullYear() - years);

            return formatDate(result);
        }

        function filterDataForSelectedDay(data) {
            return data.filter(item => new Date(item.date + 'T' + item.time).getDay() === today.getDay());
        }

        function filterDataForSelectedMonthDay(data) {
            return data.filter(item => new Date(item.date + 'T' + item.time).getDate() === today.getDate());
        }

        function formatDateString(dateString) {
            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const date = new Date(dateString);
            const month = months[date.getMonth()];
            const day = date.getDate();

            return month + " " + day;
        }

        function formatTimeString(timeString) {
            var date = new Date(timeString);
            var hours = date.getHours();
            var minutes = date.getMinutes();

            hours = (hours < 10) ? "0" + hours : hours;
            minutes = (minutes < 10) ? "0" + minutes : minutes;

            return hours + ":" + minutes;
        }

        function formatDateTime(dateTime) {
            const options = {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            };
            return new Date(dateTime).toLocaleString('en-US', options);
        }

        function updateChartData() {
            refreshChart(checkedInsrefs, defaultInsref);
        }

        function refreshChart(checkedInsrefs, defaultInsref) {
            $("#stock-chart").empty();

            let ajaxPromises = [];

            let defaultPromise = $.ajax({
                type: 'GET',
                url: 'https://test.millistream.com/mws.fcgi',
                data: {
                    usr: 'aktierse',
                    pwd: 'z6k1e3kZ1qzrOV6Y0QcpsqXTlbydAmX9JyNkvdPr',
                    cmd: cmd,
                    instrumenttype: 4,
                    fields: 'insref,date,time,tradeprice,openprice,highprice,lowprice,closeprice1d,name,closedayhighprice,closedaylowprice,closeprice',
                    insref: defaultInsref,
                    compress: compress,
                    startdate: startDate,
                    enddate: formatDate(today)
                }
            });

            ajaxPromises.push(defaultPromise);


            checkedInsrefs.forEach(function(insref) {

                let promise = $.ajax({
                    type: 'GET',
                    url: 'https://test.millistream.com/mws.fcgi',
                    data: {
                        usr: 'aktierse',
                        pwd: 'z6k1e3kZ1qzrOV6Y0QcpsqXTlbydAmX9JyNkvdPr',
                        cmd: cmd,
                        instrumenttype: 4,
                        fields: 'insref,date,time,tradeprice,openprice,highprice,lowprice,closeprice1d,name,closedayhighprice,closedaylowprice,closeprice',
                        insref: insref,
                        compress: compress,
                        startdate: startDate,
                        enddate: formatDate(today)
                    }
                });

                ajaxPromises.push(promise);
            });

            $.when.apply($, ajaxPromises).done(function() {
                let aggregatedData = [];
                for (let i = 0; i < arguments.length; i++) {

                    const responseData = arguments[i][0];

                    if (typeof responseData === 'object' && !Array.isArray(responseData)) {
                        aggregatedData.push(responseData);
                    } else if (Array.isArray(responseData) && responseData.length > 0) {
                        aggregatedData.push(responseData[0]);
                    }
                }
                renderChart(aggregatedData);
            });
        }

        function renderChart(data) {

            let datasets = [];

            if (data.find(item => item.insref == defaultInsref)) {
                const defaultData = data.find(item => item.insref == defaultInsref);
                const defaultTrade = cmd == 'trades' ? defaultData.trade : defaultData.history;
                const defaultReferenceY = cmd == 'trades' ? parseFloat(defaultTrade[0].tradeprice) : parseFloat(defaultTrade[0].openprice);
                let dataPoints = [];
                let defaultDataPoints = [];
                let candlestickDataPoints = [];
                let filteredData = [];
                if (compress == 245) {
                    filteredData = filterDataForSelectedDay(defaultTrade);
                } else if (compress == 530) {
                    filteredData = filterDataForSelectedMonthDay(defaultTrade);
                } else {
                    filteredData = defaultTrade;
                }
                filteredData.forEach(function(tradeItem, i) {
                    const dateTimeString = tradeItem.date + 'T' + tradeItem.time;
                    const dateTime = new Date(dateTimeString);
                    const percentage = cmd == 'trades' ? ((parseFloat(tradeItem.tradeprice) - defaultReferenceY) / defaultReferenceY) * 100 :
                        ((parseFloat(tradeItem.openprice) - defaultReferenceY) / defaultReferenceY) * 100;
                    defaultDataPoints.push({
                        x: i,
                        y: cmd == 'trades' ? tradeItem.tradeprice : tradeItem.openprice,
                        percentage: percentage,
                        x_label: dateTime,
                        markerSize: 0,
                    });
                    candlestickDataPoints.push({
                        x: i,
                        y: cmd == 'trades' ? [tradeItem.openprice, tradeItem.highprice, tradeItem.lowprice, tradeItem.tradeprice] : [tradeItem.openprice, tradeItem.closedayhighprice, tradeItem.closedaylowprice, tradeItem.closeprice],
                        x_label: dateTime
                    });
                });

                switch (chartType) {
                    case 'line':
                    case 'spline':
                        dataPoints = defaultDataPoints
                        break;
                    case 'candlestick':
                    case 'ohlc':
                        dataPoints = candlestickDataPoints;
                        break;
                    default:
                        break;
                }

                datasets.push({
                    type: chartType,
                    cursor: 'pointer',
                    axisYType: "secondary",
                    indexLabelFontSize: 16,
                    lineColor: '#33B864',
                    color: '#33B864',
                    lineThickness: 1,
                    dataPoints: dataPoints,
                    name: defaultData.name
                });
            }
            checkedInsrefs.forEach(function(checkedInsref) {
                const checkedData = data.find(item => item.insref == checkedInsref);
                if (checkedData) {
                    const checkedTrade = cmd == 'trades' ? checkedData.trade : checkedData.history;
                    const checkedReferenceY = cmd == 'trades' ? parseFloat(checkedTrade[0].tradeprice) : parseFloat(checkedTrade[0].openprice);
                    let checkedDataPoints = [];
                    let filteredData;
                    if (compress == 245) {
                        filteredData = filterDataForSelectedDay(checkedTrade);
                    } else if (compress == 530) {
                        filteredData = filterDataForSelectedMonthDay(checkedTrade);
                    } else {
                        filteredData = checkedTrade;
                    }
                    filteredData.forEach(function(tradeItem, i) {
                        const percentage = cmd == 'trades' ? ((parseFloat(tradeItem.tradeprice) - checkedReferenceY) / checkedReferenceY) * 100 :
                            ((parseFloat(tradeItem.openprice) - checkedReferenceY) / checkedReferenceY) * 100;
                        checkedDataPoints.push({
                            x: i,
                            y: percentage,
                            percentage: percentage,
                            markerSize: 0,
                        });
                    });

                    datasets.push({
                        type: 'spline',
                        cursor: 'pointer',
                        axisYType: "primary",
                        lineThickness: 1,
                        indexLabelFontSize: 16,
                        lineColor: '#ff78bc',
                        color: '#ff78bc',
                        dataPoints: checkedDataPoints,
                        name: checkedData.name
                    });
                }
            });

            const interval = (datasets[0].dataPoints) ? Math.ceil(datasets[0].dataPoints.length / 10) : 1;
            var chartOptions = {
                animationEnabled: true,
                theme: "light2",
                axisX: {
                    labelFontColor: "#000000",
                    labelFontFamily: 'Satoshi',
                    labelFontSize: 11,
                    labelFontWeight: 400,
                    tickLength: 10,
                    labelFormatter: function(e) {
                        var dataPoint = e.chart.data[0].dataPoints.find(dataPoint => dataPoint.x === e.value);
                        if (dataPoint && dataPoint.x_label) {
                            return isTimeFormat ? formatTimeString(dataPoint.x_label) : formatDateString(dataPoint.x_label)
                        }
                    },
                    interval: interval,
                },
                axisY: {
                    labelFontSize: 0,
                    gridThickness: 0,
                    tickLength: 0,
                },
                axisY2: {
                    gridThickness: 0,
                    labelFontColor: "#000000",
                    labelFontFamily: 'Satoshi',
                    labelFontSize: 11,
                    labelFontWeight: 400,
                    includeZero: false,
                    gridThickness: 0,
                    tickLength: 0,
                    labelPlacement: "inside",
                },
                height: 450,
                data: [],
                toolTip: {
                    shared: true,
                    contentFormatter: function(e) {
                        let tooltipContent;
                        e.entries.forEach(function(entry, i) {
                            tooltipContent = `<small> ${formatDateTime(entry.dataPoint.x_label)}</small> <br/>`;
                            const type = entry.dataSeries.options.type;
                            const colorSpot = `<span style='display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: ${entry.dataSeries.lineColor}; margin-right: 5px;'></span>`;
                            if (type == "candlestick" || type == 'ohlc') {
                                tooltipContent += `O: ${entry.dataPoint.y[0]}<br/>`;
                                tooltipContent += `H: ${entry.dataPoint.y[1]}<br/>`;
                                tooltipContent += `L: ${entry.dataPoint.y[2]}<br/>`;
                                tooltipContent += `C: ${entry.dataPoint.y[3]}<br/>`;
                            } else {
                                tooltipContent += i ? `${colorSpot} ${entry.dataSeries.name} ${entry.dataPoint.percentage.toFixed(2)}%<br/>` :
                                    `${colorSpot} ${entry.dataSeries.name} ${entry.dataPoint.y} (${entry.dataPoint.percentage.toFixed(2)}%)<br/>`;
                            }
                        });
                        return tooltipContent;
                    }
                },
            };
            chartOptions.data = datasets;
            var chart = new CanvasJS.Chart("stock-chart", chartOptions);
            chart.render();
        }



        jQuery(document).ready(function($) {

            updateChartData();

            $('.analysis-item').first().addClass('active');

            $('.cc-item-input').change(function() {
                checkedInsrefs = [];
                $('.cc-item-input:checked').each(function() {
                    checkedInsrefs.push(6485);
                });
                refreshChart(checkedInsrefs, defaultInsref);

            });

            $.ajax({
                type: 'GET',
                url: 'https://mws-2.millistream.com/mws.fcgi',
                data: {
                    usr: 'aktierse',
                    pwd: 'z6k1e3kZ1qzrOV6Y0QcpsqXTlbydAmX9JyNkvdPr',
                    cmd: 'quote',
                    instrumenttype: 4,
                    fields: 'insref,symbol,name,funcompanyname,lastprice,latestreport,country,diff1dprc,diff1d,diff1wprc,diff1mprc,diff3mprc,diff6mprc,diffytdprc,diff1yprc,diff3yprc,diff5yprc,fundcompany,ceo,chairman,company,description,numberofshares,capitalprc,gm,om,mcap,roe,per,isin,beta,roe_last,om_ttm,om_last,gm_ttm,roe_ttm,turnoverex,pm_ttm,roa_ttm,roa,roa_last,turnover,averageturnoverytd,internalturnover,adtvytdprc,adtv1mprc,adtv1wprc,adtv1yprc,adtv3mprc,basecurrency,cfps_ttm,contractsize,couponrate,eps,dps,eps_ttm,eusipa,macd,maxlevel,pcf_ttm,pbr,psr_ttm,tis,vwap,alfa,basecurrency,bidpriceex,boardlot,bvps_last,cfps_last,cfps_ttm,closeyieldytd,closeyieldqtd,diffldprc,diffmtdprc,diffpytdprc,eps,eps_ttm,eps_last,gm_last,isin,morningstarrating,nav,nextreport,numtradesex,numtrades,nominalvalue,pbr_last,pcf,pcf_last,per,per_ttm,ttm,psr_ttm,psr,roe_last,roa_last,sma20,standarddeviation3y,ucits,volatility,per_ttm,psr_ttm,isin,address,companylatestyearendreport',
                    insref: insref
                },
                success: function(response) {
                    var stock = response[0];
                    $('#stock-img').append('<img src="https://flagcdn.com/180x135/' + stock.country.toLowerCase() + '.png">')
                    $('#stock-name').html(stock.name);
                    if (stock.diff1d > 0)
                        $('#stock-status-img').append('<img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/rising.png"/>');
                    else
                        $('#stock-status-img').append('<img src="http://localhost/aktier/wp-content/themes/divi-child/uploads/down.png"/>');
                    $('#last-price').html(stock.lastprice);
                    $('#diff').html(stock.diff1d);
                    $('#diffprc').html(stock.diff1dprc);
                    $('#latest-report').html('Senaste uppdatering 2023 30 december, 02:57 UTC+2');
                    var prcs = ['diff1dprc', 'diff1wprc', 'diff1mprc', 'diff3mprc', 'diff6mprc', 'diff1yprc', 'diff3yprc', 'diff5yprc'];

                    prcs.forEach(prc => {
                        if (stock[prc] > 0) $(`#analysis-${prc}`).addClass('up');
                        else $(`#analysis-${prc}`).addClass('down');
                        $(`#analysis-${prc}`).html(stock[prc]);
                    });

                    $('#company-description').html(stock.description);
                    $('#seo').html(stock.ceo);
                    $('#chairman').html(stock.chairman)
                    $('#numberofshares').html(stock.numberofshares);
                    $('#roe_ttm').html(stock.roe_ttm);
                    $('#gm_ttm').html(stock.gm_ttm);
                    $('#symbol').html(stock.symbol);
                    $('#isin').html(stock.isin);
                    $('#psr_ttm').html(stock.psr_ttm);
                    $('#beta').html(stock.beta);
                    $('#per_ttm').html(stock.per_ttm);
                    $('#address').html(stock.address);
                    $('#mcap').html(stock.mcap);
                    $('#latestreport').html(stock.latestreport);
                    $('.active.post-page').attr('title', stock.name);
                    $('.active.post-page span').text(stock.name.toUpperCase());
                    $('#cur-stock').text(stock.name);

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            $.ajax({
                type: 'GET',
                url: 'https://mws-2.millistream.com/mws.fcgi',
                data: {
                    usr: 'aktierse',
                    pwd: 'z6k1e3kZ1qzrOV6Y0QcpsqXTlbydAmX9JyNkvdPr',
                    cmd: '',
                    instrumenttype: 4,
                    fields: 'name,owner,capitalprc,votingpowerprc',
                    insref: insref,
                    limit: 10,
                    orderby: 'capitalprc',
                    order: 'desc'
                },
                success: function(response) {
                    var owners = response[0].ownerdata;
                    owners.forEach(owner => {
                        $('#owners-tbody').append(`<tr><td>${owner.owner}</td><td>${owner.capitalprc} %</td><td>${owner.votingpowerprc} %</td><tr>`)
                    })

                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });


            $('#cs-close-btn').click(function() {
                $('.is-opened').removeClass('is-opened');
            })
            $('#cs-btn').click(function(e) {
                e.preventDefault();
                $('.is-opened').removeClass('is-opened');
                $('#cs-btn').toggleClass('is-opened');
                $('#cs-sidebar').toggleClass('is-opened');
                $('#cs-sidebar').addClass('has-transition');

            })
            $('#cc-btn').click(function(e) {
                e.preventDefault();
                $('.is-opened').removeClass('is-opened');
                $('#cc-btn').toggleClass('is-opened');
                $('#cc-sidebar').toggleClass('is-opened');
                $('#cc-sidebar').addClass('has-transition');

            })
            $('#cc-close-btn').click(function() {
                $('.is-opened').removeClass('is-opened');
            })


            $('input[name="cs"]').on('click', function() {
                chartType = $(this).attr('id');
                refreshChart(checkedInsrefs, defaultInsref);
            });


            $('.analysis-item').on('click', function() {
                $('.analysis-item').removeClass('active');
                $(this).addClass('active');

                const prcVal = $('.analysis-item.active > p:nth-child(2)').attr('id').split('-')[1];
                startDate = getStartDate(prcVal);

                var intervalSelect = $('#graph-select');
                intervalSelect.empty();

                switch (prcVal) {
                    case 'diff1dprc':
                        addOptions([{
                            text: '1min',
                            value: 1
                        }, {
                            text: '2 min',
                            value: 2
                        }, {
                            text: '5 min',
                            value: 5
                        }, {
                            text: '10 min',
                            value: 10
                        }, {
                            text: '30 min',
                            value: 30
                        }, {
                            text: '1h',
                            value: 60
                        }, ]);
                        cmd = 'trades';
                        isTimeFormat = true;
                        compress = 1;
                        break;
                    case 'diff1wprc':
                        addOptions([{
                            text: '10 min',
                            value: 10
                        }, {
                            text: '30 min',
                            value: 30
                        }, {
                            text: '1h',
                            value: 60
                        }]);
                        cmd = 'trades';
                        isTimeFormat = false;
                        compress = 10;
                        break;
                    case 'diff1mprc':
                        addOptions([{
                            text: '1h',
                            value: 60
                        }, {
                            text: 'Dag',
                            value: 60
                        }, {
                            text: 'Vecka',
                            value: 60
                        }]);
                        cmd = 'trades';
                        isTimeFormat = false;
                        compress = 60;
                        break;
                    case 'diff3mprc':
                    case 'diff6mprc':
                    case 'diff1yprc':
                    case 'diff3yprc':
                    case 'diff5yprc':
                        addOptions([{
                            text: 'Dag',
                            value: 6024
                        }, {
                            text: 'Vecka',
                            value: 245
                        }, {
                            text: 'Månad',
                            value: 530
                        }]);
                        cmd = 'history';
                        isTimeFormat = false;
                        compress = 6024;
                        break;
                    default:
                        break;
                }

                refreshChart(checkedInsrefs, defaultInsref);
            });

            function addOptions(options) {
                console.trace(options, 'options')
                var intervalSelect = $('#graph-select');
                $.each(options, function(index, option) {
                    intervalSelect.append($('<option>', {
                        value: option.value,
                        text: option.text
                    }));
                });
            }

            $('#graph-select').on('change', function() {
                compress = $(this).val();
                refreshChart(checkedInsrefs, defaultInsref);
            })

        });
    </script>
<?php
    return ob_get_clean();
}

add_shortcode('stock_summary', 'stock_summary_shortcode');

function stock_analysis_shortcode()
{
    ob_start();
?>
    <div class="stock-analysis">
        <div class="analysis-item">
            <p class="analysis-label">1 dag</p>
            <p class="analysis-content" id="analysis-diff1dprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">1 vecka</p>
            <p class="analysis-content" id="analysis-diff1wprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">1 månad</p>
            <p class="analysis-content" id="analysis-diff1mprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">3 månader</p>
            <p class="analysis-content" id="analysis-diff3mprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">6 månader</p>
            <p class="analysis-content" id="analysis-diff6mprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">1 år</p>
            <p class="analysis-content" id="analysis-diff1yprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">3 år</p>
            <p class="analysis-content" id="analysis-diff3yprc"></p>
        </div>
        <div class="analysis-item">
            <p class="analysis-label">5 år</p>
            <p class="analysis-content" id="analysis-diff5yprc"></p>
        </div>
    </div>


<?php
    return ob_get_clean();
}

add_shortcode('stock_analysis', 'stock_analysis_shortcode');

function company_info_shortcode()
{
    ob_start();
?>
    <p id="company-description"></p>
    <div class="company-info-container">
        <div class="info-col-l">
            <div class="company-info"><span class="info-label">SEO</span><span class="info-content" id="seo"></span></div>
            <div class="company-info"><span class="info-label">President</span><span class="info-content" id="chairman"></span></div>
            <div class="company-info"><span class="info-label">Antal delningar</span><span id="#mcap" class="info-content">-</span></div>
            <div class="company-info"><span class="info-label">Hållfasthet</span><span class="info-content" id="numberofshares"></span></div>
            <div class="company-info"><span class="info-label">Räntetäckningsgrad</span><span class="info-content">-</span></div>
        </div>
        <div class="info-col-r">
            <div class="company-info"><span class="info-label">Avkastning på eget kapital</span><span class="info-content" id="roe_ttm"></span></div>
            <div class="company-info"><span class="info-label">Avkastning på totalt kapital</span><span class="info-content">-</span></div>
            <div class="company-info"><span class="info-label">Bruttomarginal</span><span class="info-content" id="gm_ttm"></span></div>
            <div class="company-info"><span class="info-label">Rörelsemarginal</span><span class="info-content">-</span></div>
            <div class="company-info"><span class="info-label">Kapitalomsättningshastighet</span><span class="info-content">-</span></div>
        </div>
        <div>
        <?php
        return ob_get_clean();
    }

    add_shortcode('company_info', 'company_info_shortcode');


    function stock_info_shortcode()
    {
        ob_start();
        ?>
            <p id="stock-description"></p>
            <div class="stock-info-container">
                <div class="info-col-l">
                    <div class="company-info"><span class="info-label">Marknadsplats</span><span class="info-content" id="address">-</span></div>
                    <div class="company-info"><span class="info-label">Kortnamn</span><span class="info-content" id="symbol">-</span></div>
                    <div class="company-info"><span class="info-label">Ägare hos Avanza</span class="info-content"><span id="soa">-</span></div>
                    <div class="company-info"><span class="info-label">Antal aktier</span><span class="info-content" id="numberofshares"></span></div>
                    <div class="company-info"><span class="info-label">Beta</span><span class="info-content" id="beta">-</span></div>
                </div>
                <div class="info-col-r">
                    <div class="company-info"><span class="info-label">ISIN</span><span class="info-content" id="isin"></span></div>
                    <div class="company-info"><span class="info-label">P/E-tal</span><span class="info-content" id="per_ttm">-</span></div>
                    <div class="company-info"><span class="info-label">P/S-tal</span><span class="info-content" id="psr_ttm"></span></div>
                    <div class="company-info"><span class="info-label">Eget kapital/aktie</span><span class="info-content">-</span></div>
                    <div class="company-info"><span class="info-label">Rapportdatum</span><span id="latestreport" class="info-content"></span></div>
                </div>
                <div>
                <?php
                return ob_get_clean();
            }

            add_shortcode('stock_info', 'stock_info_shortcode');

            function owner_list_shortcode()
            {
                ob_start();
                ?>
                    <div id="site-table-container">
                        <table class="site-table">
                            <thead class="site-thead">
                                <tr>
                                    <th>10 STÖRSTA ÄGARE</th>
                                    <th>HUVUDSTAD</th>
                                    <th>RÖSTER </th>
                                </tr>
                            </thead>
                            <tbody id="owners-tbody" class="site-tbody">
                            </tbody>
                        </table>
                    </div>
                <?php
                return ob_get_clean();
            }

            add_shortcode('owner_list', 'owner_list_shortcode');



            // Add AJAX action for retrieving stocks
            add_action('wp_ajax_get_stocks', 'get_stocks');
            add_action('wp_ajax_nopriv_get_stocks', 'get_stocks'); // Allow non-logged in users to access the function

            function get_stocks()
            {
                global $wpdb;

                $table_name = $wpdb->prefix . 'stocks';

                // Pagination parameters
                $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
                $perPage = isset($_POST['perPage']) ? intval($_POST['perPage']) : 15;
                $offset = ($paged - 1) * $perPage;

                // Sorting parameters
                $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'id';
                $order = isset($_POST['order']) && in_array(strtoupper($_POST['order']), array('ASC', 'DESC')) ? strtoupper($_POST['order']) : 'ASC';

                // Search parameter
                $searchInput = isset($_POST['searchInput']) ? sanitize_text_field($_POST['searchInput']) : '';

                // Base query
                $query = "SELECT * FROM $table_name WHERE $orderby is not null";

                // Apply search filter
                if (!empty($searchInput)) {
                    $query .= " AND stock_name LIKE '%$searchInput%'";
                }

                // Apply sorting
                $query .= " ORDER BY $orderby $order";

                // Apply pagination
                $query .= " LIMIT $perPage OFFSET $offset";

                $stocks = $wpdb->get_results($query);

                if ($stocks) {
                    wp_send_json_success($stocks); // Send JSON response with the stocks data
                } else {
                    wp_send_json_error('No stocks found.');
                }

                wp_die(); // Always include this at the end of your AJAX functions
            }

            // function my_hourly_callback_function()
            // {
            //     global $wpdb;
            //     $table_realtime_discords = $wpdb->prefix . 'realtime_discords';
            //     $initial_response = wp_remote_get('https://discord.com/api/v9/invites/tQp4pSE?with_counts=true&with_expiration=true');

            //     $headers = wp_remote_retrieve_headers($initial_response);
            //     $cookie_headers = $headers['set-cookie'];
            //     $cookie = '';
            //     foreach ($cookie_headers as $cookie_header) {
            //         $cookie .= explode(';', $cookie_header)[0] . '; ';
            //     }

            //     $headers = array(
            //         'Cookie' => $cookie
            //     );

            //     $request_counter = 0;
            //     $table_discords = $wpdb->prefix . 'discords';
            //     $discords = $wpdb->get_results("SELECT * FROM $table_discords");

            //     $count = 0;
            //     // echo(json_encode("hey"));
            //     echo ("hey");

            //     foreach ($discords as $discord) {
            //         preg_match('/discord\.gg\/(\w+)/', $discord->url, $matches);
            //         echo($count);
            //         $id = $matches[1];

            //         echo($id);
            //         $response = wp_remote_get('https://discord.com/api/v9/invites/' . $id . '?with_counts=true&with_expiration=true', array(
            //             'timeout' => 30,
            //             'headers' => $headers
            //         ));


            //         if (is_wp_error($response)) {
            //             // Handle WP Error
            //             $error_message = $response->get_error_message();
            //             error_log("Failed to fetch data from Discord API: $error_message");
            //             continue; // Skip to the next iteration
            //         }

            //         $response_code = wp_remote_retrieve_response_code($response);
            //         if ($response_code == 200) {
            //             $body = wp_remote_retrieve_body($response);
            //             $discord_data = json_decode($body);

            //             // Check if the discord_id already exists in the table
            //             $existing_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_realtime_discords WHERE discord_id = %s", $id));

            //             if ($existing_row) {
            //                 $wpdb->update(
            //                     $table_realtime_discords,
            //                     array(
            //                         'name' => $discord->name,
            //                         'member_count' => $discord_data->approximate_member_count,
            //                         'presence_count' => $discord_data->approximate_presence_count,
            //                         'img_src' => $discord->img_src,
            //                         'url' => $discord->url,
            //                         'timestamp' => current_time('mysql')
            //                     ),
            //                     array('discord_id' => $id)
            //                 );
            //             } else {
            //                 $wpdb->insert(
            //                     $table_realtime_discords,
            //                     array(
            //                         'discord_id' => $id,
            //                         'name' => $discord->name,
            //                         'member_count' => $discord_data->approximate_member_count,
            //                         'presence_count' => $discord_data->approximate_presence_count,
            //                         'img_src' => $discord->img_src,
            //                         'url' => $discord->url,
            //                         'timestamp' => current_time('mysql')
            //                     )
            //                 );
            //             }
            //         } else {
            //             $error_message = "HTTP request error: Unexpected response code $response_code";
            //             error_log($error_message);
            //         }
            //         $count ++;
            //         sleep(10);
            //     }
            // }

            // function my_schedule_event() {
            //     if ( ! wp_next_scheduled( 'my_custom_event_hook' ) ) {
            //         wp_schedule_event( time(), 'minute', 'my_custom_event_hook' );
            //     }
            // }
            // add_action( 'wp', 'my_schedule_event' );
            
            // add_action( 'my_custom_event_hook', 'my_hourly_callback_function' );
