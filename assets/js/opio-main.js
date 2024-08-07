
var subSkip = 0;
var subLimit = 25;
var page = 1;

function toggleFeed(tab, index) {
    switch(tab.id) {
        case 'opioUnderline' : 
        opioToggleStuff();
        break;
        default:
        opioToggleStuff();
        break;
    }
    
    // if(tab.includes('opioNativeFeedLogo'))
}

function opioToggleStuff() {
    var currentActiveProperty = localStorage.getItem('currentActiveProperty');
    //opacity for focus
    
    document.getElementById('opioUnderline').style.opacity = '1';
    document.getElementById('entireReviewDiv').style.display = 'block';
    
    document.getElementById('opioUnderline').style.borderBottom = '2px  solid <?php echo $overviewColor ?> ';
    document.getElementById('opioUnderline').classList.remove('opioUniquePointer-modifier'); 
    document.getElementById('loadMoreDiv').style.display = 'block';
    document.getElementById('aggregationOpio').style.display = 'flex';
}

//custom js functions
function displayLargeImage(imageId, revId) {
    var elem = document.querySelector(`#largerevimg-${revId}`);
    elem.innerHTML = '<div style="display: inline-block; width: 98.5%; height: 400px; background-position: center center; background-size: cover; background-repeat: no-repeat; margin: 5px; text-align: center; background-image: url(&quot;https://images.files.ca/800x800/'+imageId+'.jpg?nocrop=1&quot;); opacity: 1; transition: opacity 1s ease 0s;"></div><div><div style="position: absolute; z-index: 1; top: 40%; right: 0px; width: 5%; margin: 25px;"></div><div style="display: none;"></div></div>';
}

function writeComment(revId) {
    var elem = document.querySelector(`#writecomment2-${revId}`);
    
    var writecommentdiv = "writecomment-" + revId;
    var writecommentdiv2 = "writecomment2-" + revId;
    
    toggle(writecommentdiv)
    toggle(writecommentdiv2)
    
    
}

function toggle(id) {
    var div1 = document.getElementById(id);
    
    if(div1.style.display != "none") {
        div1.style.display = "none";
    } else {
        div1.style.display = "inline-block";
    }
}

function deleteComment(id) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var div = document.getElementById(id);
            div.style.display = "none";
        }
    };
    
    
    var path = `/reviewFeed/deleteComment/?commentId=${id}`;
    var env = "<?php echo $environment ?>";
    
    if(env != "development") {
        path = `http://native.op.io/reviewFeed/deleteComment/?commentId=${id}`;
    }
    
    xhttp.open("GET", path, true);
    
    xhttp.send();
}

function editComment(id) {
    var editDiv = document.getElementById(id+"-editDiv");
    editDiv.style.display = "block";
    
    var commentDiv = document.getElementById(id);
    commentDiv.style.display = "none";
}


// this basically loops through all properties and hides the aggregate divs

function shareFacebookUrl(reviewFeedUrl, reviewId) {
    reviewFeedUrl = reviewFeedUrl + "?opio-review-id=" + reviewId;
    
    window.open("https://www.facebook.com/sharer.php?u="+ encodeURIComponent(reviewFeedUrl),
    "sharer", "toolbar=0,status=0,width=626,height=436"
    )
}

function shareTwitterUrl(reviewFeedUrl, reviewId) {
    reviewFeedUrl = reviewFeedUrl + "?opio-review-id=" + reviewId;
    
    var url = reviewFeedUrl;
    var i = "original_referer=" + encodeURIComponent(window.location.href)
    , n = "&text=Check out our new review! " + encodeURIComponent(url)
    , a = "https://twitter.com/intent/tweet?" + i + "&source=tweetbutton" + n;
    window.open(a, "twitter", "width=600,height=400")
}

