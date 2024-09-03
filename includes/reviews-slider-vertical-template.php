<?php 

ob_start();

?>
<!-- reviews-slider-vertical-template.php -->
<div id="opio-vertical-widget">

<?php 
    $review_feed_link = '#';
    if(isset($feed_object->review_feed_link)) {
        $review_feed_link = $feed_object->review_feed_link;
    }
?>

<?php
    function getStarRating($average) {
        $starColor = ($average > 0.5) ? '#ffc600' : '#E6E8EB';
        $starGrey = '#E6E8EB';

        $fullStar = '<div class="rating-stars-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $halfStar = '<div class="rating-stars-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:' . $starGrey . ';}.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>';
        
        $emptyStar = '<div class="rating-stars-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:' . $starGrey . ';}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $isFullStar = $i <= $average;
            $isHalfStar = ($i == ceil($average) && $average - floor($average) >= 0.5);
            $stars .= $isFullStar ? $fullStar : ($isHalfStar ? $halfStar : $emptyStar);
        }
        return $stars;
    }

    function getStarRatingWidget($average) {
        $starColor = ($average > 0.5) ? '#ffc600' : '#E6E8EB';
        $starGrey = '#E6E8EB';

        $fullStar = '<div class="rating-stars-wd-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $halfStar = '<div class="rating-stars-wd-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:' . $starGrey . ';}.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>';
        
        $emptyStar = '<div class="rating-stars-wd-v"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:' . $starGrey . ';}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $isFullStar = $i <= $average;
            $isHalfStar = ($i == ceil($average) && $average - floor($average) >= 0.5);
            $stars .= $isFullStar ? $fullStar : ($isHalfStar ? $halfStar : $emptyStar);
        }
        return $stars;
    }

    function randomColor() {
        $colors = [
            '#2c3e50', // Dark Blue
            '#34495e', // Dark Gray
            '#7f8c8d', // Gray
            '#95a5a6', // Light Gray
            '#2ecc71', // Green
            '#3498db', // Blue
            '#9b59b6', // Purple
            '#e74c3c', // Red
            '#d35400', // Orange
        ];

        $randomIndex = mt_rand(0, count($colors) - 1);
        return $colors[$randomIndex];
    }
    // Define the function to include the template with parameters

    // Make API call to get reviews
    $api_url = 'https://op.io/api/entities/reviews-slider?cache=renew&hideWidget=true';

    $api_headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOjE2NDQzNDY2MzEsInVzZXJfaWQiOiJrdHN4dzlramxkZ3RzZDNqMSIsImV4cCI6MTI5NzY0NDM0NjYzMX0.FZeMMsZlix1eQ1aJFmQ0MV_L_ezFb4RhrqCIhceTT-w',
    ];

    // body params
    $body_params = [];
    $review_type = $feed_object->review_type;
    $review_option = $feed_object->review_option;
    // Check $review_type and set the appropriate property in $body_params
    if ($review_type === 'single') {
        $body_params['entityType'] = 'business';
        $body_params['entityId'] = $feed_object->biz_id;
    } elseif ($review_type === 'orgfeed') {
        $body_params['entityType'] = 'organization';
        $body_params['orgId'] = $feed_object->org_id;
    }

    // Check $review_option and set the typeOfFeed property in $body_params
    if ($review_option === 'all') {
        $body_params['typeOfFeed'] = 'mix';
    } elseif ($review_option === 'opio') {
        $body_params['typeOfFeed'] = 'opio';
    }

    $api_response = wp_remote_post($api_url, [
        'headers' => $api_headers,
        'body'    => $body_params,
        'timeout' => 15
    ]);

    // Check if the API call was successful

    if (!is_wp_error($api_response) && $api_response['response']['code'] === 200) {
        // Decode the JSON response
        $response = json_decode($api_response['body'], true);
        $business = $response;

        $reviews = [];
        $aggregateRating = 0;
        $totalReviews = 0;
        if($review_option === 'all') {
            $business = $response;
            $reviews = $response['reviews'];
            $aggregateRating = $response['aggregateRating']['3']['average'];
            $totalReviews = $response['aggregateRating']['3']['total'];
        } else if($review_option === 'opio') {
            $business = $response[0];
            $reviews = $response[0]['reviews'];
            $aggregateRating = $response[0]['aggregateRating']["5734f48a0b64d7382829fdf7"]['average'];
            $totalReviews = $response[0]['aggregateRating']["5734f48a0b64d7382829fdf7"]['total'];
        }

        
        $filteredReviews = [];
        foreach ($reviews as $review) {
            if (strlen($review['content']) > 10) {
                $filteredReviews[] = $review;
            }
        }

        $writeReviewUrl = 'https://op.io';
        if($review_type === 'orgfeed') {
            if(isset($business["landingPageUsername"])) {
                $writeReviewUrl = 'https://' .$business["landingPageUsername"]. '.op.io';
            }
        } else {
            $writeReviewUrl = 'https://op.io/write-review/5734f48a0b64d7382829fdf7/'.$business["_id"];
            if(isset($business["landingPageUsername"])) {
                $writeReviewUrl = 'https://' .$business["landingPageUsername"]. '.op.io';
            }
        }

    }
