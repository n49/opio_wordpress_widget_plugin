<!-- reviews-slider-horizontal-carousel-template.php -->

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

<div class="widget-body carousel">
    <div class="slider-container testimonial-slider">
    <?php
        // Display the first 9 reviews
        foreach (array_slice($filteredReviews, 0, 8) as $review) {
            ?>
            <?php $currentReview = $review; ?>
            <div class="testimonial-slide review-tile" id=<?php echo esc_attr($review["_id"]);?> data-review-index="<?php echo esc_attr($index); ?>" onclick="openPhotoLightbox(<?php echo esc_attr(json_encode($currentReview)); ?>)">
                <div style="display: flex; position: relative;" >
                    <div style="vertical-align: top;">
                        <div class="avatar-container">
                            <?php if(($review['propertyId'] === 1 || $review['propertyId'] === 2) && isset($review['user']['userPic'])) { ?>
                                <div class="reviewer-avatar" style="background-image: url(&quot;<?php echo esc_attr($review['user']['userPic']);?>&quot;);">
                                </div>  
                            <?php } else if(isset($review['user']['userPic']) && isset($review['user']['userPic']['imageId'])) { ?>
                                <div class="reviewer-avatar" style="background-image: url(&quot;https://images.files.ca/200x200/<?php echo esc_attr($review['user']['userPic']['imageId']);?>.jpg?nocrop=1&quot;);">
                                </div>  
                            <?php } else if($review['user']['firstName']) { ?>
                                <div class="reviewer-avatar" style="background-color: <?php echo esc_attr(randomColor()); ?>"><?php echo esc_attr(mb_substr(ucfirst($review['user']["firstName"]), 0, 1, 'utf-8')); ?></div>
                            <?php } ?>    
                            <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                                <div class="c-facebook-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/facebook-logo.png'; ?>&quot;);"></div>
                            <?php } else if($review['propertyInfo']['name'] === 'google') { ?>
                                <div class="c-google-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/google-logo.svg'; ?>&quot;);"></div>
                            <?php } else { ?>
                                <div class="c-opio-logo" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>&quot;);"></div>
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
                        <div class="reviewer-name-container"><span class="reviewer-name"><?php echo esc_attr($review['user']['firstName']);?> <?php echo esc_attr($review['user']['lastName']);?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                    </div>

                </div>
                <?php if($review_type === 'orgfeed') { ?>
                    <div class="location-name"><?php echo esc_attr($review['entityInfo']['name']); ?></div>
                        <?php if((isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) || 
                                (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0)) { ?>
                        <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>" style="margin-top: 0px;">
                            <?php if (strlen($review['content']) > 55) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, 55, 'UTF-8')); ?> <u>Read more</u>
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
                                <?php if (strlen($review['content']) > 55) { ?>
                                    <?php echo esc_attr(mb_substr($review['content'], 0, 55, 'UTF-8')); ?> <u>Read more</u>
                                <?php } else { ?>
                                    <?php echo esc_attr($review['content']); ?>
                                <?php } ?>
                            </div>
                            <div class="tile-tag-container">
                                <?php foreach (array_slice($review['taggedEmployees'], 0, 4) as $index => $emp) { ?>
                                    <div class="review-tagged-emps">
                                        <?php if(isset($emp["userPic"]["imageId"])) { ?>
                                            <div class="emp-avatar" style="background-image: url(&quot;https://images.files.ca/200x200/<?php echo esc_attr($emp["userPic"]["imageId"]); ?>.jpg?nocrop=1&quot;);"></div>
                                        <?php } else { ?>
                                        <?php if(isset($emp["userPic"]) && $emp["userPic"] != "") { ?>
                                            <div class="emp-avatar" style="background-image: url(&quot;<?php echo esc_attr($emp["userPic"]); ?>&quot;);"></div>
                                        <?php } else { ?>
                                            <div class="emp-avatar" style="background-color: #dddddd"><?php echo esc_attr(mb_substr(ucfirst($emp["firstName"]), 0, 1, 'utf-8')); ?></div>
                                        <?php } } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>" style="margin-top: 0px;">
                            <?php if (strlen($review['content']) > 140) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, 140, 'UTF-8')); ?> <u>Read more</u>
                            <?php } else { ?>
                                <?php echo esc_attr($review['content']); ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                <?php } else { ?>
                    <?php if((isset($review['images']) && is_array($review['images']) && count($review['images']) > 0) || 
                            (isset($review['videos']) && is_array($review['videos']) && count($review['videos']) > 0)) { ?>
                    <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>">
                        <?php if (strlen($review['content']) > 110) { ?>
                            <?php echo esc_attr(mb_substr($review['content'], 0, 110, 'UTF-8')); ?> <u>Read more</u>
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
                            <?php if (strlen($review['content']) > 110) { ?>
                                <?php echo esc_attr(mb_substr($review['content'], 0, 110, 'UTF-8')); ?> <u>Read more</u>
                            <?php } else { ?>
                                <?php echo esc_attr($review['content']); ?>
                            <?php } ?>
                        </div>
                        <div class="tile-tag-container">
                            <?php foreach (array_slice($review['taggedEmployees'], 0, 4) as $index => $emp) { ?>
                                <div class="review-tagged-emps">
                                    <?php if(isset($emp["userPic"]["imageId"])) { ?>
                                        <div class="emp-avatar" style="background-image: url(&quot;https://images.files.ca/200x200/<?php echo esc_attr($emp["userPic"]["imageId"]); ?>.jpg?nocrop=1&quot;);"></div>
                                    <?php } else { ?>
                                    <?php if(isset($emp["userPic"]) && $emp["userPic"] != "") { ?>
                                        <div class="emp-avatar" style="background-image: url(&quot;<?php echo esc_attr($emp["userPic"]); ?>&quot;);"></div>
                                    <?php } else { ?>
                                        <div class="emp-avatar" style="background-color: #dddddd"><?php echo esc_attr(mb_substr(ucfirst($emp["firstName"]), 0, 1, 'utf-8')); ?></div>
                                    <?php } } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="review-content" id="reviewContent-<?php echo esc_attr($index); ?>">
                        <?php if (strlen($review['content']) > 160) { ?>
                            <?php echo esc_attr(mb_substr($review['content'], 0, 160, 'UTF-8')); ?> <u>Read more</u>
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
            <span id="powered-by-text" class="c-pwd-span">Powered by</span><div class="c-pwd-div" style="background-image: url(&quot;<?php echo esc_url(OPIO_ASSETS_URL) . 'img/opio-blue-logo.png'; ?>&quot;);"></div>
        </div>
        <div class="c-vertical-divider"></div>
        <div class="c-rating-row-2">
            <div style="display: flex;">
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
            <div class="c-slider-button left">&lt;</div>
            <!-- Your slider content goes here -->
            <div class="c-slider-button right">&gt;</div>
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
                </div>
                <div class="lb-review-property">
                    <div id="lb-property-logo" class="lb-property-logo"></div>
                </div>    
            </div>
        </div>
        <div class="close-button" onclick="closePhotoLightbox()">x</div>
    </div>
</div>

<!-- Roboto fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

<!-- Include jQuery and Slick slider scripts -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<style>
    .slider-container.testimonial-slider {
        padding: 20px;
    }
    .slick-slide {
        display: flex;
        flex-direction: column;
        height: 200px;
    }
</style>

<!-- JSON schema starts-->

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

<!-- JSON schema ends-->

<script>
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
    }

    // Function to set character limit and hide powered by text based on screen width
    function adjustLayout() {
        // Function to set character limit based on screen width
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

        // Set character limit based on screen width
        var characterLimit = (screenWidth < 430) ? 75 : 90;

        // Update the content with the character limit for each review tile
        var reviewTiles = document.querySelectorAll('.review-content');

        reviewTiles.forEach(function(reviewContent) {
            var content = reviewContent.textContent.trim();
            if (content.length > characterLimit) {
                var truncatedContent = content.substring(0, characterLimit).trim();
                reviewContent.innerHTML = truncatedContent + ' <u>Read more</u>';
            }
        });

        // hide powered by text based on the screen width
        if(screenWidth < 1024) {
            // hide the text
            document.getElementById('powered-by-text').style.display = 'none'; 
        } else {
            document.getElementById('powered-by-text').style.display = 'block'; 
        }

    }

    // Call the function on page load and window resize
    window.onload = adjustLayout;
    window.onresize = adjustLayout;

    async function openPhotoLightbox(reviewData) {

        // var review = JSON.parse(reviewData);
        document.getElementById('photo-lightbox').style.display = 'flex';

        // set data to lightbox
        var imageUrl = reviewData['user']['userPic'];

        // Update reviewer avatar
        var avatarImage = document.getElementById('avatar-image');

        if ((reviewData.propertyId === 1 || reviewData.propertyId === 2)) {
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
    }

    function closePhotoLightbox() {
        var photoContainer = document.getElementById(`lb-photo-container`);
        photoContainer.innerHTML = '';
        var mediaContainer = document.getElementById('lb-video-container');
        mediaContainer.innerHTML = '';
        document.getElementById('photo-lightbox').style.display = 'none';
        // Clear the cookie
        document.cookie = 'reviewIndex=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    }

    $(document).ready(function () {
        // Initialize the Slick slider
        var slider = $('.testimonial-slider').slick({
            autoplay: true,
            autoplaySpeed: 2000,
            speed: 600,
            draggable: true,
            infinite: true,
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: false,
            dots: false,
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                    }
                },
                {
                breakpoint: 767, // Adjusted breakpoint for better mobile experience
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 575,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ]
        });

        // Move to the next slide on clicking the ">" button
        $('.c-slider-button.right').click(function () {
            slider.slick('slickNext');
        });

        // Move to the previous slide on clicking the "<" button
        $('.c-slider-button.left').click(function () {
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