function loadMore(business_id) {
    var elem = document.querySelector(`#loadMoreOpioDivButton`);
    elem.style.backgroundColor='rgb(192, 199, 205)'; 
    elem.style.cursor = 'not-allowed';
    elem.innerHTML='Loading ...';
    
    var body = {};
    var xhttp = new XMLHttpRequest();
    // window.nextPageToken = LastEvaluatedKey;
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var newReviews = JSON.parse(this.responseText);
            var business = newReviews;
            if(!newReviews.LastEvaluatedKey || newReviews.reviews.length === 0) {
                var div = document.getElementById('loadMoreDiv');
                div.style.display = "none";
                return ;
            }
            var reviewFeedUrl = newReviews.reviews[0].entityInfo.reviewFeedUrls['5734f48a0b64d7382829fdf7'];
            
            // newReviews = newReviews.reviews.filter((review) => {
            // 	return review.propertyId == '5734f48a0b64d7382829fdf7' && (review.status == 'published' || review.status == 'guest') && review.deleted == false;
            // });
            window.nextPageToken = newReviews.LastEvaluatedKey;
            var reviewsDiv = document.querySelector(`#entireReviewDiv`);
            var loadMoreDiv = document.querySelector(`#loadMoreOpioDivButton`);
            insertDivs(newReviews.reviews, reviewsDiv, loadMoreDiv, reviewFeedUrl, business);
        }
    };
    var ent_id = business_id;
    var path = `https://op.io/api/entities/mixreviews?entityId=${ent_id}`;
    xhttp.open("POST", path, true);
    var adminApiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOjE2NDQzNDY2MzEsInVzZXJfaWQiOiJrdHN4dzlramxkZ3RzZDNqMSIsImV4cCI6MTI5NzY0NDM0NjYzMX0.FZeMMsZlix1eQ1aJFmQ0MV_L_ezFb4RhrqCIhceTT-w';
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.setRequestHeader("authorization", `Bearer ${adminApiKey}`);
    xhttp.send(JSON.stringify({LastEvaluatedKey: window.nextPageToken}));`<div id="opio-review-feed" >`
}

function insertDivs(newReviews, reviewsDiv, loadMoreDiv, reviewFeedUrl, business) {
    var overviewColor = "#0078ca";
    var buttonColor = "#0078ca";
    var bgColor = "#ffffff";
    var buttonColorText = "#ffffff";

    if (business.reviewFeedSettings && business.reviewFeedSettings.feedBgColor) {
        bgColor = business.reviewFeedSettings.feedBgColor;
    }

    if (business.reviewFeedSettings && business.reviewFeedSettings.feedButtonColor) {
        buttonColor = business.reviewFeedSettings.feedButtonColor;
    }
    var overviewColor = buttonColor;

    if(buttonColor == bgColor) {
        overviewColor = buttonColorText;
    }
    let reviewDivs = '';
    for(var i=0; i < newReviews.length; i++) {
        reviewDivs += addReview(newReviews[i], i, reviewFeedUrl, business);
        loadMoreDiv.style.backgroundColor=overviewColor; 
        loadMoreDiv.innerHTML='Load More';
        loadMoreDiv.style.cursor='pointer';

    }
    reviewsDiv.innerHTML += reviewDivs.trim();
}

function getVerificationMethod(review) {
    try {
        if(typeof review.user.userPic !== 'undefined' && typeof review.user.userPic !== 'object' && (review.user.userPic.includes('fb')) && !review.user.userPic.includes('google')) {
            return 'Facebook';
        }
        else if(typeof review.user.userPic !== 'undefined' && typeof review.user.userPic !== 'object' && review.user.userPic.includes('google')) {
            return 'Google';
        }
        else if(review.user.email && review.user.email.includes('op.io') || review.user.verifiedStatus == 'guest') {
            return 'Business';
        }
        else if(review.status == 'guest') {
            return 'Guest'
        }
        else {
            return 'Email';
        }
    }
    catch(err) {
        console.log('something went wrong', err);
        return 'Email';
    }
}

const opioWpPluginGetLastDigit = num => +(num + '').slice(-1);

function adButtonUrl(business) {
	window.open(business, 'blank');
}

