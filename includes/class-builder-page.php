<?php
namespace WP_Opio_Reviews\Includes;

class Builder_Page {


    public function __construct(Feed_Deserializer $feed_deserializer) {
        // error_reporting(0);
        $this->feed_deserializer = $feed_deserializer;
        error_reporting(E_ERROR | E_PARSE);
    }

    public function register() {
        add_action('opio_admin_page_opio-builder', array($this, 'init'));
    }

    public function init() {
        // register api endpoints
        if (isset($_GET['opio_notice'])) {
            $this->add_admin_notice();
        }

        $feed = null;
        if (isset($_GET[Post_Types::FEED_POST_TYPE . '_id'])) {
            $feed = $this->feed_deserializer->get_feed(sanitize_text_field(wp_unslash($_GET[Post_Types::FEED_POST_TYPE . '_id'])));
        }
        // var_dump('$feed value here');
        // var_dump($feed);
        $this->render($feed);
    }

    public function add_admin_notice($notice_code = 0) {

    }

    public function render($feed) {
        global $wp_version;
        if (version_compare($wp_version, '3.5', '>=')) {
            wp_enqueue_media();
        }
        $review_option = '';
        $review_type = '';
        $biz_id = '';
        $feed_id = '';
        $loaded_bizs = "{}";
        $business_name = '';
        $org_id = '';
        $feed_inited = false;
        $businesses = null;
        $feed_object = '';
        if ($feed != null) {
            $feed_object = json_decode($feed->post_content);
            // dd($feed_object);
            $feed_id = $feed->ID;
            $loaded_bizs_raw = $feed_object->loaded_bizs;
            $loaded_bizs = json_encode($feed_object->loaded_bizs);
            $business_name = $feed->post_title;
            $biz_id = ($feed_object->biz_id);
            $org_id = $feed_object->org_id;
            $biz_org_id = $feed_object->biz_org_id;
            $review_type = $feed_object->review_type;
            $review_option = $feed_object->review_option;
            if(isset($biz_org_id)  && $feed_object->review_type == "multiple") {
                // dd("coming here");
                $biz_id = $biz_org_id;
            }
            else {
                $biz_id = $feed_object->biz_id;
            }
            // dd($biz_id);
            if (isset($biz_id)) {
                $valid = preg_match('/^[a-zA-Z0-9]{15,20}$/', $biz_id);
                $valid = $valid || preg_match('/^[a-zA-Z0-9]{15,20}$/', $org_id);
                if ($valid) {
                    $feed_inited = true;
                }
            }
            $home_url = home_url(sanitize_url($_SERVER['REQUEST_URI']));
        }

        ?>
        <div class="opio-builder">
            <form id="opio-builder-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Post_Types::FEED_POST_TYPE . '_save')); ?>">
                <?php wp_nonce_field(Post_Types::FEED_POST_TYPE . '_save', Post_Types::FEED_POST_TYPE . '_nonce'); ?>
                <input type="hidden" id="opio_post_id" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[post_id]" value="<?php echo esc_attr($feed_id); ?>">
                <input type="hidden" id="opio_current_url" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE);?>[current_url]" value="<?php echo esc_url($home_url); ?>">
                <div class="opio-builder-workspace">
                    <div class="opio-toolbar">
                        <div class="opio-toolbar-title">
                            <input id="opio_title" class="opio-toolbar-title-input" type="text" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[title]" value="<?php if (isset($business_name)) { echo esc_attr($business_name); } ?>" placeholder="Enter a widget name" maxlength="255" autofocus>
                        </div>
                       
