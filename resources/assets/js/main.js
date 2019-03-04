import {getReq, saveRating} from './ajax.js';
window.$ = window.jQuery = require('jquery');
window.croppie = require('croppie');
window.Handlebars = require('handlebars');
require('jquery-validation');

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

    /* MANAGE PHOTO UPLOAD */
    $(".photoInput").on("change", function (e) {
        const file = e.target.files[0];
        const photoInput = $(this);
        const reader = new FileReader();
        reader.onloadend = function () {
            photoInput.css({"background-image": "url(" + reader.result + ")"}).removeClass("empty");
        };
        if (file) {
            reader.readAsDataURL(file);
        }
    });
    $(".photoInput .closeButton").on("click", function () {
        const $photoInput = $(this).closest(".photoInput");
        $photoInput.find("input").val(null);
        $photoInput.addClass("empty");
    });

    $("#gdpr_agreed").on("change", function () {
        if ($(this).prop("checked")) {
            $("#submit_form").prop("disabled", false);
        } else {
            $("#submit_form").prop("disabled", true);
        }
    });

    /* VALIDATE FORM */
    validateForm();

    /* ADD MAP BOX TO DOCTOR'S PROFILE PAGE */
    if ($("body").hasClass("doctor")) {
        console.log(window.location);
        const map = createMapbox($("#map").data("lat"), $("#map").data("lng"));
        const userMarker = new google.maps.Marker({
            position: new google.maps.LatLng($("#map").data("lat"), $("#map").data("lng")),
            map: map,
            icon: window.location.origin + '/images/marker.png',
            zIndex: google.maps.Marker.MAX_ZINDEX + 1,
            animation: google.maps.Animation.BOUNCE
        });
    }

    /* ADD RATING TO DOCTOR */
    manageStarsRating();
    $("#rateDoctorForm").on("submit", function (e) {
        e.preventDefault();
        const rating = rateDoctor();
        const comment = $("#comment").val();
        const userId = $("#userId").val();
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        saveRating($(this).attr("action"), {comment, rating, userId}).then((response) => {
           if (response.status === 201){
               $('.successMsg').removeClass('hidden');
               $('#rateDoctorForm').addClass('hidden');
           }
        });
    });


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
            $(".avatar").removeClass("empty");
        });
    }
};

const selectAvatar = (url) => {
    updateAvatar(url);
    closeModal();
    $('#doc_profile_pic2').val(url);
    $(".avatar").removeClass("empty");
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

const validateForm = () => {
    $("form").validate({
        rules: {
            name: {
                required: true
            },
            description: {
                required: true
            },
            email: {
                email: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password_confirmation: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            street: {
                required: true
            },
            post_code: {
                minlength: 5
            },
            city: {
                required: true
            },
            phone: {
                required: true
            }
        },
        messages: {
            name: "Zadejte jméno",
            description: "Zadejte popis",
            email: "Zadejte email ve správném formátu",
            password: "Zadejte heslo",
            password_confirmation: "Hesla se neshoduji",
            street: "Zadejte ulici",
            post_code: "Zadejte PSČ",
            city: "Zadejte město",
            phone: "Zadejte telefonní číslo"
        },
        highlight: function (element) {
            $(element).addClass("error");
        },
        unhighlight: function (element) {
            $(element).removeClass("error");
        }
    });
};

const createMapbox = (lat, lng) => {

    var styles = mapboxStyle();
    var mapsLatLgn = new google.maps.LatLng(lat, lng);
    var mapOptions = {
        center: mapsLatLgn,
        zoom: 15,
        disableDefaultUI: true,
        scrollwheel: false,
        zoomControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: styles
    };
    return new google.maps.Map(document.getElementById('map'), mapOptions);
};

const mapboxStyle = () => {
    return [{
            "featureType": "administrative",
            "elementType": "labels.text.fill",
            "stylers": [{"color": "#444444"}]
        }, {"featureType": "landscape", "elementType": "all", "stylers": [{"color": "#f2f2f2"}]}, {
            "featureType": "poi",
            "elementType": "all",
            "stylers": [{"visibility": "off"}]
        }, {
            "featureType": "poi",
            "elementType": "geometry.fill",
            "stylers": [{"visibility": "on"}, {"color": "#b7b7b7"}]
        }, {
            "featureType": "poi",
            "elementType": "labels.text.fill",
            "stylers": [{"visibility": "on"}, {"color": "#414141"}]
        }, {
            "featureType": "poi.park",
            "elementType": "geometry.fill",
            "stylers": [{"visibility": "on"}, {"color": "#dfdfdf"}]
        }, {
            "featureType": "poi.park",
            "elementType": "labels.text.fill",
            "stylers": [{"visibility": "on"}, {"color": "#565656"}]
        }, {
            "featureType": "road",
            "elementType": "all",
            "stylers": [{"saturation": -100}, {"lightness": 45}]
        }, {
            "featureType": "road.highway",
            "elementType": "all",
            "stylers": [{"visibility": "simplified"}]
        }, {
            "featureType": "road.arterial",
            "elementType": "labels.icon",
            "stylers": [{"visibility": "off"}]
        }, {"featureType": "transit", "elementType": "all", "stylers": [{"visibility": "off"}]}, {
            "featureType": "water",
            "elementType": "all",
            "stylers": [{"color": "#46bcec"}, {"visibility": "on"}]
        }, {
            "featureType": "water",
            "elementType": "labels.text.fill",
            "stylers": [{"visibility": "on"}, {"color": "#ffffff"}]
        }, {"featureType": "water", "elementType": "labels.text.stroke", "stylers": [{"visibility": "off"}]}];
};

const manageStarsRating = () => {
    $('.ratingForm li').on('mouseover', function () {
        const onStar = parseInt($(this).data('value'), 10); // The star currently mouse on

        // Now highlight all the stars that's not after the current hovered star
        $(this).parent().children('li.star').each(function (e) {
            if (e < onStar) {
                $(this).addClass('hover');
            } else {
                $(this).removeClass('hover');
            }
        });

    }).on('mouseout', function () {
        $(this).parent().children('li.star').each(function (e) {
            $(this).removeClass('hover');
        });
    });

    $('.ratingForm li').on('click', function () {
        const onStar = parseInt($(this).data('value'), 10); // The star currently selected
        const stars = $(this).parent().children('li.star');

        $(this).closest(".ratingForm").attr("data-score", onStar);

        for (let i = 0; i < stars.length; i++) {
            $(stars[i]).removeClass('selected');
        }

        for (let i = 0; i < onStar; i++) {
            $(stars[i]).addClass('selected');
        }
    });
};

const rateDoctor = () => {
    const $forms = $(".ratingForm");
    let rating = [];
    $forms.each(function(){
        rating.push({'id': $(this).data('item-id'), 'score': $(this).data('score')});
    });
    return rating;
};

