//customer saying
document.addEventListener("DOMContentLoaded", function () {
    const list = document.querySelector(".testimonial-items");
    const items = document.querySelectorAll(".testimonial-items li");
    const totalItems = items.length;
    const prevBtn = document.querySelector(".slider-control-left");
    const nextBtn = document.querySelector(".slider-control-right");
    let index = 0;
    let autoSlideInterval;

    const firstClone = items[0].cloneNode(true);
    const lastClone = items[totalItems - 1].cloneNode(true);

    list.appendChild(firstClone);
    list.insertBefore(lastClone, items[0]);


    list.style.transform = `translateX(-100%)`;

    function updateSlide() {
        list.style.transition = "transform 0.5s ease-in-out";
        list.style.transform = `translateX(-${(index + 1) * 100}%)`;
    }

    function nextSlide() {
        if (index >= totalItems) return;
        index++;
        updateSlide();

        if (index === totalItems) {
            setTimeout(() => {
                list.style.transition = "none";
                index = 0;
                list.style.transform = `translateX(-100%)`;
            }, 500);
        }
    }

    function prevSlide() {
        if (index <= -1) return;
        index--;
        updateSlide();

        if (index === -1) {
            setTimeout(() => {
                list.style.transition = "none";
                index = totalItems - 1;
                list.style.transform = `translateX(-${totalItems * 100}%)`;
            }, 500);
        }
    }


    if (nextBtn) {
        nextBtn.addEventListener("click", function () {

            prevSlide();
            resetAutoSlide();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", function () {

            nextSlide();
            resetAutoSlide();
        });
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 7000);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    startAutoSlide();
});
$(document).ready(function () {
    let slideIndex = 0;
    const interval = 7000; // Change slide every 7 seconds
    const slides = $(".upcoming-testimonial-list li");
    const dotsContainer = $(".dots-container");
    const carouselContainer = $("#testimonial_carousel");
    const maxSlides = 10;
    const limitedSlides = slides.slice(0, maxSlides);
    const numDots = limitedSlides.length;
    function createDots() {
        dotsContainer.empty();
        if (limitedSlides.length > 0) {
            for (let slideIndex = 0; slideIndex < numDots; slideIndex++) {
                let dot = $('<span class="dot"></span>');
                dot.attr("data-index", slideIndex);
                dotsContainer.append(dot);
            }
            updateActiveIndicator();
            dotsContainer.show();
        } else {
            dotsContainer.hide();
            clearInterval(slideInterval);
        }
    }
    function showSlides(slideNumber) {
        if (limitedSlides.length === 0) return;
        if (slideNumber >= limitedSlides.length) {
            slideIndex = 0;
        }
        if (slideNumber < 0) {
            slideIndex = limitedSlides.length - 1;
        }
        limitedSlides.hide();
        const dots = $(".dot");
        dots.removeClass("active");
        if (limitedSlides.length > 0) {
            limitedSlides.eq(slideIndex % limitedSlides.length).show();
            dots.eq(slideIndex % numDots).addClass("active");
        }
    }
    function changeSlide() {
        slideIndex++;
        showSlides(slideIndex);
    }
    function updateActiveIndicator() {
        let active = $(".dot.active");
        let next = active.next(".dot");
        if (next.length === 0) {
            next = $(".dot").first();
        }
        active.removeClass("active");
        next.addClass("active");
    }
    function highlightDot(index) {
        const dots = $(".dot");
        dots.removeClass("active");
        dots.eq(index).addClass("active");
    }
    dotsContainer.on("click", ".dot", function () {
        slideIndex = $(this).data("index");
        showSlides(slideIndex);
        highlightDot(slideIndex);
    });
    let slideInterval = setInterval(changeSlide, interval);
    $("#testimonial_carousel").hover(
        function () {
            clearInterval(slideInterval);
        },
        function () {
            slideInterval = setInterval(changeSlide, interval);
        }
    );
    function initializeCarousel() {
        if (limitedSlides.length > 0) {
            createDots();
            showSlides(slideIndex);
            carouselContainer.show();
        } else {
            carouselContainer.hide();
        }
    }
    initializeCarousel();
});
$(document).ready(function () {
    var colors = ["color1", "color2", "color3"];
    $(".pmi-pdu-breakdown-content table tr:first-child td").each(function (
        index
    ) {
        $(this).addClass(colors[index % colors.length]);
    });
});
$(document).ready(function () {
    $(".page-search-courses").keyup(function (event) {
        let searchValue = $(this).val().trim();

        if (searchValue !== "") {
            $(".filter-page-courses-container").show();
            getSearchCoursesDetails(searchValue);
        } else {
            handleSearchBoxClear();
        }

        // Enter key triggers search
        if (event.keyCode === 13) {
            performPageSearch();
        }
    });

    // Search icon click
    $(".training-search-icon.page-search-courses").click(function () {
        performPageSearch();
    });

    function performPageSearch() {
        let searchValue = $(".page-search-courses").val().trim();
        let selectedNid = $("#selected-nid").val();
        let selectedUrl = $("#course-summary").attr("href");

        if (selectedNid && selectedUrl) {

            window.location.href = selectedUrl;
        } else if (searchValue !== "") {

            getSearchCoursesDetails(searchValue);
        } else {
            handleSearchBoxClear();
        }
    }

    function handleSearchBoxClear() {
        $(".page-search-courses").val("");
        $("#filter-page-courses-container").empty();
        $(".filter-page-courses-container").hide();

        $("#course-summary").attr("href", "").text("");
        $("#selected-nid").val("");
        $("#selected-node-type").val("");
    }

    function normalizeUrl(url) {
        let cleanedPath = url.replace(/^\/+/, "");
        return window.location.origin + "/" + cleanedPath;
    }
    
    function getSearchCoursesDetails(title) {
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/filter/coursepagetitle",
            method: "GET",
            data: { title: title },
            success: function (response) {
                $("#filter-page-courses-container").empty();

                if (response.filterPageCourse && response.filterPageCourse.length > 0) {
                    let filtercoursepagetitle = response.filterPageCourse;
                    filtercoursepagetitle.forEach(function (course) {
                        let title = course.title;
                        let nid = course.nid;
                        let node_type = course.type;
                        let url = normalizeUrl(course.url);

                        $("#filter-page-courses-container").append(
                            `<li class='list-unstyled course-item' 
                              data-nid='${nid}' 
                              data-node-type='${node_type}' 
                              data-url='${url}'>${title}</li>`
                        );
                    });

                    $("#filter-page-courses-container").show();

                    $(".course-item").off("click").on("click", function () {
                        let selectedTitle = $(this).text();
                        let selectedNid = $(this).data("nid");
                        let selectedUrl = $(this).data("url");
                        let selectedNodeType = $(this).data("node-type");

                        $(".page-search-courses").val(selectedTitle);

                        $("#course-summary").attr("href", selectedUrl).text(selectedTitle);
                        $("#selected-nid").val(selectedNid);
                        $("#selected-node-type").val(selectedNodeType);

                        $(".filter-page-courses-container").hide();
                        window.location.href = selectedUrl;
                    });
                } else {
                    $("#filter-page-courses-container")
                        .empty()
                        .append("<li class='no-results list-unstyled'>No results found</li>")
                        .show();
                }
            },
            error: function () {
                $("#filter-courses-list").hide();
                $(".filter-courses-container").hide();
            },
        });
    }


});

