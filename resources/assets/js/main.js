import {getReq} from './ajax.js';
window.$ = window.jQuery = require('jquery');
window.croppie = require('croppie');
window.Handlebars = require('handlebars');

$(document).ready(function () {
    console.log("DOCUMENT READY");

    $('#doctorForm').bind('keypress keydown keyup', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
        }
    });

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
    $(document).on("click", ".removeWeekdayRow", function () {
        const $row = $(this).closest("div");
        $row.prev().find(".addWeekdayRow").prop('disabled', false).removeClass("invisible");
        $row.remove();
    });

    /* SEARCH PROERTIES OR SERVICES AND FILL IN LIST */
    searchCustomOptions();
    

});
$(window).on("resize", function () {
    console.log("RESIZE");
});
$(window).on("scroll", function () {
    makeStickyHeader();
});

const makeStickyHeader = () => {
    let scroll = $(window).scrollTop();
    if (scroll > 0) {
        $("header").addClass("sticky");
    } else {
        $("header").removeClass("sticky");
    }
};

const initCroppie = (width, height) => {
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

const updateCroppie = ($croppieEl, fileInput) => {
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

const saveCroppie = ($croppieEl) => {
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

const selectAvatar = (url) => {
    updateAvatar(url);
    closeModal();
    $('#doc_profile_pic2').val(url);
};

const updateAvatar = (img) => {
    $('.avatar').css({
        'background-image': 'url(' + img + ')',
    });
};

const closeModal = () => {
    $(".modal").removeClass("open");
};

const openModal = (modal) => {
    $(".modal").removeClass("open");
    $("#" + modal).addClass("open");
};

const addNewWeekdayRow = ($insertAfter, weekday) => {
    const source = $("#weekdayRowTemplate").html();
    const template = Handlebars.compile(source);
    const rowHtml = template({weekday: weekday});
    $(rowHtml).insertAfter($insertAfter);
};

const searchCustomOptions = () => {
    $(".searchOptions").on("input", function () {
        const type = $(this).data("type");
        const serachUrl = type === "properties" ? 
                                    "/get-properties?name=" + $(this).val() + "&category_id=" + $(this).data("category") : 
                                    "/get-services?name=" + $(this).val();
        const $optionInput = $(this);
        getReq(serachUrl).then((response) => {
            const source = $("#optionsTemplate").html();
            const template = Handlebars.compile(source);
            const optionsHtml = template({options: response});
            $optionInput.next().html(optionsHtml).slideDown();
        });

    });
    $(".searchOptions").keyup(function (e) {
        const $highlighted = $('.customOptions .highlighted');
        const $li = $('.customOptions ul li');
        const type = $(this).data("type");
        if (e.keyCode === 40) {
            $highlighted.removeClass('highlighted').next().addClass('highlighted');
            if ($highlighted.next().length === 0) {
                $li.eq(0).addClass('highlighted');
            }
        } else if (e.keyCode === 38) {
            $highlighted.removeClass('highlighted').prev().addClass('highlighted');
            if ($highlighted.prev().length === 0) {
                $li.eq(-1).addClass('highlighted');
            }
        } else if (e.keyCode === 13) {
            if ($highlighted.length === 0) {
                addCustomOption($(this), type);
            } else {
                selectCustomOption($highlighted, type);
            }
        }
    });
    $(document).on("click", ".customOptions li", function () {
        const type = $(this).closest(".formRow").find(".searchOptions").data("type");
        selectCustomOption($(this), type);
    });
};
const addCustomOption = ($option, type) => {
    const source = type === "properties" ? $("#propertyInputTemplate").html() : $("#serviceInputTemplate").html();
    const template = Handlebars.compile(source);
    const rowHtml = template({id: $option.val(), categoryId: $option.data("category"), name: $option.val()});
    $(rowHtml).insertBefore($option.closest(".formRow"));
    hideCustomOptions($option.next());
};
const selectCustomOption = ($option, type) => {
    const source = type === "properties" ? $("#propertyInputTemplate").html() : $("#serviceInputTemplate").html();
    const template = Handlebars.compile(source);
    const rowHtml = template({id: $option.data("option-id"), categoryId: $option.data("category-id"), name: $option.data("option-name")});
    $(rowHtml).insertBefore($option.closest(".formRow"));
    hideCustomOptions($option.closest(".customOptions"));
};
const hideCustomOptions = ($el) => {
    $el.closest(".formRow").find("input").val("");
    $el.hide();
};