function opioWpPluginAddBusinessAds(index, business) {
    var businessAdBuilder = '';
    var ad = business.ads.adButtonURL;
    
    if (business.ads && business.ads.activeAd) {
        if (opioWpPluginGetLastDigit(index + 1) === opioWpPluginGetLastDigit(business.ads.adReviewAppear)) {
            if (business.ads.adStyle === 'upload') {
                businessAdBuilder += '<div>';
                businessAdBuilder += '<div class="opio-ad" id="businessAd" onclick="adButtonUrl(\'' + ad + '\')" style="margin-top: 5%; cursor: pointer; background-position: center; background-repeat: no-repeat; background-size: cover; background-image: url(' + business.ads.adBackgroundImage + '); width: 100%; height: 175px; box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.2); border: 1px solid #8080807a; border-radius: 5px">';
                businessAdBuilder += '</div>';
                businessAdBuilder += '</div>';
            } else {
                businessAdBuilder += '<div id="adBox" style="box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.2); border: 1px solid ' + business.ads.backgroundBorderColor + '; border-radius: 5px; height: 175px; margin-top: 5%; background-position: center; background-repeat: no-repeat; background-size: cover; background-image: url(' + business.ads.adBackgroundImage + '); background-color: ' + business.ads.backgroundColor + ';">';
                businessAdBuilder += '<div style="width: 100%; height: 100%;">';
                businessAdBuilder += '<div id="adLogo" style="float: left; width: 30%;">';
                businessAdBuilder += '<img id="adImg" style="height: 110px; margin: 30px" src="' + business.ads.adImage + '" /></div>';
                businessAdBuilder += '<div id="adContent" style="float: right; width: 70%; margin: none; ">';
                businessAdBuilder += '<div id="adTitle" style="color: ' + business.ads.adTitleColor + '; line-height: normal; margin: 20px 0px 0px 0px; font-family: ' + business.ads.adTitleFontFamily + '; text-decoration: ' + business.ads.adTitleFontStyle + '; font-weight: ' + (business.ads.adTitleFontStyle === 'none' ? 'normal' : business.ads.adTitleFontStyle) + '; font-style: ' + (business.ads.adTitleFontStyle === 'none' ? 'normal' : business.ads.adTitleFontStyle) + '; font-size: ' + business.ads.adTitleFontSize + 'px;">' + business.ads.adTitle + '</div>';
                businessAdBuilder += '<div id="adSubTitle" style="line-height: normal; margin-bottom: 3%; color: ' + business.ads.adSubTitleColor + '; font-family: ' + business.ads.adSubTitleFontFamily + '; text-decoration: ' + business.ads.adSubTitleFontStyle + '; font-weight: ' + (business.ads.adSubTitleFontStyle === 'none' ? 'normal' : business.ads.adSubTitleFontStyle) + '; font-style: ' + (business.ads.adSubTitleFontStyle === 'none' ? 'normal' : business.ads.adSubTitleFontStyle) + '; font-size: ' + business.ads.adSubTitleFontSize + 'px;">' + business.ads.adSubTitle + '</div>';
                businessAdBuilder += '<button id="adCTA" onclick="adButtonUrl(\'' + ad + '\')" style="margin-left: 0px; background-color: ' + business.ads.calltoActionButtonBackgroundColor + '; border-radius: 3px; display: inline-block; height: 40px; line-height: 40px; text-align: center; min-width: 100px; cursor: pointer; font-weight: 400; font-size: 14px; padding: 0px 30px 0px 30px; border: none; color: rgb(255,255,255); transition: all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0s; ">';
                businessAdBuilder += '<span id="btnText" style="color: ' + business.ads.calltoActionButtonBorderColor + '; font-family: ' + business.ads.callToActionFontFamily + '; font-weight: ' + (business.ads.callToActionFontStyle === 'none' ? 'normal' : business.ads.callToActionFontStyle) + '; font-size: ' + business.ads.callToActionFontSize + 'px; line-height: none; ">' + business.ads.callToActionText + '</span></button>';
                businessAdBuilder += '</div>';
                businessAdBuilder += '</div>';
                businessAdBuilder += '</div>';
            }
        } else {
            businessAdBuilder;
        }
    } else {
        businessAdBuilder;
    }
    return businessAdBuilder;
}

