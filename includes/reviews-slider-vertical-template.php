<!-- reviews-slider-template.php -->
<?php

function randomColor($str = null) {
    $max = 180;
    $r = $str ? ord($str[strlen($str) - 1]) : mt_rand(0, $max);
    $g = $str ? ord($str[strlen($str) - 2]) : mt_rand(0, $max);
    $b = $str ? ord($str[strlen($str) - 3]) : mt_rand(0, $max);

    return "rgb($r, $g, $b)";
}
// Example usage:
$randomColor = randomColor("example");
// Make API call to get reviews
$api_url = 'https://op.io/api/entities/mixreviews?entityId=jhrtqujzw0ystxyfk';
$api_headers = [
    'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOjE2NDQzNDY2MzEsInVzZXJfaWQiOiJrdHN4dzlramxkZ3RzZDNqMSIsImV4cCI6MTI5NzY0NDM0NjYzMX0.FZeMMsZlix1eQ1aJFmQ0MV_L_ezFb4RhrqCIhceTT-w',
];

$api_response = wp_remote_post($api_url, [
    'headers' => $api_headers,
    'body'    => '', // Your request body, if any
]);

// Check if the API call was successful
if (!is_wp_error($api_response) && $api_response['response']['code'] === 200) {
    // Decode the JSON response
    $response = json_decode($api_response['body'], true);
    $business = $response;
    $reviews = $response['reviews'];

    $filteredReviews = [];
    foreach ($reviews as $review) {
        if (strlen($review['content']) > 10) {
            $filteredReviews[] = $review;
        }
    }

    $writeReviewUrl = 'https://op.io/write-review/5734f48a0b64d7382829fdf7/'.$business["_id"];
    if(isset($business["landingPageUsername"])) {
        $writeReviewUrl = 'https://' .$business["landingPageUsername"]. '.op.io';
    }
}

?>


