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

        $fullStar = '<div class="rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
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

        $fullStar = '<div class="rating-stars-wd-c"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' . $starColor . ';}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';
        
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
    $api_url = 'https://op.io/api/entities/reviews-slider?cache=renew&hideWidget=true&hideFeed=true';

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
                            <?php if($review['propertyId'] === 2 && isset($review['user']['userPic'])) { ?>
                                <div class="reviewer-avatar" data-fb-avatar="<?php echo esc_attr($review['user']['userPic']); ?>">
                                </div>
                            <?php } else if(($review['propertyId'] === 1 || $review['propertyId'] === 3 || $review['propertyId'] === 5) && isset($review['user']['userPic'])) { ?>
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
                                (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) ||
                                (isset($review['embeds']) && is_array($review['embeds']) && count($review['embeds']) > 0)) { ?>
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
                                    <img class="review-img" src="https://op.io/dashboard/api/reviews/get-image/<?php echo esc_attr($image['imageId']); ?>?width=400&height=400">
                                </div>
                            <?php } ?>
                        <?php } ?>
                            <?php if(isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) { ?>
                                <div class="video-icon">
                                    <div class="play-button"></div>
                                </div>
                            <?php } ?>
                            <?php if(isset($review['embeds']) && is_array($review['embeds']) && count($review['embeds']) > 0) { ?>
                                <?php foreach (array_slice($review['embeds'], 0, 3) as $embed) {
                                    $thumbUrl = isset($embed['thumbnailUrl']) ? $embed['thumbnailUrl'] : '';
                                    $platform = isset($embed['platform']) ? strtolower(trim($embed['platform'])) : '';
                                    if(empty($thumbUrl) && $platform === 'youtube' && isset($embed['videoId'])) {
                                        $thumbUrl = 'https://img.youtube.com/vi/' . $embed['videoId'] . '/hqdefault.jpg';
                                    }
                                ?>
                                    <?php if($platform === 'tiktok') { ?>
                                    <div class="review-img" style="display: inline-flex; align-items: center; justify-content: center; background-color: #000;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>
                                    </div>
                                    <?php } else if($platform === 'instagram') { ?>
                                    <div class="review-img" style="display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </div>
                                    <?php } else { ?>
                                    <div style="position: relative; display: inline-block;">
                                        <img class="review-img" src="<?php echo esc_attr($thumbUrl); ?>" style="background-color: #f0f0f0; object-fit: cover; object-position: center;">
                                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 20px; height: 20px; background: rgba(225,232,237,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="rgb(99,114,130)" style="margin-left: 2px;"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                    <?php } ?>
                                <?php } ?>
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
                            (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) ||
                            (isset($review['embeds']) && is_array($review['embeds']) && count($review['embeds']) > 0)) { ?>
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
                                <img class="review-img" src="https://op.io/dashboard/api/reviews/get-image/<?php echo esc_attr($image['imageId']); ?>?width=400&height=400">
                            </div>
                        <?php } ?>
                    <?php } ?>
                        <?php if(isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0) { ?>
                            <div class="video-icon">
                                <div class="play-button"></div>
                            </div>
                        <?php } ?>
                        <?php if(isset($review['embeds']) && is_array($review['embeds']) && count($review['embeds']) > 0) { ?>
                            <?php foreach (array_slice($review['embeds'], 0, 3) as $embed) {
                                $thumbUrl = isset($embed['thumbnailUrl']) ? $embed['thumbnailUrl'] : '';
                                if(empty($thumbUrl) && isset($embed['platform']) && $embed['platform'] === 'youtube' && isset($embed['videoId'])) {
                                    $thumbUrl = 'https://img.youtube.com/vi/' . $embed['videoId'] . '/hqdefault.jpg';
                                }
                            ?>
                                <div style="position: relative; display: inline-block;">
                                    <img class="review-img" src="<?php echo esc_attr($thumbUrl); ?>" style="background-color: #f0f0f0; object-fit: cover; object-position: center;">
                                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 20px; height: 20px; background: rgba(225,232,237,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="rgb(99,114,130)" style="margin-left: 2px;"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            <?php } ?>
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

<script>
    (function() {
        // Set background images for all Facebook avatars
        var avatars = document.querySelectorAll('.reviewer-avatar[data-fb-avatar]');
        avatars.forEach(function(avatar) {
            var url = avatar.getAttribute('data-fb-avatar');
            if (url) {
                avatar.style.backgroundImage = 'url("' + url + '")';
            }
        });
    })();
</script>

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

<?php if(isset($feed_object->schema_enabled) && $feed_object->schema_enabled == 'yes') {
    $schema_url = 'https://op.io/review-schema.json/?entid=' . $feed_object->biz_id;
    if($review_type === 'orgfeed') {
        $schema_url = 'https://op.io/review-schema.json/?orgid=' . $feed_object->org_id;
    }
    if(isset($feed_object->schema_type) && $feed_object->schema_type == 'local') {
        $schema_url .= '&type=local';
    }
    $schema_response = wp_remote_get($schema_url, ['timeout' => 5]);
    if(!is_wp_error($schema_response) && $schema_response['response']['code'] === 200) {
        $schema_json = $schema_response['body'];
        if(!empty($schema_json) && $schema_json !== '{}' && $schema_json !== 'null') {
?>
<!-- JSON schema from op.io API -->
<script type="application/ld+json">
<?php echo $schema_json; ?>
</script>
<?php
        }
    }
} ?>

</div>

<?php 

$output = ob_get_clean();
echo $output;
?>