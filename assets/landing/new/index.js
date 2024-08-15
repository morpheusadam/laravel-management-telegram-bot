 "use strict";
 $(document).ready(function() {

    $("#registerForm").submit(function(e) {
        e.preventDefault();
        const emailSubmitted = $("#registerEmail").val();
        if(emailSubmitted!='')
        window.location.href = registerURL+"?email="+emailSubmitted;
    });

    // Target element
    var $typewriter = $("#typewriter");

    // Function to simulate typing for a given text
    function typeText(text, index, callback) {
        if (index < text.length) {
            // Append the next character to the target element
            $typewriter.text($typewriter.text() + text.charAt(index));
            // Call the function again for the next character
            setTimeout(function() {
                typeText(text, index + 1, callback);
            }, 200); // Adjust the delay as needed
        } else {
            // Call the callback function when typing is complete
            callback();
        }
    }

    // Function to handle typing for each text in the array
    function typeTexts(index) {
        if (index < typingKeywords.length) {
            // Clear the target element before typing a new text
            $typewriter.text("");
            // Call the typeText function for the current text in the array
            typeText(typingKeywords[index], 0, function() {
                // Call the typeTexts function for the next text in the array
                typeTexts(index + 1);
            });
        } else {
            // All texts have been typed, repeat the process
            typeTexts(0);
        }
    }

    // Start typing when the document is ready
    typeTexts(0);
});