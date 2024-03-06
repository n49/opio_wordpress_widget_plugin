// Check if jQuery is already loaded
if (typeof jQuery !== 'undefined') {
    // Use noConflict() to release the $ alias
    jQuery.noConflict(true);
} else {
    console.log('jQuery is not loaded.');
}