function addReview(rev, index, reviewFeedUrl, business) {
    var reviewFontColor = "#1D1D1F";
    if (business.reviewFeedSettings && business.reviewFeedSettings.reviewFontColor) {
        reviewFontColor = business.reviewFeedSettings.reviewFontColor;
    }

    var user = rev.users && rev.users.length ? rev.users[0] : rev.user;
    var comments = rev.comments;
    var taggedEmployees = rev.taggedEmployees;
    var reviewBuilder = "<hr class='opioHR'>";
    reviewBuilder += `<div id="${rev._id}" style="display: flex; position: relative;"><div style="vertical-align: top; padding-right: 20px;"><div id="outer" style="display: inline-block;">`
    if(rev.reviewType === "google") {
        if(user.userPic) {
            reviewBuilder += `<div id="inner" style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url(&quot;${user.userPic}&quot;);"></div>`;
        } else {
            reviewBuilder += `<div id="inner" style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-color: #dddddd">${user.firstName ? user.firstName.charAt(0).toUpperCase() : ''}</div>`
        }
    } else if(user.userPic && user.userPic.imageId) {
        reviewBuilder += `<div id="inner" style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url(&quot;https://images.files.ca/200x200/${user.userPic.imageId}.jpg?nocrop=1&quot;);"></div>`;
    } else {
        reviewBuilder += `<div id="inner" style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-color: #dddddd">${user.firstName ? user.firstName.charAt(0).toUpperCase() : ''}</div>`
    }
    reviewBuilder += '</div></div>';
    reviewBuilder += `<div id="${rev._id}-reviewContainer" style="vertical-align: top; flex-grow: 1;"><div style="margin-bottom: 10px;"><div><div style="overflow: hidden; position: relative;">`;
    if(rev.propertyInfo.name === 'facebook') {
        if(rev.rating === "positive") {
            reviewBuilder += `<div style="display: inline-flex; margin-bottom: 5px; align-items: center; vertical-align: middle;">`;
            reviewBuilder += `<p style="margin: 0; font-weight: 500; font-size: 13px; white-space: nowrap;">Recommends</p>`;
            reviewBuilder += `</div>`;
        } else {
            reviewBuilder += `<div style="display: inline-flex; margin-bottom: 5px; align-items: center; vertical-align: middle;">`;
            reviewBuilder += `<p style="margin: 0; font-weight: 500; font-size: 13px; white-space: nowrap;">Doesn't Recommend</p>`;
            reviewBuilder += `</div>`;
        }
    } else {
        reviewBuilder += `<style> .react-stars-04811029757080685:before { position: absolute; overflow: hidden; display: block; z-index: 1; top: 0; left: 0; width: 50%; content: attr(data-forhalf); color: <?php echo $starColor ?>; }</style>`;
        if(rev.rating > 0) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        } else if(rev.rating == 0.5) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-2{fill:#E6E8EB;}.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>`;
        } else {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        }
        
        if(rev.rating > 1){
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        } else if(rev.rating == 1.5) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill:#E6E8EB;}.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>`;
        } else {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        }
        
        if(rev.rating > 2){
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        } else if(rev.rating == 2.5) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill:#E6E8EB;}.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>`;
        } else {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        }
        
        if(rev.rating > 3){
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        } else if(rev.rating == 3.5) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill:#E6E8EB;}.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>`;
        } else {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        }
        
        if(rev.rating > 4){
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        } else if(rev.rating == 4.5) {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str-1{fill:#E6E8EB;}.str-1{fill: <?php echo $starColor ?>;}</style></defs><title>yellow-grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str-2" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0m0,18.36"/><path class="str-1" d="M12,0,8.64,8,0,8.71l6.55,5.68-2,8.44L12,18.36m0,0"/></g></g></svg></div>`;
        } else {
            reviewBuilder += `<div style="position: relative; overflow: hidden; cursor: pointer; display: block; float: left; width:16px; height: 22px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24.04 22.83"><defs><style>.str0-1{fill:#E6E8EB;}</style></defs><title>grey</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="str0-1" d="M12,18.36l7.43,4.48-2-8.44L24,8.71,15.39,8,12,0,8.64,8,0,8.71l6.55,5.68-2,8.44Zm0,0"/></g></g></svg></div>`;
        }
    }
    
    reviewBuilder += `</div></div><div style="display: flex; flex-wrap: wrap; align-items: center;font-weight: 400; font-size: 12px; color: ${reviewFontColor};"><span>By</span>`;
    
    /**Remove member link for guest reviews */
    const verifiedByStatus = getVerificationMethod(rev);
    if(verifiedByStatus == 'Guest' || user.verifiedStatus === 'guest' || rev.propertyId === 1 || rev.propertyId === 2) {
        reviewBuilder += `<a class="nativefeed" style=" text-decoration: none; color: ${reviewFontColor}; border-bottom: none; font-weight: 500"><span style="margin-left: 3px;">${user.firstName} ${user.lastName}</span></a><span style="margin-left: 3px;"> on ${moment(rev.dateCreated).format("MMM D, YYYY")}</span>`;
    }
    else if(rev.propertyId === 6) {
        reviewBuilder += `<a class="nativefeed" target="_blank" href="${user.user_id}" style=" text-decoration: none; color: ${reviewFontColor}; border-bottom: none; font-weight: 500"><span style="margin-left: 3px;">${user.firstName} ${user.lastName}</span></a><span style="margin-left: 3px;"> on ${moment(rev.dateCreated).format("MMM D, YYYY")}</span>`;
    } 
    else if(rev.propertyId === 3) {
        reviewBuilder += `<a class="nativefeed" target="_blank" href="https://www.yelp.ca/user_details?userid=${user.user_id}" style=" text-decoration: none; color: ${reviewFontColor}; border-bottom: none; font-weight: 500"><span style="margin-left: 3px;">${user.firstName} ${user.lastName}</span></a><span style="margin-left: 3px;"> on ${moment(rev.dateCreated).format("MMM D, YYYY")}</span>`;
    }
    else {
        reviewBuilder += `<a class="nativefeed" target="_blank" href="https://op.io/member/${user.user_id}/" style=" text-decoration: none; color: ${reviewFontColor}; border-bottom: none; font-weight: 500"><span style="margin-left: 3px;">${user.firstName} ${user.lastName}</span></a><span style="margin-left: 3px;"> on ${moment(rev.dateCreated).format("MMM D, YYYY")}</span>`;
    }
    
    /**Place verified by here */
    reviewBuilder += `<div class="verifiedByContainer" >`;
    reviewBuilder += `<div style="display: flex; flex-wrap: wrap; align-items: center;">`;
    reviewBuilder += `<div style="display: flex; flex-wrap: wrap; align-items: center; width: 15px; height: 22px; vertical-align: middle; margin-right: 2px;">`;
    reviewBuilder += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14"><defs><style>.cls-1{fill: ${reviewFontColor};}</style></defs><title>Asset 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7,0a7,7,0,1,0,7,7A7,7,0,0,0,7,0Zm3.18,5.64L6.51,9.31a.67.67,0,0,1-.47.2.69.69,0,0,1-.48-.2L3.82,7.56a.63.63,0,0,1-.2-.47.68.68,0,0,1,1.15-.48L6,7.88,9.23,4.69a.68.68,0,0,1,1.15.48A.65.65,0,0,1,10.18,5.64Z"/></g></g></svg>`;
    reviewBuilder += `</div>`;
    
    if(verifiedByStatus == 'Guest') {
        reviewBuilder += `<span style="vertical-align: middle;">Guest Review</span></div>`;
    } else {
        reviewBuilder += `<span style="vertical-align: middle;">Verified by ${verifiedByStatus.toLowerCase()}</span></div>`;
    }
    reviewBuilder += `</div></div></div>`;
    reviewBuilder += `<div style="margin-bottom: 10px;"></div><div class="reviewTextColor" style="white-space: pre-wrap; font-size: 14px; line-height: 1.5em; color: ${reviewFontColor} ">${rev.content === null ? '' : rev.content}</div>`;
    /**Tagged employees */
    if(taggedEmployees && taggedEmployees.length > 0) {
        reviewBuilder += taggedEmployeesBuilder(taggedEmployees, rev, reviewFontColor);
        reviewBuilder += `<span style="font-size: 14px; font-weight: 500; color: ${reviewFontColor}">Employees tagged in this review</span>`;
    }
    if((rev.images === null || (rev.images && rev.images.length == 0)) && (rev.videos === null || (rev.videos && rev.videos.length == 0))) {
        reviewBuilder += `<div id="media-container" style="padding-bottom: 10px;"></div>`;
    } else {
        reviewBuilder += `<div id="media-container" style="padding-bottom: 10px;">`;
        if(rev.images) {
            reviewBuilder += `<div id="largerevimg-${rev._id}"></div>`;
            rev.images.forEach(image => {
                reviewBuilder += `<a onclick="displayLargeImage(${image.imageId}, ${rev._id})"><div style="display: inline-block; width: 72px; height: 72px; background-position: center center; background-size: cover; background-repeat: no-repeat; margin: 5px; text-align: center; background-image: url(&quot;https://images.files.ca/200x200/${image.imageId}.jpg?nocrop=1&quot;);">
                </div></a>`;
            });
        }
        
        if(rev.videos) {
            rev.videos.forEach(video =>  {
                reviewBuilder += `<div><video preload="auto" controls="" style="height: auto; margin: 5px; width: 100%; transition: width 1s ease-out 0s, height 1s ease-out 0s;"><source src="https://videocdn.n49.ca/mp4sdpad480p/${video.videoId}.mp4#t=0.1" type="video/mp4"></video></div>`;
            });
        }		
        
        reviewBuilder += '</div>';
    }
    reviewBuilder += `<div style="display: inline-block; margin-top: 10px; margin-bottom:10px;"></div>`;
    if(reviewFeedUrl){
        reviewBuilder += `<div style="color: rgb(170, 170, 170); height: 20px; line-height: 20px; display: flex; margin-top: 10px; float: right; right: 0px;">`;
        reviewBuilder += `<span style="color: ${reviewFontColor}; font-size: 14px;">Share</span>`;
        reviewBuilder += `<a style="display: flex; cursor: pointer; margin-left: 5px;" onclick="shareFacebookUrl('${reviewFeedUrl}', '${rev._id}')">`;
        reviewBuilder += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><defs><style>.cls-1{fill:<?php echo $reviewFontColor; ?>;}</style></defs><title>Asset 1</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M10,0A10,10,0,1,0,20,10,10,10,0,0,0,10,0Zm2.14,10h-1.4v5H8.66V10h-1V8.24h1V7.1A2,2,0,0,1,10.76,5H12.3V6.72H11.18a.42.42,0,0,0-.44.48v1h1.58Z"/></g></g></svg>`;
        reviewBuilder += `</a>`;
        reviewBuilder += `<a style="display: flex; cursor: pointer; margin-left: 3px;" onclick="shareTwitterUrl('${reviewFeedUrl}', '${rev._id}')">`;
        reviewBuilder += `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><defs><style>.cls-1{fill: <?php echo $reviewFontColor; ?>;}</style></defs><title>Asset 2</title><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M10,0A10,10,0,1,0,20,10,10,10,0,0,0,10,0Zm4,8v.27a5.84,5.84,0,0,1-9,4.91,3.91,3.91,0,0,0,.49,0A4.12,4.12,0,0,0,8,12.29a2,2,0,0,1-1.92-1.42,2.47,2.47,0,0,0,.39,0,2,2,0,0,0,.54-.07,2.06,2.06,0,0,1-1.65-2v0a2.08,2.08,0,0,0,.93.25,2,2,0,0,1-.91-1.71,2,2,0,0,1,.28-1A5.84,5.84,0,0,0,9.92,8.46,2.42,2.42,0,0,1,9.87,8a2.05,2.05,0,0,1,2.05-2,2.07,2.07,0,0,1,1.5.64,4.07,4.07,0,0,0,1.3-.49,2,2,0,0,1-.9,1.13A4.2,4.2,0,0,0,15,6.9,4.11,4.11,0,0,1,14,8Z"/></g></g></svg>`;
        reviewBuilder += `</a>`;
        reviewBuilder += `</div>`;
    }
    
    
    
    
    if(rev.comments && rev.comments.length > 0) {
        reviewBuilder += addComments(rev.comments, rev, business);
    }
    
    if(rev.propertyInfo.name === "facebook") {
        reviewBuilder += `<div style="background-image: url(&quot;https://op.io/dashboard/api/reviews/get-image/${rev.propertyInfo.logo.imageId}?width=200&amp;height=200&quot;); position: absolute; right: 0px; top: 2px; display: flex; text-align: right; min-width: 75px; min-height: 24px; background-size: contain; background-position: center center; background-repeat: no-repeat; margin-right: 0px;"></div>`
    } else {
        reviewBuilder += `<div style="background-image: url(&quot;https://op.io/dashboard/api/reviews/get-image/${rev.propertyInfo.logo.imageId}?width=200&amp;height=200&quot;); position: absolute; right: 0px; top: 2px; display: flex; text-align: right; min-width: 100px; min-height: 24px; background-size: contain; background-position: center center; background-repeat: no-repeat; margin-right: 0px;"></div>`;
    }
    reviewBuilder += `</div></div>`;
    
    if(business && business.ads){
        reviewBuilder += opioWpPluginAddBusinessAds(index, business);
    }
    //reviewBuilder += addComment(rev.comments);
    
    return reviewBuilder;
    
}

