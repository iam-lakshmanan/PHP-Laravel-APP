var teamNodeId;
var iframeSrcs = [];
var autoplayInterval;
var players = [];
var isPlaying = false;
var userPlayed = false;

$(document).ready(function () {
    teamNodeId = $('#team_page_nid').val();
    $('#loader').show();
    $('#team-content').empty();

    $.ajax({
        url: `/team-page-content/${teamNodeId}`,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            iframeSrcs = response.iframe_srcs;
            var fieldTeamSummaryValue = response.getTeamPageContent.getTeamPageContent.field_team_summary_value;
            $('#team-content').html('<div class="abstract-drop-content">' + fieldTeamSummaryValue + '</div>');

            var contentDivs = document.getElementsByClassName('abstract-drop-content');
            if (contentDivs.length > 0) {
                var contentDiv = contentDivs[0];
                var html = contentDiv.innerHTML;
                contentDiv.innerHTML = html.replace(/(<p[^>]*>)(\w)/, function (match, p1, p2) {
                    return p1 + '<span class="text-drop-cap">' + p2 + '</span>';
                });
            }

            teamContentIframe();
            setTimeout(function () {
                $('.loader-container').hide();
            }, 1500);
        },
        error: function (xhr, status, error) {
            $('.loader-container').hide();
            console.error("Error: " + error);
        }
    });

    getVideoTestimonials();
});

function teamContentIframe() {
    $('#team-content iframe').each(function (index) {
        if (iframeSrcs[index]) {
            $(this).attr('src', iframeSrcs[index]);
        }
    });
}

function getVideoTestimonials() {
    $.ajax({
        url: `/team-page/${teamNodeId}`,
        type: 'GET',
        data: { teamNodeId: teamNodeId },
        success: function (response) {
            if (response.getTeamVideoTestimonials.length > 0) {
                var videosHtml = '<div class="container p-0"><div class="row"><div class="col-12"><div class="video-testimonial-team-info team-video-container">' +
                    '<div id="team-video-container">';

                var youtubeFolder = "youtube://v/";
                var embedYouTubeUrl = "https://www.youtube.com/embed/";

                response.getTeamVideoTestimonials.forEach(function (videoTestimonialData, index) {
                    var videoTestimonialVideo = videoTestimonialData.uri;
                    var videoUrl = '';

                    if (videoTestimonialVideo) {
                        var pathParts = videoTestimonialVideo.split(youtubeFolder);
                        var relativePath = (pathParts[1]) ? pathParts[1] : videoTestimonialVideo;
                        videoUrl = embedYouTubeUrl + relativePath;
                    }

                    var iframeUrl = videoUrl + (videoUrl.indexOf('?') === -1 ? '?enablejsapi=1' : '&enablejsapi=1');

                    videosHtml += '<div class="team-video-wrapper">' +
                        '<iframe class="video-frame" id="video' + index + '" src="' + iframeUrl + '" ' +
                        'frameborder="0" allow="autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
                        '</div>';
                });

                videosHtml += '</div><div id="team-dots-container"></div></div></div></div></div>';
                $('#result').html(videosHtml);

                initializeVideoTestimonials();

                if (typeof YT !== 'undefined' && YT && YT.Player) {
                    onYouTubeIframeAPIReady();
                }
            }

            $('#loader').hide();
        },
        error: function (xhr, status, error) {
            $('#loader').hide();
        }
    });
}

function initializeVideoTestimonials() {
    const container = document.getElementById("team-video-container");

    if (!container) return;

    const maxTestimonials = 10;
    let wrappers = Array.from(container.querySelectorAll(".team-video-wrapper")).slice(0, maxTestimonials);
    const dotsContainer = document.getElementById("team-dots-container");
    let currentIndex = 0;

    function showVideo(index) {
        wrappers.forEach((wrapper) => {
            wrapper.style.display = "none";
        });

        dotsContainer.querySelectorAll(".dot").forEach((dot) => dot.classList.remove("active"));
        wrappers[index].style.display = "block";

        if (dotsContainer.children[index]) {
            dotsContainer.children[index].classList.add("active");
        }
    }

    function nextVideo() {
        currentIndex = (currentIndex + 1) % wrappers.length;
        showVideo(currentIndex);
    }

    function createDots() {
        dotsContainer.innerHTML = "";
        wrappers.forEach((_, index) => {
            const dot = document.createElement("div");
            dot.classList.add("dot");
            dot.addEventListener("click", () => {
                clearInterval(autoplayInterval);
                currentIndex = index;
                showVideo(currentIndex);
                sendVideoPlayStatus(index);
                isPlaying = false;
                let isAnyPlaying = players.some(p => p.getPlayerState() === YT.PlayerState.PLAYING);
                if (!isAnyPlaying) startAutoplay();
            });
            dotsContainer.appendChild(dot);
        });
    }

    function sendVideoPlayStatus(index) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/update-video-status", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.send(JSON.stringify({ videoIndex: index, played: true }));
    }

    function startAutoplay() {
        autoplayInterval = setInterval(() => {
            if (!isPlaying && !userPlayed) {
                nextVideo();
            }
        }, 7000);
    }

    function pauseSlider() {
        clearInterval(autoplayInterval);
    }

    if (wrappers.length > 0) {
        createDots();
        showVideo(currentIndex);
        isPlaying = false;
        let isAnyPlaying = players.some(p => p.getPlayerState() === YT.PlayerState.PLAYING);
        if (!isAnyPlaying) startAutoplay();
    }
}

// Global: YouTube IFrame API callback
window.onYouTubeIframeAPIReady = function () {
    console.log('YouTube IFrame API Ready');
    const videoFrames = document.querySelectorAll('iframe.video-frame');

    videoFrames.forEach((iframe, index) => {
        players[index] = new YT.Player(iframe.id, {
            events: {
                'onStateChange': onPlayerStateChange
            }
        });
    });
};

// Global: Handle play/pause
function onPlayerStateChange(event) {
    if (event.data === YT.PlayerState.PLAYING) {
        console.log('Video playing...');
        isPlaying = true;
        userPlayed = true;
        pauseSlider();
    } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
        console.log('Video paused or ended...');
        isPlaying = false;

        const isAnyVideoPlaying = players.some(player =>
            player.getPlayerState() === YT.PlayerState.PLAYING
        );

        if (!isAnyVideoPlaying) {
            userPlayed = false;
            startAutoplay();
        }
    }
}
  