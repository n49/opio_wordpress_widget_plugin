<?php 

ob_start();

?>
<!-- reviews-slider-horizontal-carousel-template.php -->
<div id="opio-carousel-widget">
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

        $fullStar = '<div class="rating-stars-wd-c"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
        $halfStar = '<div class="rating-stars-wd-c"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:' . $starGrey . ';}.str-1{fill: ' . $starColor . ';}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>';
        
        $emptyStar = '<div class="rating-stars-wd-c"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:' . $starGrey . ';}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
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

    // Function to check if the user is on a mobile device
    function isMobileDevice() {
        return (isset($_SERVER['HTTP_USER_AGENT']) && 
                preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $_SERVER['HTTP_USER_AGENT']));
    }
    // Check if it's a mobile device
    $isMobile = isMobileDevice();

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

<?php if(isset($filteredReviews) && count(array_slice($filteredReviews, 0, 8)) > 3) { ?>

<div class="widget-body opio-carousel">
    <div class="slider-container c-testimonial-slider">
    <?php
        // Display the first 9 reviews
        foreach (array_slice($filteredReviews, 0, 8) as $review) {
            ?>
            <?php $currentReview = $review; ?>
            <div class="testimonial-slide review-tile" id=<?php echo esc_attr($review["_id"]);?> data-review-index="<?php echo esc_attr($index); ?>" onclick="openPhotoLightbox(<?php echo esc_attr(json_encode($currentReview)); ?>)">
                <div style="display: flex; position: relative;" >
                    <div style="vertical-align: top;">
                        <div class="avatar-container">
                            <?php if(($review['propertyId'] === 1 || $review['propertyId'] === 2 || $review['propertyId'] === 3 || $review['propertyId'] === 5) && isset($review['user']['userPic'])) { ?>
                                <div class="reviewer-avatar" style="background-image: url(<?php echo esc_attr($review['user']['userPic']);?>);">
                                </div>  
                            <?php } else if(isset($review['user']['userPic']) && isset($review['user']['userPic']['imageId'])) { ?>
                                <div class="reviewer-avatar" style="background-image: url(https://images.files.ca/200x200/<?php echo esc_attr($review['user']['userPic']['imageId']);?>.jpg?nocrop=1);">
                                </div>  
                            <?php } else if($review['user']['firstName']) { ?>
                                <div class="reviewer-avatar" style="background-color: <?php echo esc_attr(randomColor()); ?>"><?php echo esc_attr(mb_substr(ucfirst($review['user']["firstName"]), 0, 1, 'utf-8')); ?></div>
                            <?php } ?>    
                            <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                                <div class="c-facebook-logo"></div>
                            <?php } else if($review['propertyInfo']['name'] === 'google' || $review['propertyInfo']['name'] === 'Google') { ?>
                                <div class="c-google-logo"></div>
                            <?php } else if($review['propertyInfo']['name'] === 'yelp') { ?>
                                <div class="c-yelp-logo"></div>
                            <?php } else if($review['propertyInfo']['name'] === 'Trip Advisor') { ?>
                                <div class="c-tripadvisor-logo"></div>
                            <?php } else { ?>
                                <div class="c-opio-logo"></div>
                            <?php } ?>  
                        </div>
                    </div>
                    <div class="rating-container">
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
                            if(isset($reviewer_name) && strlen($reviewer_name) > 28) {
                                $reviewer_name = mb_substr($reviewer_name, 0, 28, 'UTF-8');
                            }
                        ?>
                        <div class="reviewer-name-container"><span class="reviewer-name"><?php echo esc_attr($reviewer_name);?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                    </div>

                </div>
                <?php if($review_type === 'orgfeed') { ?>
                    <?php 
                        $contentWithMedia_org = 40;
                        $contentWithoutMedia_org = 90;

                        if($isMobile) {
                            $contentWithMedia_org = 60;
                            $contentWithoutMedia_org = 110;
                        }
                    ?>
                    <div class="location-name"><?php echo esc_attr($review['entityInfo']['name']); ?></div>
                        <?php if((isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) || 
                                (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0)) { ?>
                        <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>" style="margin-top: 0px;">
                            <?php if (strlen($review['content']) > $contentWithMedia_org) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithMedia_org, 'UTF-8')); ?> <u>Read more</u>
                            <?php } else { ?>
                                <?php echo esc_attr($review['content']); ?>
                            <?php } ?>
                        </div>
                        <div class="review-media-images">
                        <?php if(isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) { ?>
                            <?php foreach (array_slice($review['images'], 0, 3) as $image) {?>
                                <div>
                                    <img class="review-img" src="https://op.io/dashboard/api/reviews/get-image/<?php echo esc_attr($image['imageId']); ?>?width=200&height=200">
                                </div>
                            <?php } ?>
                        <?php } ?>
                            <?php if(isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) { ?>
                                <div class="video-icon">
                                    <div class="play-button"></div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php } else if(isset($review['taggedEmployees']) && is_array($review['taggedEmployees']) && count($review['taggedEmployees']) > 0) { ?>
                            <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>" style="margin-top: 0px;">
                                <?php if (strlen($review['content']) > $contentWithMedia_org) { ?>
                                    <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithMedia_org, 'UTF-8')); ?> <u>Read more</u>
                                <?php } else { ?>
                                    <?php echo esc_attr($review['content']); ?>
                                <?php } ?>
                            </div>
                            <div class="tile-tag-container">
                                <?php foreach (array_slice($review['taggedEmployees'], 0, 4) as $index => $emp) { ?>
                                    <div class="review-tagged-emps">
                                        <?php if(isset($emp["userPic"]["imageId"])) { ?>
                                            <div class="emp-avatar" style="background-image: url(https://images.files.ca/200x200/<?php echo esc_attr($emp["userPic"]["imageId"]); ?>.jpg?nocrop=1);"></div>
                                        <?php } else { ?>
                                        <?php if(isset($emp["userPic"]) && $emp["userPic"] != "") { ?>
                                            <div class="emp-avatar" style="background-image: url(<?php echo esc_attr($emp["userPic"]); ?>);"></div>
                                        <?php } else { ?>
                                            <div class="emp-avatar" style="background-color: #dddddd"><?php echo esc_attr(mb_substr(ucfirst($emp["firstName"]), 0, 1, 'utf-8')); ?></div>
                                        <?php } } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>" style="margin-top: 0px;">
                            <?php if (strlen($review['content']) > $contentWithoutMedia_org) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithoutMedia_org, 'UTF-8')); ?> <u>Read more</u>
                            <?php } else { ?>
                                <?php echo esc_attr($review['content']); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                <?php } else { ?>
                    <?php 
                        $contentWithMedia_biz = 55;
                        $contentWithoutMedia_biz = 155;

                        if($isMobile) {
                            $contentWithMedia_biz = 60;
                            $contentWithoutMedia_biz = 170;
                        }
                    ?>
                    <?php if((isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) || 
                            (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0)) { ?>
                    <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>">
                        <?php if (strlen($review['content']) > $contentWithMedia_biz) { ?>
                            <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithMedia_biz, 'UTF-8')); ?> <u>Read more</u>
                        <?php } else { ?>
                            <?php echo esc_attr($review['content']); ?>
                        <?php } ?>
                    </div>
                    <div class="review-media-images">
                    <?php if(isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) { ?>
                        <?php foreach (array_slice($review['images'], 0, 3) as $image) {?>
                            <div>
                                <img class="review-img" src="https://op.io/dashboard/api/reviews/get-image/<?php echo esc_attr($image['imageId']); ?>?width=200&height=200">
                            </div>
                        <?php } ?>
                    <?php } ?>
                        <?php if(isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) { ?>
                            <div class="video-icon">
                                <div class="play-button"></div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } else if(isset($review['taggedEmployees']) && is_array($review['taggedEmployees']) && count($review['taggedEmployees']) > 0) { ?>
                        <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>">
                            <?php if (strlen($review['content']) > $contentWithMedia_biz) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithMedia_biz, 'UTF-8')); ?> <u>Read more</u>
                            <?php } else { ?>
                                <?php echo esc_attr($review['content']); ?>
                            <?php } ?>
                        </div>
                        <div class="tile-tag-container">
                            <?php foreach (array_slice($review['taggedEmployees'], 0, 4) as $index => $emp) { ?>
                                <div class="review-tagged-emps">
                                    <?php if(isset($emp["userPic"]["imageId"])) { ?>
                                        <div class="emp-avatar" style="background-image: url(https://images.files.ca/200x200/<?php echo esc_attr($emp["userPic"]["imageId"]); ?>.jpg?nocrop=1);"></div>
                                    <?php } else { ?>
                                    <?php if(isset($emp["userPic"]) && $emp["userPic"] != "") { ?>
                                        <div class="emp-avatar" style="background-image: url(<?php echo esc_attr($emp["userPic"]); ?>);"></div>
                                    <?php } else { ?>
                                        <div class="emp-avatar" style="background-color: #dddddd"><?php echo esc_attr(mb_substr(ucfirst($emp["firstName"]), 0, 1, 'utf-8')); ?></div>
                                    <?php } } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>">
                        <?php if (strlen($review['content']) > $contentWithoutMedia_biz) { ?>
                            <?php echo esc_attr(mb_substr($review['content'], 0, $contentWithoutMedia_biz, 'UTF-8')); ?> <u>Read more</u>
                        <?php } else { ?>
                            <?php echo esc_attr($review['content']); ?>
                        <?php } ?>
                    </div>
                    <?php } ?>
                <?php } ?>  
            </div>
    <?php
        }
    ?>
    </div>
    <div class="c-rating-widget-container">
        <div class="c-rating-row-1">
        <?php if(isset($feed_object->opio_logo_color)) { ?>
            <?php if($feed_object->opio_logo_color == 'blue' || $feed_object->opio_logo_color == '') { ?>
                <?php if($isMobile) { ?>
                    <span id="powered-by-text" class="c-pwd-span"></span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>);"></div></a>
                <?php } else { ?>
                    <span id="powered-by-text" class="c-pwd-span">Powered by</span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>);"></div></a>
                <?php } ?>
            <?php } else if($feed_object->opio_logo_color == 'white') { ?>
                <?php if($isMobile) { ?>
                    <span id="powered-by-text" class="c-pwd-span"></span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-white-logo.png'; ?>);"></div></a>
                <?php } else { ?>
                    <span id="powered-by-text" class="c-pwd-span">Powered by</span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-white-logo.png'; ?>);"></div></a>
                <?php } ?>
            <?php } ?>
            <?php } else { ?>
                <?php if($isMobile) { ?>
                    <span id="powered-by-text" class="c-pwd-span"></span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>);"></div></a>
                <?php } else { ?>
                    <span id="powered-by-text" class="c-pwd-span">Powered by</span><a href="https://www.opioapp.com"><div class="c-pwd-div" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>);"></div></a>
                <?php } ?>        
            <?php } ?>
        </div>
        <div class="c-vertical-divider"></div>
        <div class="c-rating-row-2">
            <div style="display: flex; gap: 5px;">
                <div class="c-rating-r-row-1">
                    <?php echo wp_kses(getStarRatingWidget($aggregateRating), $this->slider_deserializer->get_allowed_tags()); ?>
                </div>
                <div class="c-rating-r-row-2"><span class="c-rating-row-span"><?php echo esc_attr($aggregateRating); ?>/5</span></div>
            </div>
            <div class="c-see-all-div">
                <span class="c-see-all-span">
                    <a href="<?php echo esc_attr($review_feed_link); ?>">See all <?php echo esc_attr($totalReviews); ?> Reviews</a>
                </span>
            </div>   
        </div>
        <div class="c-rating-row-3">
            <div class="c-write-rev-container">
                <a class="write-review-btn-a" target="_blank" href="<?php echo esc_attr($writeReviewUrl); ?>">
                    <div class="c-write-rev-outer-div">
                        <div class="c-write-rev-inner-div">
                            <span>Write a review</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="c-rating-row-4">
            <div class="c-slider-button left" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/left-arrow.svg'; ?>);" ></div>
            <div class="c-slider-button right" style="background-image: url(<?php echo esc_url(OPIO_ASSETS_URL) . 'img/right-arrow.svg'; ?>);" ></div>
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
    .slider-container.c-testimonial-slider {
        padding: 0px;
    }
    .widget-body .slick-slide {
        display: flex;
        flex-direction: column;
        height: 200px;
    }

    <?php if(isset($feed_object->widget_background_color)) { ?>
        .widget-body, .slider-container.c-testimonial-slider {
            background-color: <?php echo esc_attr($feed_object->widget_background_color); ?>
        }
        .widget-body .rating-widget-part {
            background-color: <?php echo esc_attr($feed_object->widget_background_color); ?>
        }
        .widget-body .c-rating-widget-container {
            background-color: <?php echo esc_attr($feed_object->widget_background_color); ?>
        }
    <?php } ?>
    <?php if(isset($feed_object->text_color)) { ?>
        .widget-body a {
            color: <?php echo esc_attr($feed_object->text_color); ?>
        }
        .widget-body .c-rating-row-1 {
            color: <?php echo esc_attr($feed_object->text_color); ?>
        }
        .widget-body .c-rating-row-2 {
            color: <?php echo esc_attr($feed_object->text_color); ?>
        }
    <?php } ?>
    <?php if(isset($feed_object->writereview_button_color)) { ?>
        .widget-body .c-write-rev-outer-div {
            background-color: <?php echo esc_attr($feed_object->writereview_button_color); ?>
        }
    <?php } ?>
    <?php if(isset($feed_object->writereview_text_color)) { ?>
        .widget-body .c-write-rev-outer-div {
            color: <?php echo esc_attr($feed_object->writereview_text_color); ?>
        }
    <?php } ?>
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
            <?php foreach(array_slice($filteredReviews, 0, 8) as $key => $review) { ?>
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
            <?php if($count < count(array_slice($filteredReviews, 0, 8))){
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
            <?php foreach(array_slice($filteredReviews, 0, 8) as $key => $review) { ?>

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
            <?php if($count < count(array_slice($filteredReviews, 0, 8))){
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