function taggedEmployeesBuilder(employees, review, reviewFontColor) {
    const finalStringStart = `<div class="empTagContainer">`;
    var taggedEmps = '';
    const finalStringEnd = `</div>`;
    var entityid = review.entityId;
    
    employees.map(emp => {
        let position = '';
        if (!emp.hasOwnProperty('lastName')) {
            emp.lastName = '';
        }
        if (emp.hasOwnProperty('position_title')) {
            if (emp.position_title.length === 0) {
                position = ''
            } else {
                let posIndex = emp.position_title
                    .map((p) => p.entityId)
                    .indexOf(entityid);
                if (posIndex !== -1) {
                    position = emp.position_title[posIndex].position
                }
            }
        }
        let userImage = '';
        if (emp.userPic && emp.userPic.imageId) {
            userImage += `<div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url('https://images.files.ca/200x200/${emp.userPic.imageId}.jpg?nocrop=1');"></div>`;
        } else if (emp.userPic && emp.userPic !== "") {
            userImage += `<div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-image: url('${emp.userPic}');"></div>`;
        } else {
            userImage += `<div id="inner" style="width: 35px; height: 35px; line-height: 35px; border-radius: 50%; color: ${reviewFontColor}; font-size: 24px; text-align: center; background-size: cover; background-repeat: no-repeat; background-color: #dddddd">${emp.firstName.charAt(0).toUpperCase()}</div>`;
        }
        if (position === undefined || position === '') {
            taggedEmps += 
            `<div class="empTagCard">
            ${userImage}
            <div style="display: grid; grid-template-rows: 25px">
            <span style="align-self: end; justify-self: left; margin-left: 10px; color: ${reviewFontColor}; font-size: 14px">
            ${emp.firstName + ' ' + emp.lastName.charAt(0)}
            </span>
            </div>
            </div>`
        } else {
            taggedEmps += 
            `<div class="empTagCard">
            ${userImage}
            <div style="display: grid; grid-template-rows: 25px 20px">
            <span style="align-self: end; justify-self: left; margin-left: 10px; color: ${reviewFontColor}; font-size: 14px">
            ${emp.firstName + ' ' + emp.lastName.charAt(0)}
            </span>
            <span style="align-self: top; justify-self: left; margin-left: 10px; margin-top: -2px; color: ${reviewFontColor}; font-size: 12px">
            ${position}
            </span>
            </div>
            </div>`;
        }
    })
    return finalStringStart + taggedEmps + finalStringEnd;
}