<div class="widget-body horizontal">
    <div style="display: flex; background: #A9B7BF; padding: 10px 0px 10px 10px; color: white;">
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 14px; margin-top: -6px;">Reviews for</span>
            <?php if (strlen($business['name']) > 14) { ?>
                <span style="font-size: 22px; margin-top: -7px;"><?php echo esc_attr(mb_substr($business['name'], 0, 14, 'UTF-8')); ?>...</span>
            <?php } else { ?>
                <span style="font-size: 22px; margin-top: -7px;"><?php echo esc_attr($business['name']); ?></span>
            <?php } ?>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center;margin-left: 7px; margin-top: -7px;">
            <span style="font-size: 13px;">Powered by</span>
            <div style="background-image: url(&quot;https://op.io/dashboard/graphics/opio-logo-white.png&quot;); display: flex; align-items: center; min-width: 94px; min-height: 30px; background-size: contain; background-position: center center; background-repeat: no-repeat;"></div>
        </div>
    </div>
    <div>
    <div style="display: flex; flex-direction: column; padding: 10px; margin-top: -5px;">
        <div style="display: flex;">
            <div style="overflow: hidden; position: relative; margin-top: 16px;">
                <style>
                    .react-stars-04811029757080685:before {
                        position: absolute;
                        overflow: hidden;
                        display: block;
                        z-index: 1;
                        top: 0;
                        left: 0;
                        width: 50%;
                        content: attr(data-forhalf);
                        color: <?php echo esc_attr($starColor); ?>;
                    }
                </style>
                <!-- Refactor this later -->
                <?php if($business['aggregateRating'][3]['average'] > 0.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } else if($business['aggregateRating'][3]['average'] == 0.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                <?php } else { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } ?>

                <?php if($business['aggregateRating'][3]['average'] > 1.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } else if($business['aggregateRating'][3]['average'] == 1.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                <?php } else { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } ?>

                <?php if($business['aggregateRating'][3]['average'] > 2.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } else if($business['aggregateRating'][3]['average'] == 2.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                <?php } else { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } ?>

                <?php if($business['aggregateRating'][3]['average'] > 3.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } else if($business['aggregateRating'][3]['average'] == 3.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                <?php } else { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } ?>

                <?php if($business['aggregateRating'][3]['average'] > 4.5) { ?>
                <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } else if($business['aggregateRating'][3]['average'] == 4.5) { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                <?php } else { ?>
                    <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width: 24px; height:  24px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                <?php } ?>
            </div>
            <div style="display: flex; margin-left: 42px;"><span style="display: flex; margin-top: 0px; font-size: 50px; font-weight: 700;"><?php echo esc_attr($business['aggregateRating']['3']['average']); ?>/5</span></div>
        </div>
        <div style="display: flex;margin-top: -36px;text-align: center;justify-content: start;">
            <span style="display: flex;font-size: 14px;align-items: flex-end;"><a href="https://www.xyzstorage.com/locations/scarborough">See all <?php echo esc_attr($business['aggregateRating']['3']['total']); ?> Reviews</a></span>
        </div>   
    </div>
    </div>
    <div class="slider-container testimonial-slider vertical-slider">
    <?php

            foreach (array_slice($filteredReviews, 0, 8) as $review) {
                ?>
                <div class="testimonial-slide review-tile-vertical">
                    <div id=<?php echo esc_attr($review["_id"]);?> style="display: flex; position: relative;">
                        <div style="vertical-align: top; padding-right: 10px;">
                            <div id="outer" style="display: inline-block;">
                            <?php if(($review['propertyId'] === 1 || $review['propertyId'] === 2) && isset($review['user']['userPic'])) { ?>
                                <div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: rgb(99, 114, 130); font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url(&quot;<?php echo esc_attr($review['user']['userPic']);?>&quot;);">
                                </div>  
                            <?php } else if(isset($review['user']['userPic']) && isset($review['user']['userPic']['imageId'])) { ?>
                                <div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: rgb(99, 114, 130); font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url(&quot;https://images.files.ca/200x200/<?php echo esc_attr($review['user']['userPic']['imageId']);?>.jpg?nocrop=1&quot;);">
                                </div>  
                            <?php } else if($review['user']['firstName']) { ?>
                                <div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: rgb(99, 114, 130); font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-color: <?php echo esc_attr(randomColor(mb_substr(ucfirst($review['user']["firstName"]), 0, 1, 'utf-8'))); ?>"><?php echo esc_attr(mb_substr(ucfirst($review['user']["firstName"]), 0, 1, 'utf-8')); ?></div>
                            <?php } ?>    
                            </div>
                            <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                                <div style="background-image: url(&quot;https://s3.amazonaws.com/files-image-service/original/d38749a7cc26b5a3932ff8906f10f138.png&quot;); position: absolute; right: -6px; top: -2px; display: flex; text-align: right; min-width: 70px; min-height: 24px; background-size: contain; background-position: center center; background-repeat: no-repeat; margin-right: 0px;"></div>
                            <?php } else if($review['propertyInfo']['name'] === 'google') { ?>
                                <div style="background-image: url(&quot;https://op.io/dashboard/graphics/google-slider-logo.svg&quot;); position: absolute; right: -20px; top: 2px; display: flex; text-align: right; min-width: 90px; min-height: 20px; background-size: contain; background-position: center center; background-repeat: no-repeat; margin-right: 0px;"></div>
                            <?php } else { ?>
                                <div style="background-image: url(&quot;https://op.io/dashboard/graphics/opio-blue-new-1x.png&quot;); position: absolute; right: -30px; top: 2px; display: flex; text-align: right; min-width: 100px; min-height: 24px; background-size: contain; background-position: center center; background-repeat: no-repeat; margin-right: 0px;"></div>
                            <?php } ?>  
                        </div>
                        <div style="display: flex; flex-direction: column;">
                            <?php if($review['propertyInfo']['name'] === 'facebook') { ?>
                                <?php if($review['rating'] === "positive") { ?>
                                    <div style="display: inline-flex; margin-bottom: 5px; align-items: center; vertical-align: middle;">
                                        <img style="padding-right: 5px; width: 20px; height: 20px; padding-top: 2px;" src="https://op.io/dashboard/graphics/facebook-recommends.png" />
                                        <p style="margin: 0; font-weight: 700; font-size: 12px; white-space: nowrap; color: #637282;">Recommends</p>
                                    </div>
                                <?php } else { ?>
                                    <div style="display: inline-flex; margin-bottom: 5px; align-items: center; vertical-align: middle;">
                                        <img style="padding-right: 5px; width: 20px; height: 20px; padding-top: 2px;" src="https://op.io/dashboard/graphics/facebook-recommends-grey.png" />
                                        <p style="margin: 0; font-weight: 700; font-size: 13px; white-space: nowrap; color: #637282;">Doesn't Recommend</p>
                                    </div>
                                <?php } ?>  
                            <?php } else { ?>
                                <div style="overflow: hidden; position: relative;">
                                        <style>
                                            .react-stars-04811029757080685:before {
                                                position: absolute;
                                                overflow: hidden;
                                                display: block;
                                                z-index: 1;
                                                top: 0;
                                                left: 0;
                                                width: 50%;
                                                content: attr(data-forhalf);
                                                color: <?php echo esc_attr($starColor); ?>;
                                            }
                                        </style>
                                        <!-- Refactor this later -->
                                        <?php if($review['rating'] > 0.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } else if($review['rating'] == 0.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                                        <?php } else { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } ?>

                                        <?php if($review['rating'] > 1.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } else if($review['rating'] == 1.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                                        <?php } else { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } ?>

                                        <?php if($review['rating'] > 2.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } else if($review['rating'] == 2.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                                        <?php } else { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } ?>

                                        <?php if($review['rating'] > 3.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } else if($review['rating'] == 3.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                                        <?php } else { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } ?>

                                        <?php if($review['rating'] > 4.5) { ?>
                                        <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: #ffc600;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } else if($review['rating'] == 4.5) { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: #ffc600;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>
                                        <?php } else { ?>
                                            <div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>
                                        <?php } ?>
                                </div>
                            <?php } ?>
                            <div style="font-size: 12px; color: rgb(99, 114, 130);"><span class="reviewer-name"><?php echo esc_attr(mb_substr($review['user']['firstName'], 0, 15, 'UTF-8'));?></span> on <?php echo esc_attr(date('M d, Y', $review['dateCreated']/1000)); ?></div>
                        </div>

                    </div>
                    <!-- <div class="location-name"><?php echo esc_attr($review['entityInfo']['address']['city']); ?></div> -->
                    <div class="review-content">
                    <?php if (strlen($review['content']) > 65) { ?>
                        <?php echo esc_attr(mb_substr($review['content'], 0, 65, 'UTF-8')); ?>...<u>Read more</u>
                    <?php } else { ?>
                        <?php echo esc_attr($review['content']); ?>
                    <?php } ?>
                    </div>
                </div>
        <?php
            }
    ?>
    </div>
    <div style="display: flex;background: #ffffff;height: 80px;align-items: flex-end;justify-content: center;gap: 10px;flex-direction: row; padding-right: 30px; padding: 15px;">
        <div class="slider-navigation">
            <div class="slider-button left">&lt;</div>
            <!-- Your slider content goes here -->
            <div class="slider-button right">&gt;</div>
        </div>
        <div style="display: flex; margin-left: 5px;">
            <div id="some id" style="width: 100%; display: flex; flex-direction: row; justify-content: center; padding: 0px 8px; text-align: center;">
                <a style="text-decoration: none; color:inherit" target="_blank" href="<?php echo esc_attr($writeReviewUrl); ?>">
                <div style="border-radius: 2px; display: inline-block; height: 40px; line-height: 40px; margin-left: 0px; text-align: center; cursor: pointer; padding: 0px; font-weight: 400; font-size: 14px; color: #ffffff; border: none; background-color: #0078ca; width: 150px; position: relative; transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1); user-select: none;"><div style="display: inline-block; position: relative; height: 100%;">
                    <span>Write a review</span>
                </div></div></a>
            </div>
        </div>
    </div>

    <div id="lightbox">
        <div class="lightbox-container">
            <video id="VisaChipCardVideo" width="600">
                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
                <!-- Fallback message for browsers that don't support the video tag -->
                Your browser does not support the video tag.
            </video>
            <div class="close-button" onclick="closeLightbox()">✖</div>
        </div>
    </div>

    <div id="photo-lightbox">
        <div class="lightbox-container">
            <div class="lightbox-photo-tile"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/Wildlife_at_Maasai_Mara_%28Lion%29.jpg/800px-Wildlife_at_Maasai_Mara_%28Lion%29.jpg" alt="Photo 1"></div>
            <div class="lightbox-photo-tile"><img src="https://static.toiimg.com/thumb/msid-89967601,width-748,height-499,resizemode=4,imgsize-45994/Celebration-of-the-wild.jpg" alt="Photo 2"></div>
            <div class="close-button" onclick="closePhotoLightbox()">✖</div>
        </div>
    </div>