                    </div> 
                    <div class="opio-builder-preview" style="background-color:white">
                        <textarea id="opio-builder-connection" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[content]" style="display:none"><?php echo esc_textarea($biz_id); ?></textarea>
                        <div id="opio_collection_preview">
                        <?php
                            // dd($feed_object);
                            $review_enabled = $feed_object->review_enabled == "yes";
                            if(!$review_enabled) {
                                echo wp_kses("<div class='text-center'>Review Feed is Disabled</div>", $this->feed_deserializer->get_allowed_tags());
                            }
                            else {
                                $option = $review_option == "opio" ? "reviewFeed" : "allReviewFeed";
                                $opio_handler = new Opio_Handler($biz_id, $option, $review_type, $org_id);
                                $reviews = $opio_handler->get_business();
                                if($option == "allReviewFeeds" && $review_type == "singles") {
                                    $reviews = $this->feed_deserializer->prepareString($reviews);
                                    echo wp_kses($reviews, $this->feed_deserializer->get_allowed_tags());
                                }
                                else {
                                    echo $reviews;
                                }
                            }
                        // dd($biz_id);
                        ?>
                        </div>
                    </div>
                </div>
                <div id="opio-builder-option" class="opio-builder-options">
                    <div id="opio-loading-spinner" class="opio-loading-spinner">
                        <div class="opio-loading-spinner-inner">
                            <div class="opio-loading-spinner-icon"></div>
                        </div>
                    <div class="opio-builder-inside">
                    <div class="opio-builder-first">Review Feed Name</div>
                        <input id="widget_title" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[title]" value="<?php echo esc_attr($business_name) ?>" type="text"/>
                    </div>
                    <div class="opio-builder-inside">
                        <div class="opio-builder-first">Review Feed Select Options</div>
                        <select id="reviewTypeSelection" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[review_type]">
                            <option value="single" <?php if ($review_type == 'single') { echo esc_html('selected'); } ?>>Enter Business ID</option>
                            <option value="multiple" <?php if ($review_type == 'multiple') { echo esc_html('selected'); } ?>>Enter Organization ID</option>
                            <option value="orgfeed" <?php if ($review_type == 'orgfeed') { echo esc_html('selected'); } ?>>Organization Feed</option>
                            </select>
                    </div>
                    <div style="display:none" id="bizIDSelector" class="opio-builder-inside">
                        <div class="opio-builder-first">Business ID</div>
                        <input name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[biz_id]" value="<?php echo esc_attr($biz_id) ?>" type="text"/>
                    </div>
                    <div style="display:none" id="orgStuff">
                        <div class="opio-builder-inside">
                            <div class="opio-builder-first">Organization ID</div>
                            <input id="orgIDSelector"  name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[org_id]" value="<?php echo esc_attr($org_id) ?>" type="text"/>
                        </div>
                        <div id="orgSelectStuff">
                        <div class="opio-builder-inside">
                        <div class="text-justify">
                            <button id="orgIDButton" class="button button-primary">Load Businesses</button>
                                </div>
                    </div>
                        <input type="hidden" id="loadedBusinsses" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[loaded_bizs]" value="<?php echo esc_attr($loaded_bizs) ?>">

                        <div style="display:none" id="businessListSelector" class="opio-builder-inside">
                            <div class="opio-builder-first">Select Business</div>
                            <select id="businessSelected" name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[biz_org_id]">

                                </select>
                        </div>
                    </div>
                        
                    </div>
                    
                    <div class="opio-builder-inside">
                        <div class="opio-builder-first">Review Feed Option</div>
                        <select name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[review_option]" value="<?php echo esc_attr($review_option) ?>">
                            <option value="all" <?php if ($review_option == 'all') { echo esc_html('selected'); } ?>>All Reviews Feed</option>
                            <option value="opio" <?php if ($review_option == 'opio') { echo esc_html('selected'); } ?>>Opio Feed</option>
                            </select>
                    </div>
                 
                    <div class="opio-builder-inside">
                        <div class="opio-builder-first">Review Feed Status</div>
                        <select name="<?php echo esc_attr(Post_Types::FEED_POST_TYPE); ?>[review_enabled]">
                            <option value="yes" <?php if ($feed_object->review_enabled == 'yes') { echo esc_html('selected');} ?>>Enabled</option>
                            <option value="no" <?php if ($feed_object->review_enabled == 'no') { echo esc_html('selected'); } ?>>Disabled</option>
                            </select>
                        </div>
                            <?php if ($feed_inited) { ?>
                                <div class="opio-builder-inside">
                                <div class="opio-builder-first">Shortcode</div>

                                <input id="opio_sc" type="text" value="[opio_feed id=&quot;<?php echo esc_attr($feed_id); ?>&quot;]" data-opio-shortcode="[opio_feed id=&quot;<?php echo esc_attr($feed_id); ?>&quot;]" onclick="this.select(); document.execCommand('copy'); window.opio_sc_msg.innerHTML = 'Shortcode Copied! ';" readonly/>
                                <div class="opio-toolbar-options">
                                    <label title="Sometimes, you need to use this shortcode in PHP, for instance in header.php or footer.php files, in this case use this option"><input type="checkbox" onclick="var el = window.opio_sc; if (this.checked) { el.value = '&lt;?php echo do_shortcode( \'' + el.getAttribute('data-opio-shortcode') + '\' ); ?&gt;'; } else { el.value = el.getAttribute('data-opio-shortcode'); } el.select();document.execCommand('copy'); window.opio_sc_msg.innerHTML = 'Shortcode Copied! ';"/>Use in PHP</label>
                                </div>
                                <?php } ?>
                                <div class="opio-builder-inside">

                                <div class="text-justify">
                                    <button id="opio_save" type="submit" class="button button-primary ">Save & Update</button>
                                </div>
                                <div style="padding-top: 10px; font-size: 15px">

