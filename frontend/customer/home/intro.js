document.addEventListener("DOMContentLoaded", function () {
    const introOverlay = document.getElementById("intro-overlay");
    const introVideo = document.getElementById("intro-video");
    const pageContent = document.getElementById("page-content");

    // Check if intro already played in this browser tab
    const hasPlayed = sessionStorage.getItem("novaIntroPlayed");

    if (!hasPlayed) {
        // FIRST TIME: show video and hide page
        introOverlay.style.display = "flex";
        pageContent.style.opacity = "0";

        introVideo.play();
        
        introVideo.onended = function () {
            introOverlay.style.display = "none";
            pageContent.style.opacity = "1";

            // mark intro as "played"
            sessionStorage.setItem("novaIntroPlayed", "true");
        };

    } else {
        // INTRO HAS ALREADY PLAYED – hide overlay immediately
        introOverlay.style.display = "none";
        pageContent.style.opacity = "1";
    }
});