?>

<style>
    <?php if(isset($feed_object->writereview_button_color)) { ?>
        .widget-body .v-write-rev-div {
            background-color: <?php echo esc_attr($feed_object->writereview_button_color); ?>
        }
    <?php } ?>
    <?php if(isset($feed_object->writereview_text_color)) { ?>
        .widget-body .v-write-rev-div {
            color: <?php echo esc_attr($feed_object->writereview_text_color); ?>
        }
    <?php } ?>
</style>
<?php $currentReview = $review; ?>

<?php if(isset($filteredReviews) && count(array_slice($filteredReviews, 0, 7)) > 3) { ?>

<div class="widget-body opio-vertical">
    <div class="v-header">
        <div class="v-h-col-1">
            <span style="font-size: 22px; font-weight: 700;">Our Reviews</span>
        </div>
        <div class="v-h-col-2">
            <span class="v-pwd-span">Powered by</span>
            <a href="https://www.opioapp.com"><div class="v-h-opio-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-white-logo.png'; ?>);"></div></a>
        </div>
    </div>
    <div class="v-rating-container">
        <div class="v-r-col-1">
            <div class="v-r-row-1">
                <?php echo wp_kses(getStarRatingWidget($aggregateRating), $this->slider_deserializer->get_allowed_tags()); ?>
            </div>
            <span class="v-see-all-span"><a href="<?php echo esc_attr($review_feed_link); ?>">See all <?php echo esc_attr($totalReviews); ?> Reviews</a></span>
        </div>
        <div class="v-r-col-2">
            <div class="v-r-row-1"><span class="v-rating-span"><?php echo esc_attr($aggregateRating); ?>/5</span></div>
        </div>   
    </div>
    <div class="slider-container v-testimonial-slider vertical-slider">
    <?php
        foreach (array_slice($filteredReviews, 0, 7) as $review) {
    ?>
        <?php $currentReview = $review; ?>
            <div class="testimonial-slide review-tile-vertical" data-review-index="<?php echo esc_attr($index); ?>" onclick="openPhotoLightbox(<?php echo esc_attr(json_encode($currentReview)); ?>)">
                <div id=<?php echo esc_attr($review["_id"]);?> class="v-review-tile-container">
                    <div class="v-rev-content">
                        <?php if(($review['propertyId'] === 1 || $review['propertyId'] === 2 || $review['propertyId'] === 3 || $review['propertyId'] === 5) && isset($review['user']['userPic'])) { ?>
                            <div class="v-reviewer-avatar" style="background-image: url(<?php echo esc_attr($review['user']['userPic']);?>);">
                            </div>  
                        <?php } else if(isset($review['user']['userPic']) && isset($review['user']['userPic']['imageId'])) { ?>
                            <div class="v-reviewer-avatar" style="background-image: url(https://images.files.ca/200x200/<?php echo esc_attr($review['user']['userPic']['imageId']);?>.jpg?nocrop=1);">
                            </div>  
                        <?php } else if($review['user']['firstName']) { ?>
                            <div class="v-reviewer-avatar" style="background-color: <?php echo esc_attr(randomColor()); ?>"><?php echo esc_attr(mb_substr(ucfirst($review['user']["firstName"]), 0, 1, 'utf-8')); ?></div>
                        <?php } ?> 
                        
                    </div>
                    <div class="v-rev-rating-container">
                        <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                            <?php if($review['rating'] === "positive") { ?>
                                <div class="fb-rating-div">
                                    <img class="fb-rating-img" src="<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-recommends.png'; ?>" />
                                    <p class="fb-rating-text-p">Recommends</p>
                                </div>
                            <?php } else { ?>
                                <div class="fb-rating-div">
                                    <img class="fb-rating-img" src="<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-recommends-grey.png'; ?>"/>
                                    <p class="fb-rating-text-n">Doesn't Recommend</p>
                                </div>
                            <?php } ?>  
                        <?php } else { ?>
                            <div class="opio-rating-container">
                                <?php echo wp_kses(getStarRating($review['rating']), $this->slider_deserializer->get_allowed_tags()); ?>
                            </div>
                        <?php } ?>
                        <?php 
                            $reviewer_name = $review['user']['firstName'] . " " . $review['user']['lastName']; 
                            if(isset($reviewer_name) && strlen($reviewer_name) > 12) {
                                $reviewer_name = mb_substr($reviewer_name, 0, 12, 'UTF-8');
                            }
                        ?>
                        <div class="reviewer-name-container"><span class="reviewer-name"><?php echo esc_attr($reviewer_name); ?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                    </div>
                    <div>
                        <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                            <div class="v-facebook-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-logo.png'; ?>);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'google' || $review['propertyInfo']['name'] === 'Google') { ?>
                            <div class="v-google-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/google-logo.svg'; ?>);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'yelp') { ?>
                            <div class="v-yelp-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/yelp-logo.png'; ?>);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'Trip Advisor') { ?>
                            <div class="v-tripadvisor-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/tripadvisor-logo.png'; ?>);"></div>
                        <?php } else { ?>
                            <div class="v-opio-logo" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>);"></div>
                        <?php } ?>
                    </div>
                </div>
                <!-- <div class="location-name"><?php echo esc_attr($review['entityInfo']['address']['city']); ?></div> -->
                <?php if($review_type === 'orgfeed') { ?>
                    <div class="location-name"><?php echo esc_attr($review['entityInfo']['name']); ?></div>
                    <div class="review-content" id="v-reviewContent">
                    <?php if (strlen($review['content']) > 55) { ?>
                        <?php echo esc_attr(mb_substr($review['content'], 0, 55, 'UTF-8')); ?> <u>Read more</u>
                    <?php } else { ?>
                        <?php echo esc_attr($review['content']); ?>
                    <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="review-content" id="v-reviewContent">
                    <?php if (strlen($review['content']) > 60) { ?>
                        <?php echo esc_attr(mb_substr($review['content'], 0, 60, 'UTF-8')); ?> <u>Read more</u>
                    <?php } else { ?>
                        <?php echo esc_attr($review['content']); ?>
                    <?php } ?>
                    </div>
                <?php } ?>
            </div>
    <?php
        }
    ?>
    </div>
    <div class="v-footer">
        <div class="v-f-col-1">
            <div class="v-slider-button left" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/down-arrow.svg'; ?>);" ></div>
            <div class="v-slider-button right" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/top-arrow.svg'; ?>);" ></div>
        </div>
        <div class="v-f-col-2">
            <div class="v-wrire-rev-container">
                <a class="v-wrire-rev-a" target="_blank" href="<?php echo esc_attr($writeReviewUrl); ?>">
                    <div class="v-write-rev-div">
                        <div class="v-write-rev-div-2">
                            <span>Write a review</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <?php $reviewIndex = isset($_COOKIE['reviewIndex']) ? $_COOKIE['reviewIndex'] : null; ?>

    <div id="photo-lightbox">
        <div class="lb-reviewbox">
            <div class="lb-review-container">
                <div class="lb-review-avatar">
                    <div id="avatar-image" class="lb-reviewer-avatar"></div>
                </div>
                <div class="lb-review-content-container">
                    <div id="lb-fb-review-rating" class="lb-review-rating">
                    </div>
                    <div class="lb-reviwer-box">
                        <div id="reviewer-details" class="lb-reviewer-div"><span id="lb-reviewer-name" class="lb-reviewer-name"><?php echo esc_attr($filteredReviews[$reviewIndex]['user']['firstName']);?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                    </div>
                    <div class="lb-reviewtext">
                        <div id="lb-review-content" class="lb-review-content"><?php echo esc_attr($filteredReviews[$reviewIndex]['content']); ?></div>
                    </div>
                    <div class="lb-emp-tag" id="lb-empTag"></div>
                    <div id="lb-photo-container" class="lb-photo-div"></div>
                    <div id="lb-video-container" class="lb-video-div"></div>
                    <div id="lb-comment-container" class="lb-comment-div"></div>
                </div>
                <div class="lb-review-property">
                    <div id="lb-property-logo" class="lb-property-logo"></div>
                </div>    
            </div>
        </div>
        <div class="close-button" onclick="closePhotoLightbox()">x</div>
    </div>
