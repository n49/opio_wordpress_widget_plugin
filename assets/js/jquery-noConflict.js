// Check if jQuery is already loaded
if (typeof jQuery !== 'undefined') {
    // Use noConflict() to release the $ alias
    $.noConflict(true);
} else {
    console.log('jQuery is not loaded.');
}