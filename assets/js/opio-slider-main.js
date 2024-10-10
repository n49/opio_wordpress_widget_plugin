function getStarRatingJS(average) {
    var starColor = '#E6E8EB';
    if(average > 0.5) {
        starColor = '#ffc600';
    }
   
    // const starColor = (average > 0.5) ? '#ffc600' : '#E6E8EB';
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

// Function to set character limit and hide powered by text based on screen width
function adjustLayout() {
    // Function to set character limit based on screen width
    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

    // Set character limit based on screen width
    // var characterLimit = (screenWidth < 430) ? 75 : 90;

    // Update the content with the character limit for each review tile
    // var reviewTiles = document.querySelectorAll('.review-content');

    // reviewTiles.forEach(function(reviewContent) {
    //     var content = reviewContent.textContent.trim();
    //     if (content.length > characterLimit) {
    //         var truncatedContent = content.substring(0, characterLimit).trim();
    //         reviewContent.innerHTML = truncatedContent + ' <u>Read more</u>';
    //     }
    // });

    // hide powered by text based on the screen width
    // if(screenWidth < 1024) {
    //     // hide the text
    //     if(document.getElementById('powered-by-text')) {
    //         document.getElementById('powered-by-text').style.display = 'none'; 
    //     }
    // } else {
    //     if(document.getElementById('powered-by-text')) {
    //         document.getElementById('powered-by-text').style.display = 'block'; 
    //     }
    // }

}

async function openPhotoLightbox(reviewData) {

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
        lbPropertyLogo.style.backgroundImage = `url("../wp-content/plugins/widget-for-opio-reviews/assets/img/facebook-logo.png")`;
        lbPropertyLogo.style.width = '66px';
        lbPropertyLogo.style.height = '26px';
        lbPropertyLogo.style.minWidth = '66px';
        lbPropertyLogo.style.minHeight = '26px';
        lbPropertyLogo.style.marginTop = '-5px';
    } else if (reviewData.propertyInfo.name === 'google' || reviewData.propertyInfo.name === 'Google') {
        lbPropertyLogo.style.backgroundImage = 'url("../wp-content/plugins/widget-for-opio-reviews/assets/img/google-logo.svg")';
        lbPropertyLogo.style.width = '60px';
        lbPropertyLogo.style.height = '22px';
        lbPropertyLogo.style.minWidth = '60px';
        lbPropertyLogo.style.minHeight = '22px';
        lbPropertyLogo.style.marginTop = '0px';
    } else if (reviewData.propertyInfo.name === 'yelp') {
        lbPropertyLogo.style.backgroundImage = `url("../wp-content/plugins/widget-for-opio-reviews/assets/img/yelp-logo.png")`;
        lbPropertyLogo.style.width = '70px';
        lbPropertyLogo.style.height = '26px';
        lbPropertyLogo.style.minWidth = '70px';
        lbPropertyLogo.style.minHeight = '26px';
        lbPropertyLogo.style.marginTop = '0px';
    } else if (reviewData.propertyInfo.name === 'Trip Advisor') {
        lbPropertyLogo.style.backgroundImage = `url("../wp-content/plugins/widget-for-opio-reviews/assets/img/tripadvisor-logo.png")`;
        lbPropertyLogo.style.width = '110px';
        lbPropertyLogo.style.height = '25px';
        lbPropertyLogo.style.minWidth = '110px';
        lbPropertyLogo.style.minHeight = '25px';
        lbPropertyLogo.style.marginTop = '0px';
    } else {
        lbPropertyLogo.style.backgroundImage = `url("../wp-content/plugins/widget-for-opio-reviews/assets/img/opio-blue-logo.png")`;
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

        byCommentName.innerHTML = `<span>By</span>&nbsp;<span class="lb-by-name-span">${commenterName} </span>&nbsp;on ${moment(reviewData.comments[0].dateCreated).format("MMM D, YYYY")}`;
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

(function ($) {
    $(document).ready(function () {

    // Initialize the Slick slider
    var c_slider = $('.c-testimonial-slider').slick({
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

    $('.c-testimonial-slider').slick('refresh');

    // Move to the next slide on clicking the ">" button
    $('.c-slider-button.right').click(function () {
        c_slider.slick('slickNext');
    });

    // Move to the previous slide on clicking the "<" button
    $('.c-slider-button.left').click(function () {
        c_slider.slick('slickPrev');
    });
    });
})(jQuery);

(function ($) {
    $(document).ready(function () {

    // Initialize the Slick slider
    var v_slider = $('.v-testimonial-slider').slick({
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

    $('.v-testimonial-slider').slick('refresh');

    // Move to the next slide on clicking the ">" button
    $('.v-slider-button.right').click(function () {
        v_slider.slick('slickNext');
    });

    // Move to the previous slide on clicking the "<" button
    $('.v-slider-button.left').click(function () {
        v_slider.slick('slickPrev');
    });
    });
})(jQuery);

(function ($) {
    $(document).ready(function () {

    // Initialize the Slick slider
    var h_slider = $('.h-testimonial-slider').slick({
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
                breakpoint: 768, // Adjusted breakpoint for better mobile experience
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

    $('.h-testimonial-slider').slick('refresh');

    $('.slider-button.right').click(function () {
        h_slider.slick('slickNext');
    });

    // Move to the previous slide on clicking the "<" button
    $('.slider-button.left').click(function () {
        h_slider.slick('slickPrev');
    });
    });
})(jQuery);

// Close lightbox when clicking outside the video container
window.addEventListener('click', function (event) {
    if (event.target === document.getElementById('photo-lightbox')) {
        closePhotoLightbox();
    }
});

// Close lightbox on pressing the 'Esc' key
window.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closePhotoLightbox();
    }
});

// Call the function on page load and window resize
// window.onload = adjustLayout;
// window.onresize = adjustLayout;