</div>

<!-- Include jQuery and Slick slider scripts -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<style>
    .horizontal {
        display: flex;
        height: 500px;
        width: 300px;
        flex-direction: column;
        border: 2px solid #A9B7BF;
        border-radius: 3px;
        margin: 0;
    }
    .testimonial-slider.vertical-slider {
        padding: 0px;
        height: 360px;
        overflow: hidden;
        margin-top: 5px;
    }
    .slick-vertical .slick-slide {
        height: 140px;
        border-top: 2px solid #E6E6E6;
        border-bottom: 2px solid #E6E6E6;
    }

    .vertical-divider {
        height: 40%;
        width: 1px;
        background-color: #9b9b9b; /* Change the color as needed */
        margin: 0px 10px;
    }
    .slider-navigation {
        display: flex;
        align-items: center;
        margin-top: 3px;
        margin: 0 auto;
    }

    .slider-button {
        width: 34px;
        height: 34px;
        padding: 18px;
        background-color: #F5F5F7;
        color: #919191;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin: 0 4px;
        font-size: 16px;
    }
</style>

<script>
    function openLightbox() {
        document.getElementById('lightbox').style.display = 'flex';
        document.getElementById('VisaChipCardVideo').play();
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.getElementById('VisaChipCardVideo').pause();
        document.getElementById('VisaChipCardVideo').load();
        document.getElementById('VisaChipCardVideo').removeAttribute('controls');
    }

    function openPhotoLightbox() {
        document.getElementById('photo-lightbox').style.display = 'flex';
    }

    function closePhotoLightbox() {
        document.getElementById('photo-lightbox').style.display = 'none';
    }

    $(document).ready(function () {
        // Initialize the Slick slider
        var slider = $('.testimonial-slider').slick({
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
            responsive: [
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 2,
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
        $('.slider-button.right').click(function () {
            slider.slick('slickNext');
        });

        // Move to the previous slide on clicking the "<" button
        $('.slider-button.left').click(function () {
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
