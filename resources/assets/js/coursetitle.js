$(document).ready(function () {
    $(".search-courses").keyup(function (event) {
        let searchValue = $(this).val().trim();

        if (searchValue !== "") {
            $(".filter-courses-container").show();
            getSearchCoursesDetails(searchValue);
        } else {
            $("#filter-courses-container").empty();
            $("#selected-nid").val("");
            $("#course-summary").attr("href", "").text("");
            $(".filter-courses-container").hide();
        }
        if (event.keyCode === 13) {
            performSearch();
        }
    });
    $("img.search-icon").click(function () {
        performSearch();
    });
    function performSearch() {
        let searchValue = $(".search-courses").val().trim();
        let selectedNid = $("#selected-nid").val();
        let selectedUrl = $("#course-summary").attr("href");
        if (selectedNid && selectedUrl) {
            var baseUrl = window.location.origin;
            const selectedUrlCourses = baseUrl + "/" + selectedUrl;
            window.location.href = selectedUrlCourses;
        } else if (searchValue !== "") {
            getSearchCoursesDetails(searchValue);
        } else {
            handleSearchBoxClear();
        }
    }
    function handleSearchBoxClear() {
        $(".search-courses").val("");
        $("#filter-courses-container").empty();
        $(".filter-courses-container").hide();

        $("#course-summary").attr("href", "").text("");
        $("#selected-nid").val("");
    }
    function getSearchCoursesDetails(title) {
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: "/filter/coursetitle",
            method: "GET",
            data: {
                title: title,
            },
            success: function (response) {
                $("#filter-courses-container").empty();
                if (response.filtercourse && response.filtercourse.length > 0) {
                    let filtercoursetitle = response.filtercourse;
                    filtercoursetitle.forEach(function (course) {
                        let title = course.title;
                        let nid = course.nid;
                        let url = course.url;
                        $("#filter-courses-container").append(
                            `<li class='list-unstyled course-item' data-nid='${nid}' data-url='${url}' >${title}</li>`
                        );
                    });
                    $("#filter-courses-container").show();

                    // On click  set values + redirect
                    $(".course-item").click(function () {
                        let selectedTitle = $(this).text();
                        let selectedNid = $(this).attr("data-nid");
                        let selectedUrl = $(this).attr("data-url");

                        // Set into search box + hidden fields
                        $(".search-courses").val(selectedTitle);
                        $("#selected-nid").val(selectedNid);
                        $("#course-summary").attr("href", selectedUrl).text(selectedTitle);

                        // Hide dropdown
                        $(".filter-courses-container").hide();
                        $("#filter-courses-container").empty();

                        // Redirect
                        window.location.href = selectedUrl;
                    });

                } else {
                    console.error("Error: Unable to fetch course title or no results found.");
                    $("#filter-courses-container")
                        .empty()
                        .append("<li class='no-results list-unstyled'>No results found</li>");
                    $("#filter-courses-container").show();
                }
            },
            error: function (xhr, status, error) {
                $("#filter-courses-list").hide();
                $(".filter-courses-container").hide();
            },
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    var contentDivs = document.getElementsByClassName("abstract-drop-content");

    for (
        var contentIndex = 0;
        contentIndex < contentDivs.length;
        contentIndex++
    ) {
        var contentDiv = contentDivs[contentIndex];
        var html = contentDiv.innerHTML;
        contentDiv.innerHTML = html.replace(
            /^(<[^>]*>)*([^\s<])/,
            function (match, p1, p2) {
                return (
                    (p1 || "") + '<span class="text-drop-cap">' + p2 + "</span>"
                );
            }
        );
    }
});
$(document).ready(function () {
    $('#contactForm').on('submit', function (event) {
        event.preventDefault();

        $('.error').remove();

        let isValid = true;

        function isEmpty(value) {
            return !value || value.trim().length === 0;
        }

        const name = $('#name').val().trim();
        if (isEmpty(name)) {
            isValid = false;
            $('#name').after('<span class="error text-danger">Name is required.</span>');
        } else if (name.length > 255) {
            isValid = false;
            $('#name').after('<span class="error text-danger">Name cannot be longer than 255 characters.</span>');
        }

        const email = $('#email').val().trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (isEmpty(email)) {
            isValid = false;
            $('#email').after('<span class="error text-danger">Email is required.</span>');
        } else if (!emailPattern.test(email)) {
            isValid = false;
            $('#email').after('<span class="error text-danger">Please enter a valid email address.</span>');
        }

        const subject = $('#subject').val().trim();
        if (isEmpty(subject)) {
            isValid = false;
            $('#subject').after('<span class="error text-danger">Subject is required.</span>');
        }
        const message = $('#message').val().trim();
        if (isEmpty(message)) {
            isValid = false;
            $('#message').after('<span class="error text-danger">Message is required.</span>');
        }

        const recaptchaResponse = grecaptcha.getResponse();
        if (isEmpty(recaptchaResponse)) {
            isValid = false;
            $('.g-recaptcha').after('<span class="error text-danger">The Google reCAPTCHA is required.</span>');
        }

        if (isValid) {
            this.submit();
        }
    });

    $('#name, #email, #subject, #message').on('input', function () {
        $(this).next('.error').remove();
    });
    grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise.on('change', function () {
            $('.g-recaptcha').next('.error').remove();
        });
    });
});
