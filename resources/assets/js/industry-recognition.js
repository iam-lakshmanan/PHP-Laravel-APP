
$(document).ready(function () {
    $(".main-menu .menu-item").each(function () {
        if ($(this).find("a.active").length > 0) {
            $(this).addClass("menu-highlight");
        }
    });
});
$(document).ready(function () {
    $(".sub-menu-item").each(function () {
        if ($(this).find("a.active").length > 0) {
            $(this).addClass("sub-menu-item-highlight");
        }
    });
});
$(document).ready(function () {
    var columnsWithImages = 0;
    $(".company-news-conainer-data").each(function () {
        $(this)
            .find(".row")
            .each(function () {
                $(this)
                    .find(".col-12.col-md-3.col-lg-2")
                    .each(function () {
                        var imageCount = $(this).find("img").length;

                        if (imageCount > 0) {
                            columnsWithImages++;
                        }
                    });
            });
    });
    console.log(columnsWithImages);
    if (columnsWithImages == 0) {
        $(".col-12.col-md-3.col-lg-2").css("display", "none");

        $(this)
            .find(".col-12.col-md-9.col-lg-10.company-news-data-content")
            .removeClass("col-12 col-md-9 col-lg-10")
            .addClass("col-12 col-md-12 col-lg-12");

        $(this)
            .find(".col-12.col-md-3.col-lg-2.company-news-image-content")
            .removeClass("col-md-3 col-lg-2");
    }
});
$(document).ready(function () {
    $(".nav-menu-item").click(function (e) {
        e.preventDefault();
        $(this).next(".submenu.desktopProductsSubmenu").slideToggle();
    });

    $(".submenu-toggle").click(function (e) {
        e.preventDefault();
        $(this).next(".submenu-inner").slideToggle();
    });
});