                                    <a style="text-decoration: none" href="/wp-admin/admin.php?page=opio-support&opio_tab=figFull&opio_tab=welcome">Full Installation Guide </a></div>
                            </div>
                            </div>
                            </div>
                        </div>
            </form>
        </div>

        <script>
            jQuery(document).ready(function($) {
                function getBusinesses(orgID) {
                    var businessSelected = document.getElementById("businessSelected");
                    // empty the select
                    businessSelected.innerHTML = "";
                    var ajaxUrl = "/wp-json/opioreviews/v1/get_businesses?orgID=" + orgID;
                    var divLoader = document.getElementById("opio-builder-option");
                    
                    // reduce the opacity of div
                    divLoader.style.opacity = 0.5;
                    // make it unclickable
                    divLoader.style.pointerEvents = "none";
                    // add spinner bootstrap class
                    divLoader.classList.add("spinner-grow");


                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            var stringified = JSON.stringify(data);
                            loadedBusinsses = document.getElementById("loadedBusinsses");
                            loadedBusinsses.value = stringified;

                            // add to the select
                            for (var i = 0; i < data.length; i++) {
                                var option = document.createElement("option");
                                option.text = data[i].name;
                                option.value = data[i]._id;
                                businessSelected.add(option);
                            }
                            var businessListSelector = document.getElementById("businessListSelector");
                            businessListSelector.style.display = "block";
                            // make it clickable
                            divLoader.style.pointerEvents = "auto";
                            // make it opaque
                            divLoader.style.opacity = 1;
                        },
                        error: function(data) {
                            console.log(data);
                            // make it clickable
                            divLoader.style.pointerEvents = "auto";
                            // make it opaque
                            divLoader.style.opacity = 1;

                        }
                    });
            }
            var widget_title = document.getElementById("widget_title");
            var opio_title = document.getElementById("opio_title");
            // sync the widget title with the opio_title
            widget_title.addEventListener("keyup", function() {
                opio_title.value = widget_title.value;
            });
            opio_title.addEventListener("keyup", function() {
                widget_title.value = opio_title.value;
            });
                var orgIDButton = document.getElementById("orgIDButton");
                orgIDButton.addEventListener("click", function(event) {
                    event.preventDefault();
                    // event.preventDefault();
                    var orgID = document.getElementById("orgIDSelector").value;
                    getBusinesses(orgID);
                });
                var savedReviewType = "<?php echo esc_html($review_type) ?>";
                var loadedBizs = <?php echo esc_html($loaded_bizs)  ?>;
                if(loadedBizs) {
                    var loadedBusinsses = document.getElementById("loadedBusinsses");
                    loadedBusinsses.value = JSON.stringify(loadedBizs);
                    var businessSelected = document.getElementById("businessSelected");
                    var bizData = (loadedBizs);
                    for (var i = 0; i < bizData.length; i++) {
                        var option = document.createElement("option");
                        option.text = bizData[i].name;
                        option.value = bizData[i]._id;
                        businessSelected.add(option);
                    }
                    businessSelected.value = "<?php echo esc_html($biz_org_id) ?>";
                    var businessListSelector = document.getElementById("businessListSelector");
                            businessListSelector.style.display = "block";

                }
                if(!savedReviewType || savedReviewType == 'single') {
                    document.getElementById("bizIDSelector").style.display = "block";

                } 
                if(savedReviewType == 'multiple') {
                    document.getElementById("orgStuff").style.display = "block";

                }
                if(savedReviewType == 'orgfeed') {
                    document.getElementById("orgStuff").style.display = "block";
                    document.getElementById("orgSelectStuff").style.display = "none";

                }
                var reviewTypeSelection = document.getElementById("reviewTypeSelection");
                var bizIDSelector = document.getElementById("bizIDSelector");
                var orgIDSelector = document.getElementById("orgIDSelector");
    
                reviewTypeSelection.addEventListener("change", function() {
                    if (reviewTypeSelection.value == "single") {
                        bizIDSelector.style.display = "block";
                        document.getElementById("orgStuff").style.display = "none";
                    }
                    if (reviewTypeSelection.value == "multiple") {
                        bizIDSelector.style.display = "none";
                        document.getElementById("orgStuff").style.display = "block";
                        document.getElementById("orgSelectStuff").style.display = "block";
                    }
                    if (reviewTypeSelection.value == "orgfeed") {
                        bizIDSelector.style.display = "none";
                        document.getElementById("orgStuff").style.display = "block";
                        document.getElementById("orgSelectStuff").style.display = "none";
                    }
                });
            });
        </script>
        <style>
            #footer-thankyou {display: none;}
            .update-nag { display: none; }
        </style>
        <?php
    }
}
?>
