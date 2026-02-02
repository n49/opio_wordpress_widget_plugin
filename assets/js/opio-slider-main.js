function getStarRatingJS(average) {
    var starColor = '#E6E8EB';
    if(average > 0.5) {
        starColor = '#ffc600';
    }
   
    // const starColor = (average > 0.5) ? '#ffc600' : '#E6E8EB';
    const starGrey = '#E6E8EB';

    const fullStar = '<div class="lb-rating-stars"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: ' + starColor + ';}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>';

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

// Update thumbnail selection state in lightbox
function updateLbThumbnailSelection(revId, selectedId) {
    var container = document.querySelector('#media-thumbs-' + revId);
    if (!container) return;

    var thumbs = container.querySelectorAll('.media-thumb');
    thumbs.forEach(function(thumb) {
        thumb.style.border = '2px solid transparent';
    });

    var selected = container.querySelector('.media-thumb[data-media-id="' + selectedId + '"]');
    if (selected) {
        selected.style.border = '2px solid #1976d2';
    }
}

function displayLargeImage(imageId, revId) {
    var photoContainer = document.querySelector(`#lb-photo-container`);
    if (!photoContainer) {
        // Fallback for pages without lightbox (e.g., review feed)
        var elem = document.querySelector(`#largerevimg-${revId}`);
        if (!elem) return;
        var imgUrl = 'https://images.files.ca/800x800/' + imageId + '.jpg?nocrop=1';
        elem.innerHTML = '<div style="display: inline-block; width: 98.5%; height: 400px; background-color: #f0f0f0; margin: 5px; text-align: center; display: flex; align-items: center; justify-content: center;">Loading...</div>';
        var img = new Image();
        img.onload = function() {
            var containerWidth = elem.offsetWidth ? elem.offsetWidth * 0.985 : 300;
            var aspectRatio = img.naturalHeight / img.naturalWidth;
            var calculatedHeight = Math.min(containerWidth * aspectRatio, 400);
            var isPortrait = img.naturalHeight > img.naturalWidth;
            var bgPosition = isPortrait ? 'left center' : 'center center';
            elem.innerHTML = '<div style="display: inline-block; width: 98.5%; height: ' + calculatedHeight + 'px; background-position: ' + bgPosition + '; background-size: contain; background-repeat: no-repeat; margin: 5px; text-align: center; background-image: url(&quot;' + imgUrl + '&quot;); opacity: 1; transition: opacity 1s ease 0s; border-radius: 4px;"></div>';
        };
        img.onerror = function() {
            elem.innerHTML = '<div style="display: inline-block; width: 98.5%; height: 400px; background-position: center center; background-size: contain; background-repeat: no-repeat; margin: 5px; text-align: center; background-image: url(&quot;' + imgUrl + '&quot;); opacity: 1; transition: opacity 1s ease 0s;"></div>';
        };
        img.src = imgUrl;
        return;
    }
    photoContainer.style.display = 'block';

    // Select all elements with the class name 'photo-a-tag'
    var photoATags = document.querySelectorAll('.photo-a-tag');

    // Iterate through each element and change its display property
    photoATags.forEach(function(photoATag) {
        photoATag.style.display = 'inline-block';
    });

    var elem = document.querySelector(`#largerevimg-${revId}`);
    if (!elem) return;
    elem.style.display = 'flex';
    var imgUrl = 'https://images.files.ca/800x800/' + imageId + '.jpg?nocrop=1';
    elem.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 400px; background-color: #f0f0f0;">Loading...</div>';
    var img = new Image();
    img.onload = function() {
        var containerWidth = elem.offsetWidth || 300;
        var aspectRatio = img.naturalHeight / img.naturalWidth;
        var calculatedHeight = Math.min(containerWidth * aspectRatio, 400);
        var isPortrait = img.naturalHeight > img.naturalWidth;
        var bgPosition = isPortrait ? 'left center' : 'center center';
        elem.innerHTML = `
            <div onClick="hideLargeImage('${revId}')" class="lb-large-img" style="background-image: url('${imgUrl}'); height: ${calculatedHeight}px; background-size: contain; background-position: ${bgPosition}; background-repeat: no-repeat;"></div>
            <div>
                <div class="lb-large-img-div"></div>
                <div style="display: none;"></div>
            </div>`;
    };
    img.onerror = function() {
        elem.innerHTML = `
            <div onClick="hideLargeImage('${revId}')" class="lb-large-img" style="background-image: url('${imgUrl}'); background-size: contain; background-position: center center; background-repeat: no-repeat;"></div>
            <div>
                <div class="lb-large-img-div"></div>
                <div style="display: none;"></div>
            </div>`;
    };
    img.src = imgUrl;
}

function displayEmbed(embed, revId) {
    try {
        if (!embed || typeof embed !== 'object') return;
        var elem = document.querySelector(`#largerevimg-${revId}`);
        if (!elem) return;

        var photoContainer = document.querySelector(`#lb-photo-container`);
        if (photoContainer) photoContainer.style.display = 'block';

        elem.style.display = 'flex';
        var maxHeight = 400;
        var containerWidth = elem.offsetWidth || 300;
        var platform = (embed.platform || 'youtube').toLowerCase().trim();

        if (platform === 'youtube' && embed.videoId && typeof embed.videoId === 'string') {
            var videoId = embed.videoId.trim();
            if (!videoId) return;
            var isShort = embed.embedType === 'short' || (embed.url && typeof embed.url === 'string' && embed.url.indexOf('/shorts/') !== -1);
            var iframeHtml;
            if (isShort) {
                var shortWidth = maxHeight * (9 / 16);
                iframeHtml = '<div style="display: inline-block; width: ' + shortWidth + 'px; height: ' + maxHeight + 'px; margin: 5px; position: relative; background: #000; border-radius: 4px; overflow: hidden; vertical-align: top;"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + videoId + '?autoplay=1&modestbranding=1&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0;"></iframe></div>';
            } else {
                var videoHeight = Math.min(containerWidth * 0.5625, maxHeight);
                iframeHtml = '<div style="width: 100%; height: ' + videoHeight + 'px; margin: 5px 0; position: relative; background: #000; border-radius: 4px; overflow: hidden;"><iframe width="100%" height="100%" src="https://www.youtube.com/embed/' + videoId + '?autoplay=1&modestbranding=1&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0;"></iframe></div>';
            }
            elem.innerHTML = iframeHtml;
        } else if (platform === 'tiktok') {
            var tiktokVideoId = embed.videoId || '';
            if (!tiktokVideoId && embed.url) {
                var tiktokMatch = embed.url.match(/video\/(\d+)/);
                if (tiktokMatch) tiktokVideoId = tiktokMatch[1];
            }
            if (!tiktokVideoId) {
                if (embed.url) window.open(embed.url, '_blank');
                return;
            }
            var tiktokWidth = window.innerWidth < 768 ? Math.min(window.innerWidth * 0.985, 380) : 340;
            tiktokWidth = Math.max(tiktokWidth, 320);
            var tiktokHeight = window.innerWidth < 768 ? 700 : 740;
            var tiktokHtml = '<div style="display: inline-block; width: ' + tiktokWidth + 'px; height: ' + tiktokHeight + 'px; margin: 5px auto; position: relative; background: #000; border-radius: 4px; overflow: hidden; vertical-align: top;"><iframe width="100%" height="100%" src="https://www.tiktok.com/embed/v2/' + tiktokVideoId + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen scrolling="no" style="position: absolute; top: 0; left: 0;"></iframe></div>';
            elem.innerHTML = tiktokHtml;
        } else if (platform === 'instagram') {
            var igPostId = embed.postId || '';
            if (!igPostId && embed.url) {
                var igMatch = embed.url.match(/instagram\.com\/(?:p|reel|tv)\/([A-Za-z0-9_-]+)/);
                if (igMatch) igPostId = igMatch[1];
            }
            if (!igPostId) {
                if (embed.url) window.open(embed.url, '_blank');
                return;
            }
            var isReel = (embed.url && embed.url.indexOf('/reel/') !== -1) || embed.embedType === 'reel';
            var igWidth = window.innerWidth < 768 ? Math.min(window.innerWidth * 0.985, 380) : 340;
            igWidth = Math.max(igWidth, 320);
            var igHeight = isReel ? (window.innerWidth < 768 ? 700 : 740) : (igWidth * 1.25 + 98);
            var igSrc = 'https://www.instagram.com/' + (isReel ? 'reel' : 'p') + '/' + igPostId + '/embed/?hidecaption=true';
            var igHtml = '<div style="display: inline-block; width: ' + igWidth + 'px; height: ' + igHeight + 'px; margin: 5px auto; position: relative; background: #fafafa; border-radius: 4px; overflow: hidden; vertical-align: top;"><iframe width="100%" height="100%" src="' + igSrc + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen scrolling="no" style="position: absolute; top: 0; left: 0;"></iframe></div>';
            elem.innerHTML = igHtml;
        } else if (embed.url && typeof embed.url === 'string' && embed.url.trim()) {
            window.open(embed.url.trim(), '_blank');
        }
    } catch (e) {
        console.error('displayEmbed error:', e);
    }
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

    // Update all media in single container (like feed does)
    var revId = reviewData._id;
    var photoContainer = document.getElementById('lb-photo-container');
    photoContainer.innerHTML = '';

    // Track first media for auto-init
    var firstMedia = null;

    // Create large image display div (visible for auto-init)
    var largerImageDiv = document.createElement("div");
    largerImageDiv.id = 'largerevimg-' + revId;
    largerImageDiv.style.marginBottom = '8px';
    photoContainer.appendChild(largerImageDiv);

    // Create thumbnails container
    var thumbsContainer = document.createElement("div");
    thumbsContainer.id = 'media-thumbs-' + revId;
    thumbsContainer.style.cssText = 'margin-top: 8px; white-space: nowrap; overflow-x: auto; overflow-y: hidden;';
    photoContainer.appendChild(thumbsContainer);

    // Add embed thumbnails FIRST (priority order: embeds, images, videos)
    if (reviewData.embeds && reviewData.embeds.length > 0) {
        reviewData.embeds.forEach(function(embed, idx) {
            var platform = (embed.platform || '').toLowerCase().trim();
            var thumbUrl = embed.thumbnailUrl || '';
            var mediaId = 'embed-' + (embed.videoId || embed.postId || idx);
            if (!thumbUrl && platform === 'youtube' && embed.videoId) {
                thumbUrl = 'https://img.youtube.com/vi/' + embed.videoId + '/hqdefault.jpg';
            }
            var embedDiv = document.createElement('div');
            embedDiv.className = 'media-thumb';
            embedDiv.setAttribute('data-media-id', mediaId);
            if (platform === 'tiktok') {
                embedDiv.style.cssText = 'display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; margin: 5px; cursor: pointer; border-radius: 4px; background-color: #000; border: 2px solid transparent; box-sizing: border-box; vertical-align: top;';
                embedDiv.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64 2.93 2.93 0 01.88.13V9.4a6.84 6.84 0 00-1-.05A6.33 6.33 0 005 20.1a6.34 6.34 0 0010.86-4.43v-7a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-1-.1z"/></svg>';
            } else if (platform === 'instagram') {
                embedDiv.style.cssText = 'display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; margin: 5px; cursor: pointer; border-radius: 4px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); border: 2px solid transparent; box-sizing: border-box; vertical-align: top;';
                embedDiv.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>';
            } else {
                embedDiv.style.cssText = 'display: inline-block; width: 72px; height: 72px; background-position: center center; background-size: cover; background-repeat: no-repeat; margin: 5px; cursor: pointer; position: relative; border-radius: 4px; background-color: #f0f0f0; border: 2px solid transparent; box-sizing: border-box; vertical-align: top;';
                embedDiv.style.backgroundImage = "url('" + thumbUrl + "')";
                embedDiv.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 24px; height: 24px; background: rgba(225,232,237,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><svg width="12" height="12" viewBox="0 0 24 24" fill="rgb(99,114,130)" style="margin-left: 2px;"><path d="M8 5v14l11-7z"/></svg></div>';
            }
            embedDiv.onclick = function() { displayEmbed(embed, revId); updateLbThumbnailSelection(revId, mediaId); };
            thumbsContainer.appendChild(embedDiv);

            // Track first embed
            if (!firstMedia) {
                firstMedia = { type: 'embed', data: embed, mediaId: mediaId };
            }
        });
    }

    // Add image thumbnails
    if (reviewData.images && reviewData.images.length > 0) {
        reviewData.images.forEach(function(img) {
            var imageId = img.imageId;
            var mediaId = 'img-' + imageId;
            var imageDiv = document.createElement("div");
            imageDiv.className = 'media-thumb lb-small-img';
            imageDiv.setAttribute('data-media-id', mediaId);
            imageDiv.style.cssText = 'display: inline-block; width: 72px; height: 72px; background-position: center center; background-size: cover; background-repeat: no-repeat; margin: 5px; cursor: pointer; border-radius: 4px; border: 2px solid transparent; box-sizing: border-box; vertical-align: top;';
            imageDiv.style.backgroundImage = 'url(https://images.files.ca/200x200/' + imageId + '.jpg?nocrop=1)';
            imageDiv.onclick = function() { displayLargeImage(imageId, revId); updateLbThumbnailSelection(revId, mediaId); };
            thumbsContainer.appendChild(imageDiv);

            // Track first image if no embed
            if (!firstMedia) {
                firstMedia = { type: 'image', data: imageId, mediaId: mediaId };
            }
        });
    }

    // Add video thumbnails
    if (reviewData.videos && reviewData.videos.length > 0) {
        reviewData.videos.forEach(function(video) {
            var mediaId = 'vid-' + video.videoId;
            var thumbUrl = video.thumbnailUrl || 'https://videocdn.n49.ca/thumb/' + video.videoId + '.jpg';
            var videoThumb = document.createElement("div");
            videoThumb.className = 'media-thumb';
            videoThumb.setAttribute('data-media-id', mediaId);
            videoThumb.style.cssText = 'display: inline-block; width: 72px; height: 72px; background-color: #333; background-image: url(' + thumbUrl + '); background-size: cover; background-position: center; margin: 5px; cursor: pointer; position: relative; border-radius: 4px; border: 2px solid transparent; box-sizing: border-box; vertical-align: top;';
            videoThumb.innerHTML = '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 24px; height: 24px; background: rgba(225,232,237,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center;"><svg width="12" height="12" viewBox="0 0 24 24" fill="rgb(99,114,130)" style="margin-left: 2px;"><path d="M8 5v14l11-7z"/></svg></div>';
            videoThumb.onclick = function() {
                var elem = document.querySelector('#largerevimg-' + revId);
                if (elem) {
                    elem.innerHTML = '<div><video preload="auto" controls autoplay style="width: 100%; max-height: 400px; border-radius: 4px;"><source src="https://videocdn.n49.ca/mp4sdpad480p/' + video.videoId + '.mp4#t=0.1" type="video/mp4"></video></div>';
                }
                updateLbThumbnailSelection(revId, mediaId);
            };
            thumbsContainer.appendChild(videoThumb);

            // Track first video if no embed or image
            if (!firstMedia) {
                firstMedia = { type: 'video', data: video.videoId, mediaId: mediaId };
            }
        });
    }

    // Auto-init first media item
    if (firstMedia) {
        setTimeout(function() {
            if (firstMedia.type === 'embed') {
                displayEmbed(firstMedia.data, revId);
            } else if (firstMedia.type === 'image') {
                displayLargeImage(firstMedia.data, revId);
            } else if (firstMedia.type === 'video') {
                var elem = document.querySelector('#largerevimg-' + revId);
                if (elem) {
                    elem.innerHTML = '<div><video preload="auto" controls style="width: 100%; max-height: 400px; border-radius: 4px;"><source src="https://videocdn.n49.ca/mp4sdpad480p/' + firstMedia.data + '.mp4#t=0.1" type="video/mp4"></video></div>';
                }
            }
            updateLbThumbnailSelection(revId, firstMedia.mediaId);
        }, 100);
    }

    // Clear unused containers
    var videoContainer = document.getElementById('lb-video-container');
    if (videoContainer) videoContainer.innerHTML = '';
    var embedContainer = document.getElementById('lb-embed-container');
    if (embedContainer) embedContainer.innerHTML = '';

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
    var embedContainer = document.getElementById('lb-embed-container');
    if (embedContainer) embedContainer.innerHTML = '';
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
