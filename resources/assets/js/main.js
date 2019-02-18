window.$ = window.jQuery = require('jquery');
window.croppie = require('croppie');
window.Handlebars = require('handlebars');

const makeStickyHeader = () => {
    let scroll = $(window).scrollTop();
    if (scroll > 0) {
        $("header").addClass("sticky");
    } else {
        $("header").removeClass("sticky");
    }
};

initCroppie = (width, height) => {
    const $croppieEl = $('.croppie').croppie({
        viewport: {
            width: width,
            height: height,
            type: 'circle'
        },
        boundary: {
            width: '100%',
            height: height + 40
        }
    });
    return $croppieEl;
};

updateCroppie = ($croppieEl, fileInput) => {
    if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $croppieEl.croppie('bind', {
                url: e.target.result
            });
        };
        reader.readAsDataURL(fileInput.files[0]);
    }
};

saveCroppie = ($croppieEl) => {
    if (typeof ($(".cr-image").attr("src")) !== "undefined") {
        $croppieEl.croppie('result', {
            type: 'canvas',
            circle: false,
            size: 'original'
        }).then(function (img) {
            updateAvatar(img);
            closeModal();
            $('#doc_profile_pic').val(img);
        });
    }
};

selectAvatar = (url) => {
    updateAvatar(url);
    closeModal();
    $('#doc_profile_pic2').val(url);
};

updateAvatar = (img) => {
    $('.avatar').css({
        'background-image': 'url(' + img + ')',
    });
};

closeModal = () => {
    $(".modal").removeClass("open");
};

openModal = (modal) => {
    $(".modal").removeClass("open");
    $("#" + modal).addClass("open");
};

addNewWeekdayRow = ($insertAfter, weekday) => {
    console.log(weekday);
    const source = $("#weekdayRowTemplate").html();
    const template = Handlebars.compile(source);
    const rowHtml = template({weekday: weekday});
    $(rowHtml).insertAfter($insertAfter);
};

$(document).ready(function () {
    console.log("DOCUMENT READY");

    /* TOGGLE MODAL */
    $(".openModal").on("click", function () {
        openModal($(this).attr('data-modal'));
    });
    $(".closeModal").on("click", function () {
        closeModal();
    });

    /* MANAGE AVATAR */
    const $croppieEl = initCroppie(210, 210);
    $('#avatarInput').on("change", function () {
        updateCroppie($croppieEl, this);
    });
    $('#saveAvatar').on("click", function () {
        saveCroppie($croppieEl);
    });
    $(".selectAvatar").on("click", function () {
        selectAvatar($(this).attr("data-avatar"));
    });

    /* MANAGE WEEKDAYS SELECT */
    $(".weekdaySelect").on("change", function () {
        if (parseInt($(this).val(), 10) > 1) {
            $(this).closest("div").find("input, button").attr("disabled", true);
        } else {
            $(this).closest("div").find("input, button").attr("disabled", false);
        }
    });

    /* MANAGE NEW WEEKDAY ROW */
    $(".addWeekdayRow").on("click", function () {
        const $insertAfter = $(this).closest("div");
        $(this).prop('disabled', true).addClass("invisible");
        addNewWeekdayRow($insertAfter, $insertAfter.data("weekday"));
    });
    $(document).on("click", ".removeWeekdayRow", function(){
        const $row = $(this).closest("div");
        $row.prev().find(".addWeekdayRow").prop('disabled', false).removeClass("invisible");
        $row.remove();
    });
});
$(window).on("resize", function () {
    console.log("RESIZE");
});
$(window).on("scroll", function () {
    makeStickyHeader();
});