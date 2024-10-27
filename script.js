document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById("toggleBtn");
    var closeBtn = document.getElementById("closeBtn");
    var chatbotIframe = document.querySelector(".chatbot-iframe");
    var loadingGif = document.getElementById("loadingGif");

    // Functionality for the chatbot toggle button
    toggleBtn.addEventListener("click", function () {
        chatbotIframe.classList.toggle("active");
        closeBtn.style.display = chatbotIframe.classList.contains("active") ? "block" : "none";
        if (chatbotIframe.classList.contains("active")) {
            loadingGif.style.display = "flex";
            // Get the current URL
            var currentUrlWithoutParams = window.location.origin + window.location.pathname;

            // Set the src property of the iframe based on the current URL
            chatbotIframe.src = 'https://chatbotqa.camhdk.com/ChatBot.aspx?parenturl=' + encodeURIComponent(currentUrlWithoutParams);
        } else {
            loadingGif.style.display = "none";
        }
    });

    closeBtn.addEventListener("click", function () {
        chatbotIframe.classList.remove("active");
        closeBtn.style.display = "none";
        loadingGif.style.display = "none";
    });

    chatbotIframe.addEventListener("load", function () {
        loadingGif.style.display = "none";
    });

    // Ensure iframe loading gif is shown again if reloaded manually
    chatbotIframe.addEventListener("beforeunload", function () {
        loadingGif.style.display = "flex";
    });
});