function addComments(comments, rev, business) {
    var reviewFontColor = "#1D1D1F";
    if (business.reviewFeedSettings && business.reviewFeedSettings.reviewFontColor) {
        reviewFontColor = business.reviewFeedSettings.reviewFontColor;
    }
    var cookiedUser = null;
    if(!cookiedUser) {
        cookiedUser = {
            "fullName": "NULL",
            "user_id": "NULL"
        }
    }
    var commentBuilder = '';
    for (var i = 0; i < comments.length; i++) {
        var comment = comments[i];
        var user = comment.users && comment.users.length ? comment.users[0] : [];
        if(typeof user === 'undefined') return '';
        // if(typeof comment.entities[0] === 'undefined') return '';
        if(comment.status) {
            return '<div></div>';
        }
        commentBuilder += `<div style="margin-top: 10px;"><div id="${comment._id}" style="display: flex; position: relative;"><div style="vertical-align: top; padding-right: 10px;"><div id="outer" style="display: inline-block;">`;
        if(user.length) {
            if(comment.users[0].userPic && comment.users[0].userPic.imageId) {
                var imageId = comment.users[0].userPic.imageId;
                commentBuilder += `<div id="inner" style="width: 30px; height: 30px; line-height: 30px; border-radius: 50%; color: ${reviewFontColor}; font-size: 18px; text-align: center; background-position: center center; background-size: contain; background-repeat: no-repeat; background-image: url(&quot;https://images.files.ca/200x200/${imageId}.jpg?nocrop=1&quot;);"></div>`
            } else {
                commentBuilder += '<div id="inner" style="width: 30px; height: 30px; line-height: 30px; border-radius: 50%; color: ' + reviewFontColor + '; font-size: 18px; text-align: center; background-position: center center; background-size: contain; background-repeat: no-repeat; background-color: ddddd"> '+user.firstName.charAt(0).toUpperCase()+' </div>';
            }
        } else if(rev.entityInfo.logo && rev.entityInfo.logo.imageId){
            var imageId = rev.entityInfo.logo.imageId;
            commentBuilder += `<div id="inner" style="width: 30px; height: 30px; line-height: 30px; border-radius: 50%; color: ${reviewFontColor}; font-size: 18px; text-align: center; background-position: center center; background-size: contain; background-repeat: no-repeat; background-image: url(&quot;https://images.files.ca/200x200/${imageId}.jpg?nocrop=1&quot;);"></div>`
        } else {
            commentBuilder += `<div id="inner" style="width: 30px; height: 30px; line-height: 30px; border-radius: 50%; color: ${reviewFontColor}; font-size: 18px; text-align: center; background-position: center center; background-size: contain; background-repeat: no-repeat;"></div>`
        }
        
        commentBuilder += '</div></div><div style="vertical-align: top; flex-grow: 1;"><div style="font-weight: 400; font-size: 12px; margin-bottom: 10px; color: ' + reviewFontColor + ';">By ';
        
        if(user.length) {
            commentBuilder += `<a class="nativefeed" style="font-weight: 500; text-decoration: none; color: ${reviewFontColor}; border-bottom: none;">${comment.users[0].firstName} ${comment.users[0].lastName}</a> on  ${moment(comment.dateCreated).format("MMM D, YYYY")} `;
        } else {
            commentBuilder += `<a class="nativefeed" style="font-weight: 500; text-decoration: none; color: ${reviewFontColor}; border-bottom: none;">${rev.entityInfo.name}</a> on  ${moment(comment.dateCreated).format("MMM D, YYYY")} `;
        }
        
        commentBuilder += `</div><div style="font-size: 14px; white-space: pre-wrap; line-height: 1.5em;">${comment.content}</div>`;
        commentBuilder += `<div style="display: inline-block; margin-top: 10px;">`;
        
        commentBuilder += `<div></div>`;
        commentBuilder += `</div></div>`;
        commentBuilder += `</div></div>`;
        
    }
    
    return commentBuilder;
}

function getImagePath(logo, width, height) {
    if(!logo) return '';
    if(typeof logo == 'string') {
        if(logo.match('default-avatar')) return '';
        return logo;
    }
    width = width ? width : '200';
    height = height ? height : '200';
    return `https://images.files.ca/${width}x${height}/${logo.imageId}.jpg?nocrop=1`;
}

function getFullName (user) {
    var out = '';
    if(user.firstName) out += user.firstName;
    if(user.firstName && user.lastName) out += ' ';
    if(user.lastName) out += user.lastName;
    return out;
}
