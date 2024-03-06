// Check if jQuery is already loaded
if (typeof jQuery !== 'undefined') {
    // Use noConflict() to release the $ alias
    console.log('jQuery is loaded.');

    jQuery.noConflict(true);
} else {
    console.log('jQuery is not loaded.');
}