</div>

<?php } else { ?>
    <div class="opio-more-reviews-required">
        <p class="opio-more-error-note">Error: More than 3 reviews are required for the widget</p>
    </div>
<?php } ?>
<style>
    .widget-body .slick-slide {
        display: block;
        float: none;
        height: 137px !important;
        min-height: 1px;
    }
</style>

<?php if(isset($feed_object->schema_enabled) && $feed_object->schema_enabled == 'yes') { ?>

<!-- JSON schema starts-->

<?php if(isset($feed_object->schema_type) && $feed_object->schema_type == 'local') { ?>
    <script id="jsonldSchema" type="application/ld+json">
    <?php $count=1; ?>
    {
        "@context": "http://schema.org",
        "@type": "LocalBusiness",
        "name": "<?php echo $business["name"]?>",
        "image": "<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "<?php echo esc_attr($business["address"]["address1"]); ?>",
            "addressRegion": "<?php echo esc_attr($business["address"]["province"]); ?>",
            "postalCode": "<?php echo esc_attr($business["address"]["postalCode"]); ?>"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo esc_attr($aggregateRating); ?>",
            "reviewCount": "<?php echo esc_attr($totalReviews); ?>"
        },
        "review": [
            <?php foreach(array_slice($filteredReviews, 0, 7) as $key => $review) { ?>
            {
                "@type": "Review",
                <?php if(isset($review['user']['firstName'])) { ?>
                "author": {
                    "@type": "Person",
                    "name": "<?php echo esc_attr($review['user']['firstName']); ?>"
                },
                <?php } ?>
                "datePublished": "<?php echo esc_attr(date('M d, Y', $review["dateCreated"]/1000)); ?>",
                "reviewBody": "<?php echo esc_attr($review['content']); ?>",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": <?php echo esc_attr($review['propertyInfo']['name'] === 'facebook' ? $review['rating'] === 'positive' ? 5 : 1 : $review['rating']); ?>
                },
                "publisher": {
                    "@type": "Organization",
                    "name": "op.io",
                    "sameAs": "https://www.op.io"
                }
            }
            <?php if($count < count(array_slice($filteredReviews, 0, 7))){
                echo ",";
            } 
            ?>
            <?php $count = $count + 1; ?>
            <?php } ?>
        ]
    }
    </script>

