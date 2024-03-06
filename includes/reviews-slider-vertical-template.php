<!-- reviews-slider-vertical-template.php -->

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

        $fullStar = '<div class="rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $halfStar = '<div class="rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:' . $starGrey . ';}.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>';
        
        $emptyStar = '<div class="rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:' . $starGrey . ';}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
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
    $api_url = 'https://op.io/api/entities/reviews-slider';

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
<?php $currentReview = $review; ?>

<div class="widget-body opio-vertical">
    <div class="v-header">
        <div class="v-h-col-1">
            <span style="font-size: 22px; font-weight: 700;">Our reviews</span>
        </div>
        <div class="v-h-col-2">
            <span class="v-pwd-span">Powered by</span>
            <a href="https://www.opioapp.com"><div class="v-h-opio-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-white-logo.png'; ?>&quot;);"></div></a>
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
    <div class="slider-container testimonial-slider vertical-slider">
    <?php
        foreach (array_slice($filteredReviews, 0, 7) as $review) {
    ?>
        <?php $currentReview = $review; ?>
            <div class="testimonial-slide review-tile-vertical" data-review-index="<?php echo esc_attr($index); ?>" onclick="openPhotoLightbox(<?php echo esc_attr(json_encode($currentReview)); ?>)">
                <div id=<?php echo esc_attr($review["_id"]);?> class="v-review-tile-container">
                    <div class="v-rev-content">
                        <?php if(($review['propertyId'] === 1 || $review['propertyId'] === 2 || $review['propertyId'] === 3 || $review['propertyId'] === 5) && isset($review['user']['userPic'])) { ?>
                            <div class="v-reviewer-avatar" style="background-image: url(&quot;<?php echo esc_attr($review['user']['userPic']);?>&quot;);">
                            </div>  
                        <?php } else if(isset($review['user']['userPic']) && isset($review['user']['userPic']['imageId'])) { ?>
                            <div class="v-reviewer-avatar" style="background-image: url(&quot;https://images.files.ca/200x200/<?php echo esc_attr($review['user']['userPic']['imageId']);?>.jpg?nocrop=1&quot;);">
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
                        <div class="reviewer-name-container"><span class="reviewer-name"><?php echo esc_attr($review['user']['firstName']);?> <?php echo esc_attr($review['user']['lastName']);?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                    </div>
                    <div>
                        <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                            <div class="v-facebook-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-logo.png'; ?>&quot;);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'google') { ?>
                            <div class="v-google-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/google-logo.svg'; ?>&quot;);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'yelp') { ?>
                            <div class="v-yelp-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/yelp-logo.png'; ?>&quot;);"></div>
                        <?php } else if($review['propertyInfo']['name'] === 'Trip Advisor') { ?>
                            <div class="v-tripadvisor-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/tripadvisor-logo.png'; ?>&quot;);"></div>
                        <?php } else { ?>
                            <div class="v-opio-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>&quot;);"></div>
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
                    <?php if (strlen($review['content']) > 70) { ?>
                        <?php echo esc_attr(mb_substr($review['content'], 0, 70, 'UTF-8')); ?> <u>Read more</u>
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
            <div class="v-slider-button left" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/down-arrow.svg'; ?>&quot;);" ></div>
            <div class="v-slider-button right" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/top-arrow.svg'; ?>&quot;);" ></div>
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

<style>
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


<script>

    // Use jQuery.noConflict() to avoid conflicts with other libraries
    var opio_slider_v_jq = jQuery.noConflict();

    var selectedReviewIndex;

    function getStarRatingJS(average) {
        const starColor = (average > 0.5) ? '#ffc600' : '#E6E8EB';
        const starGrey = '#E6E8EB';

        const fullStar = '<div class="lb-rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' + starColor + ';}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';

        const halfStar = '<div class="lb-rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:' + starGrey + ';}.str-1{fill: ' + starColor + ';}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>';

        const emptyStar = '<div class="lb-rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:' + starGrey + ';}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';

        var stars = '';
        for (var i = 1; i <= 5; i++) {
            const isFullStar = i <= average;
            var isHalfStar = false;
            if (i === Math.ceil(average)) {
                if (average - Math.floor(average) >= 0.5) {
                    isHalfStar = true;
                }
            }
            stars += isFullStar ? fullStar : (isHalfStar ? halfStar : emptyStar);
        }
        return stars;
    }

    function randomColor() {
        const colors = [
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

        const randomIndex = Math.floor(Math.random() * colors.length);
        return colors[randomIndex];
    }

    function displayLargeImage(imageId, revId) {
        var photoContainer = document.querySelector(`#lb-photo-container`);
        photoContainer.style.display = 'block';

        // Select all elements with the class name 'photo-a-tag'
        var photoATags = document.querySelectorAll('.photo-a-tag');

        // Iterate through each element and change its display property
        photoATags.forEach(function(photoATag) {
            photoATag.style.display = 'inline-block';
        });

        var elem = document.querySelector(`#largerevimg-${revId}`);
        elem.style.display = 'flex';
        elem.innerHTML = `
            <div onClick="hideLargeImage('${revId}')" class="lb-large-img" style="background-image: url('https://images.files.ca/800x800/${imageId}.jpg?nocrop=1');"></div>
            <div>
                <div class="lb-large-img-div"></div>
                <div style="display: none;"></div>
            </div>`;
    }

    function hideLargeImage(revId) {
        var elem = document.querySelector(`#largerevimg-${revId}`);
        elem.style.display = 'none';

        var photoContainer = document.querySelector(`#lb-photo-container`);
        photoContainer.style.display = 'flex';

        // Select all elements with the class name 'photo-a-tag'
        var photoATags = document.querySelectorAll('.photo-a-tag');

        // Iterate through each element and change its display property
        photoATags.forEach(function(photoATag) {
            photoATag.style.display = 'flex';
        });
    }

    async function openPhotoLightbox(reviewData) {
        console.log('lightbox clicked!',reviewData)

        // var review = JSON.parse(reviewData);
        document.getElementById('photo-lightbox').style.display = 'flex';

        // set data to lightbox
        var imageUrl = reviewData['user']['userPic'];

        // Update reviewer avatar
        var avatarImage = document.getElementById('avatar-image');

        if ((reviewData.propertyId === 1 || reviewData.propertyId === 2 || reviewData.propertyId === 3 || reviewData.propertyId === 5)) {
            if(reviewData.user.userPic) {
                avatarImage.innerHTML = '';
                avatarImage.style.backgroundImage = 'url("' + reviewData.user.userPic + '")';
            }
        } else if (reviewData.user.userPic) {
            if(reviewData.user.userPic.imageId) {
                avatarImage.innerHTML = '';
                avatarImage.style.backgroundImage = 'url("https://images.files.ca/200x200/' + reviewData.user.userPic.imageId + '.jpg?nocrop=1")';
            }
        } else if (reviewData.user ) {
            if(reviewData.user.firstName) {
                avatarImage.style.backgroundImage = 'none'; // Set to none if there is a background color
                avatarImage.style.backgroundColor = randomColor();
                avatarImage.innerHTML = reviewData.user.firstName.charAt(0).toUpperCase();
            }
        }
        
        // update review rating
        var ratingElement = document.getElementById('lb-fb-review-rating');

        if(reviewData.propertyInfo.name === 'facebook') {
            if (reviewData.rating === "positive") {
                ratingElement.innerHTML = `
                <div class="lb-fb-rating-div">
                    <img class="lb-fb-rating-img" src="<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-recommends.png'; ?>"/>
                    <p class="lb-fb-rating-text-p">Recommends</p>
                </div>`;
            } else {
                ratingElement.innerHTML = `
                <div class="lb-fb-rating-div>
                    <img class="lb-fb-rating-img" src="<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-recommends-grey.png'; ?>" />
                    <p class="lb-fb-rating-text-n">Doesn't Recommend</p>
                </div>`;
            }
        } else {
            var starHTML =  getStarRatingJS(reviewData.rating)
            ratingElement.innerHTML = `<div class="lb-opio-rating-container">${starHTML}</div>`;
        }

        // Update reviewer name
        document.getElementById('reviewer-details').innerHTML = `<span id="lb-reviewer-name" class="lb-reviewer-name">${reviewData.user.firstName} ${reviewData.user.lastName}</span> on ${moment(reviewData.dateCreated).format("MMM D, YYYY")}`;

        // Update review content
        document.getElementById('lb-review-content').textContent = reviewData.content;

        // Update property logo

        var lbPropertyLogo = document.getElementById('lb-property-logo');

        if (reviewData.propertyInfo.name === 'facebook') {
            lbPropertyLogo.style.backgroundImage = 'url("<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-logo.png'; ?>")';
            lbPropertyLogo.style.width = '66px';
            lbPropertyLogo.style.height = '26px';
            lbPropertyLogo.style.minWidth = '66px';
            lbPropertyLogo.style.minHeight = '26px';
            lbPropertyLogo.style.marginTop = '-5px';
        } else if (reviewData.propertyInfo.name === 'google') {
            lbPropertyLogo.style.backgroundImage = 'url("<?php echo esc_url(OPIO_ASSETS_URL) . 'img/google-logo.svg'; ?>")';
            lbPropertyLogo.style.width = '60px';
            lbPropertyLogo.style.height = '22px';
            lbPropertyLogo.style.minWidth = '60px';
            lbPropertyLogo.style.minHeight = '22px';
            lbPropertyLogo.style.marginTop = '0px';
        } else if (reviewData.propertyInfo.name === 'yelp') {
            lbPropertyLogo.style.backgroundImage = 'url("<?php echo esc_url(OPIO_ASSETS_URL) . 'img/yelp-logo.png'; ?>")';
            lbPropertyLogo.style.width = '70px';
            lbPropertyLogo.style.height = '26px';
            lbPropertyLogo.style.minWidth = '70px';
            lbPropertyLogo.style.minHeight = '26px';
            lbPropertyLogo.style.marginTop = '0px';
        } else if (reviewData.propertyInfo.name === 'Trip Advisor') {
            lbPropertyLogo.style.backgroundImage = 'url("<?php echo esc_url(OPIO_ASSETS_URL) . 'img/tripadvisor-logo.png'; ?>")';
            lbPropertyLogo.style.width = '110px';
            lbPropertyLogo.style.height = '25px';
            lbPropertyLogo.style.minWidth = '110px';
            lbPropertyLogo.style.minHeight = '25px';
            lbPropertyLogo.style.marginTop = '0px';
        } else {
            lbPropertyLogo.style.backgroundImage = 'url("<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>")';
            lbPropertyLogo.style.width = '60px';
            lbPropertyLogo.style.height = '24px';
            lbPropertyLogo.style.minWidth = '60px';
            lbPropertyLogo.style.minHeight = '24px';
            lbPropertyLogo.style.marginTop = '0px';
        }

        // Update employee tags
        const reviewContainer = document.getElementById('lb-empTag');
        reviewContainer.innerHTML = '';

        if (reviewData.taggedEmployees && reviewData.taggedEmployees.length > 0) {
            const empTagContainer = document.createElement('div');
            empTagContainer.classList.add('lb-empTagContainer');

            reviewData.taggedEmployees.forEach(emp => {
                let position = '';
                if (emp.position_title.length === 0) {
                    position = ''
                } else {
                    let posIndex = emp.position_title
                    .map((p) => p.entityId)
                    .indexOf(reviewData.entityId);
                    if (posIndex !== -1) {
                        position = emp.position_title[posIndex].position
                    }
                }  

                const empTagCard = document.createElement('div');
                empTagCard.classList.add('lb-empTagCard');

                const innerDiv = document.createElement('div');
                innerDiv.classList.add('lb-emp-avatar');

                // innerDiv.style.width = '35px';
                // innerDiv.style.height = '35px';
                // innerDiv.style.lineHeight = '35px';
                // innerDiv.style.borderRadius = '50%'; 
                // innerDiv.style.color = '#637282'; 
                // innerDiv.style.fontSize = '24px'; 
                // innerDiv.style.textAlign = 'center'; 
                // innerDiv.style.backgroundSize = 'cover'; 
                // innerDiv.style.backgroundRepeat = 'no-repeat'; 

                if (emp.userPic && emp.userPic.imageId) {
                    innerDiv.style.backgroundImage = `url('https://images.files.ca/200x200/${emp.userPic.imageId}.jpg?nocrop=1')`;
                } else if (emp.userPic && emp.userPic !== "") {
                    innerDiv.style.backgroundImage = `url('${emp.userPic}')`;
                } else {
                    innerDiv.style.backgroundColor = '#dddddd';
                    innerDiv.textContent = emp.firstName ? emp.firstName.charAt(0).toUpperCase() : '';
                }

                const nameDiv = document.createElement('div');
                // nameDiv.classList.add('lb-emp-name-div');
                nameDiv.style.display = 'grid';
                nameDiv.style.gridTemplateRows = '25px';

                const nameSpan = document.createElement('span');
                nameSpan.classList.add('lb-emp-name-span');
                // nameSpan.style.alignSelf = 'end';
                // nameSpan.style.justifySelf = 'left';
                // nameSpan.style.marginLeft = '10px';
                // nameSpan.style.color = '#637282';
                // nameSpan.style.fontSize = '14px';

                if (emp.firstName && emp.lastName) {
                    nameSpan.textContent = `${emp.firstName} ${emp.lastName.charAt(0).toUpperCase()}`;
                } else if (emp.firstName && !emp.lastName) {
                    nameSpan.textContent = emp.firstName;
                }

                nameDiv.appendChild(nameSpan);

                const positionDiv = document.createElement('div');
                positionDiv.classList.add('lb-emp-position-div');
                // positionDiv.style.display = 'grid';
                // positionDiv.style.gridTemplateRows = '20px 15px';

                const positionNameSpan = document.createElement('span');
                positionNameSpan.classList.add('lb-emp-position-name-span');
                // positionNameSpan.style.alignSelf = 'end';
                // positionNameSpan.style.justifySelf = 'left';
                // positionNameSpan.style.marginLeft = '10px';
                // positionNameSpan.style.color = '#637282';
                // positionNameSpan.style.fontSize = '14px';

                const positionSpan = document.createElement('span');
                positionSpan.classList.add('lb-emp-position-span');
                // positionSpan.style.alignSelf = 'top';
                // positionSpan.style.justifySelf = 'left';
                // positionSpan.style.marginLeft = '10px';
                // positionSpan.style.marginTop = '-2px';
                // positionSpan.style.color = '#637282';
                // positionSpan.style.fontSize = '12px';

                if (emp.position_title) {
                    positionNameSpan.textContent = emp.firstName;
                    positionSpan.textContent = position || '';
                }

                positionDiv.appendChild(positionNameSpan);
                positionDiv.appendChild(positionSpan);

                empTagCard.appendChild(innerDiv);
                empTagCard.appendChild(position === '' ? nameDiv : positionDiv);

                empTagContainer.appendChild(empTagCard);
            });
            reviewContainer.innerHTML = `<span class="lb-emp-mention">Employees mentions:</span>`;
            reviewContainer.appendChild(empTagContainer);
        }

        // Update photos
        if(reviewData.images && reviewData.images.length > 0) {
            // Creating a div for the larger image with the specified id
            var revId = reviewData._id; // Replace with the actual revId
            var photoContainer = document.getElementById(`lb-photo-container`); // Replace with the actual container id
            photoContainer.innerHTML = '';

            var largerImageDiv = document.createElement("div");
            largerImageDiv.id = `largerevimg-${revId}`;
            largerImageDiv.style.display = "none"; // Initially set to hidden
            photoContainer.appendChild(largerImageDiv);
            reviewData.images.map(img => {
                // Assuming this is inside a loop for each image
                // Replace $image['imageId'] and $_rev['_id'] with appropriate JavaScript variables
                var imageId = img.imageId; // Replace with the actual imageId

                var anchor = document.createElement("a");
                anchor.setAttribute("onclick", `displayLargeImage('${imageId}', '${revId}')`);
                anchor.style.borderBottom = "none";
                anchor.style.display = "flex";
                anchor.classList.add("photo-a-tag");

                var imageDiv = document.createElement("div");
                imageDiv.classList.add("lb-small-img");
                // imageDiv.style.display = "inline-block";
                // imageDiv.style.width = "72px";
                // imageDiv.style.height = "72px";
                // imageDiv.style.backgroundPosition = "center center";
                // imageDiv.style.backgroundSize = "cover";
                // imageDiv.style.backgroundRepeat = "no-repeat";
                // imageDiv.style.margin = "5px";
                // imageDiv.style.textAlign = "center";
                imageDiv.style.backgroundImage = `url('https://images.files.ca/200x200/${imageId}.jpg?nocrop=1')`;

                anchor.appendChild(imageDiv);
                photoContainer.appendChild(anchor);
            })
        }


        // Update videos
        if (reviewData.videos && reviewData.videos.length > 0) {
            var mediaContainer = document.getElementById('lb-video-container');
            mediaContainer.innerHTML = '';

            reviewData.videos.forEach(video => {
                // Create a temporary container
                var tempContainer = document.createElement('div');

                // Set the HTML string as its innerHTML
                tempContainer.innerHTML = `<div><video preload="auto" controls="" class="lb-video-player"><source src="https://videocdn.n49.ca/mp4sdpad480p/${video['videoId']}.mp4#t=0.1" type="video/mp4"></video></div>`;

                // Append the child of the temporary container to the mediaContainer
                mediaContainer.appendChild(tempContainer.firstChild);
            });

        }

        // Update comments
        if(reviewData.comments && reviewData.comments.length > 0) {
            console.log('coming in comments',reviewData.comments);
            var commentContainer = document.getElementById('lb-comment-container');
            commentContainer.style.marginTop = '5px';
            commentContainer.innerHTML = '';

            var commentHeader = document.createElement("div");
            commentHeader.classList.add('lb-comment-header');
            commentHeader.textContent = 'Comments';
            commentContainer.appendChild(commentHeader);

            var commentBox = document.createElement("div");
            commentBox.classList.add('lb-comment-box');

            var commentAvatar = document.createElement("div");
            commentAvatar.classList.add('lb-comment-avatar');

            var commenterName = reviewData.entityInfo.name; // business name by default

            if(reviewData.comments[0].users && reviewData.comments[0].users[0].fullName) {
                commenterName = reviewData.comments[0].users[0].fullName
            }

            // Update comment avatar
            commentAvatar.style.backgroundImage = 'none'; // Set to none if there is a background color
            commentAvatar.style.backgroundColor = randomColor();
            commentAvatar.innerHTML = commenterName.charAt(0).toUpperCase();
            commentBox.appendChild(commentAvatar);

            var commentContentDiv = document.createElement("div");
            commentContentDiv.classList.add('lb-comment-content-div');

            var byCommentName = document.createElement("div");
            byCommentName.classList.add("lb-by-comment-name");

            byCommentName.innerHTML = `<span class="lb-by-name-span">By ${commenterName} </span>&nbsp;on ${moment(reviewData.comments[0].dateCreated).format("MMM D, YYYY")}`;
            commentContentDiv.appendChild(byCommentName);

            var commentContent = document.createElement("div");
            commentContent.classList.add("lb-comment-content");
            commentContent.innerText =reviewData.comments[0].content;
            commentContentDiv.appendChild(commentContent);

            commentBox.appendChild(commentContentDiv);
            commentContainer.appendChild(commentBox);

        }
    }

    function closePhotoLightbox() {
        var photoContainer = document.getElementById(`lb-photo-container`);
        photoContainer.innerHTML = '';
        var mediaContainer = document.getElementById('lb-video-container');
        mediaContainer.innerHTML = '';
        var commentContainer = document.getElementById('lb-comment-container');
        commentContainer.innerHTML = '';
        document.getElementById('photo-lightbox').style.display = 'none';
        // Clear the cookie
        document.cookie = 'reviewIndex=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }

    opio_slider_v_jq(document).ready(function () {
        // Initialize the Slick slider
        var slider = opio_slider_v_jq('.testimonial-slider').slick({
            autoplay: true,
            autoplaySpeed: 4000,
            speed: 600,
            draggable: true,
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: false,
            dots: false,
            vertical: true,
        });

        // Move to the next slide on clicking the ">" button
        opio_slider_v_jq('.v-slider-button.right').click(function () {
            slider.slick('slickNext');
        });

        // Move to the previous slide on clicking the "<" button
        opio_slider_v_jq('.v-slider-button.left').click(function () {
            slider.slick('slickPrev');
        });
    });

    // Close lightbox when clicking outside the video container
    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('lightbox')) {
            closeLightbox();
        }
        if (event.target === document.getElementById('photo-lightbox')) {
            closePhotoLightbox();
        }
    });

    // Close lightbox on pressing the 'Esc' key
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLightbox();
            closePhotoLightbox();
        }
    });
</script>

