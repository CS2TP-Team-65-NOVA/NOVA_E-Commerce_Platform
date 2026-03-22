document.addEventListener("DOMContentLoaded", function () {
    const introOverlay = document.getElementById("intro-overlay");
    const introVideo = document.getElementById("intro-video");
    const pageContent = document.getElementById("page-content");

    // Check if intro already played in this browser tab
    const hasPlayed = sessionStorage.getItem("novaIntroPlayed");

    function revealMainPage() {
        if (introOverlay) introOverlay.style.display = "none";
        if (pageContent) pageContent.style.opacity = "1";
        document.body.classList.remove("nova-intro-pending");
    }

    if (!hasPlayed) {
        // FIRST TIME: show video and hide page (chatbot stays hidden via .nova-intro-pending CSS)
        if (introOverlay) introOverlay.style.display = "flex";
        if (pageContent) pageContent.style.opacity = "0";

        if (introVideo) {
            introVideo.play().catch(function () {});

            introVideo.onended = function () {
                revealMainPage();
                sessionStorage.setItem("novaIntroPlayed", "true");
            };

            introVideo.onerror = function () {
                revealMainPage();
                sessionStorage.setItem("novaIntroPlayed", "true");
            };
        } else {
            revealMainPage();
            sessionStorage.setItem("novaIntroPlayed", "true");
        }
    } else {
        // INTRO HAS ALREADY PLAYED – hide overlay immediately, show chatbot
        revealMainPage();
    }
});