$(document).ready(function () {
    const slider = $(".aboutindustry-slider");
    const slides = $(".industry-slide");
    const slideWidth = slides.outerWidth(true);
    const slideCount = slides.length / 2; // Adjusted because we duplicated slides
    let scrollPosition = 0;
    const autoScrollDelay = 3000; // Auto-scroll every 3 seconds
    let autoScrollTimer;
    slider.append(slides.clone());
    function startAutoScroll() {
        stopAutoScroll();
        autoScrollTimer = setInterval(function () {
            scrollPosition += slideWidth;
            slider.animate(
                { scrollLeft: scrollPosition },
                800,
                "linear",
                function () {
                    if (scrollPosition >= slideWidth * slideCount) {
                        scrollPosition = 0;
                        slider.scrollLeft(scrollPosition);
                    }
                }
            );
        }, autoScrollDelay);
    }
    function stopAutoScroll() {
        clearInterval(autoScrollTimer);
    }
    startAutoScroll();
    slider
        .on("mouseenter", function () {
            stopAutoScroll();
        })
        .on("mouseleave", function () {
            startAutoScroll();
        });
    $(".industry-right-btn").on("click", function () {
        stopAutoScroll();
        scrollPosition += slideWidth;
        slider.animate(
            { scrollLeft: scrollPosition },
            800,
            "linear",
            function () {
                if (scrollPosition >= slideWidth * slideCount) {
                    scrollPosition = 0;
                    slider.scrollLeft(scrollPosition);
                }
            }
        );
        startAutoScroll();
    });
    $(".industry-left-btn").on("click", function () {
        stopAutoScroll();
        scrollPosition -= slideWidth;
        if (scrollPosition < 0) {
            scrollPosition = slideWidth * slideCount - slideWidth;
            slider.scrollLeft(scrollPosition);
        }
        slider.animate({ scrollLeft: scrollPosition }, 800, "linear");
        startAutoScroll();
    });
});
$(window).on("load", function () {
    const slider = $(".partnerslogo-slider");
    const slides = $(".partners-slide");
    const slideWidth = slides.outerWidth(true);
    const slideCount = slides.length / 2; // Adjusted because we duplicated slides
    let scrollPosition = 0;
    const autoScrollDelay = 3000; // Set the auto-scroll delay to 3 seconds (3000 ms)
    let autoScrollTimer;
    slider.append(slides.clone());
    slider.scrollLeft(scrollPosition);
    function startAutoScroll() {
        stopAutoScroll(); // Ensure no overlapping timers
        autoScrollTimer = setInterval(function () {
            scrollPosition += slideWidth;
            slider.animate(
                { scrollLeft: scrollPosition },
                700,
                "linear",
                function () {
                    if (scrollPosition >= slideWidth * slideCount) {
                        scrollPosition = 0;
                        slider.scrollLeft(scrollPosition);
                    }
                }
            );
        }, autoScrollDelay);
    }
    function stopAutoScroll() {
        clearInterval(autoScrollTimer);
    }
    startAutoScroll();
    slider
        .on("mouseenter", function () {
            stopAutoScroll();
        })
        .on("mouseleave", function () {
            startAutoScroll();
        });
    $(".partnership-right-btn").on("click", function () {
        stopAutoScroll();
        scrollPosition += slideWidth;
        slider.animate(
            { scrollLeft: scrollPosition },
            800,
            "linear",
            function () {
                if (scrollPosition >= slideWidth * slideCount) {
                    scrollPosition = 0;
                    slider.scrollLeft(scrollPosition);
                }
            }
        );
        startAutoScroll();
    });
    $(".partnership-left-btn").on("click", function () {
        stopAutoScroll();
        scrollPosition -= slideWidth;
        if (scrollPosition < 0) {
            scrollPosition = slideWidth * slideCount - slideWidth;
            slider.scrollLeft(scrollPosition);
        }
        slider.animate({ scrollLeft: scrollPosition }, 800, "linear");
        startAutoScroll();
    });
    $(window).on("resize", function () {
        const newSlideWidth = slides.outerWidth(true);
        if (newSlideWidth !== slideWidth) {
            scrollPosition = slider.scrollLeft();
            slider.scrollLeft(scrollPosition);
        }
    });
    console.log("Initial slideWidth:", slideWidth);
    console.log("Total slides count:", slideCount);
});
document.addEventListener("DOMContentLoaded", function () {
    var paragraphs = document.querySelectorAll(".training-content p");

    paragraphs.forEach(function (paragraph) {
        var cleanedContent = paragraph.innerHTML.replace(/&nbsp;/g, "").trim();
        if (cleanedContent === "") {
            paragraph.style.display = "none";
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    document
        .querySelectorAll(".consultant-testimonial-toggle")
        .forEach(function (toggle) {
            toggle.addEventListener("click", function (e) {
                e.preventDefault();
                const action = this.getAttribute("data-action");
                const testimonialId = this.closest(
                    ".testimonial-content"
                ).getAttribute("data-testimonial-id");

                const shortText = this.closest(
                    ".testimonial-content"
                ).querySelector(".short-text");
                const fullText = this.closest(
                    ".testimonial-content"
                ).querySelector(".full-text");

                if (action === "more") {
                    shortText.classList.add("hidden");
                    fullText.classList.remove("hidden");
                } else if (action === "less") {
                    shortText.classList.remove("hidden");
                    fullText.classList.add("hidden");
                }
            });
        });
});
var menuHolder = document.getElementById("menuHolder");
var siteBrand = document.getElementById("siteBrand");
function menuToggle() {
    if (menuHolder.className === "drawMenu") menuHolder.className = "";
    else menuHolder.className = "drawMenu";
}