<?php } else { ?>
    <script id="jsonldSchema" type="application/ld+json">
    <?php $count=1; ?>
    {
        "@context": "http://schema.org",
        "@type": "Product",
        "name": "<?php echo $business["name"]?>",
        "image": "<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?php echo esc_attr($aggregateRating); ?>",
            "reviewCount": "<?php echo esc_attr($totalReviews); ?>"
        },
        <?php if(isset($business['lowPriceRange']) && isset($business['highPriceRange']) && $business['lowPriceRange'] !== null && $business['highPriceRange'] !== null) { ?>
            "offers": {
                "@type": "AggregateOffer",
                "offerCount": 5,
                "lowPrice": "<?php echo $business['lowPriceRange']?>",
                "highPrice": "<?php echo $business['highPriceRange']?>",
                "priceCurrency": "CAD"
            },
        <?php } ?>
        "review": [
            <?php foreach(array_slice($filteredReviews, 0, 7) as $key => $review) { ?>

            {
                "@type": "Review",
                <?php if(isset($review['user']['firstName'])) { ?>
                "author": {
                    "@type": "Person",
                    "name": "<?php echo esc_attr($review['user']['firstName']); ?>"
                },
                <?php } ?>
                "datePublished": "<?php echo esc_attr(date('M d, Y', $review["dateCreated"]/1000)); ?>",
                "reviewBody": "<?php echo esc_attr($review['content']); ?>",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue":  <?php echo esc_attr($review['propertyInfo']['name'] === 'facebook' ? $review['rating'] === 'positive' ? 5 : 1 : $review['rating']); ?>
                },
                "publisher": {
                    "@type": "Organization",
                    "name": "op.io",
                    "sameAs": "https://www.op.io"
                }
            }
            <?php if($count < count(array_slice($filteredReviews, 0, 7))){
                echo ",";
            } 
            ?>
            <?php $count = $count + 1; ?>
            
            <?php } ?>
        ]
    }
</script>
<?php } ?>

<!-- JSON schema ends-->

<?php } ?>

</div>

<?php 

$output = ob_get_clean();
echo $output;
?>