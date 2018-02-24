"use strict";

$(function () {
    $("#addStudentBtn").click(function () {
        $("#schoolCounter").hide();
        $("#addCourseContainer").hide();
        $("#editCourseContainer").hide();
        $("#editStudentContainer").hide();
        $("#addStudentContainer").show();
    });

    $("#addCourseBtn").click(function () {
        $("#schoolCounter").hide();
        $("#addStudentContainer").hide();
        $("#editCourseContainer").hide();
        $("#editStudentContainer").hide();
        $("#addCourseContainer").show();
    });

    $("#addAdministratorBtn").click(function () {
        $("#administratorCounter").hide();
        $("#editAdministratorContainer").hide();
        $("#addAdministratorContainer").show();
    });

    $("#editCourseBtn").click(function (e) {
        e.preventDefault();
        $("#updateCourse").toggle();
        $("#courseInformation").toggle();
        $("#deleteCourseBtn").toggle();
    });

    $("#editStudentBtn").click(function (e) {
        e.preventDefault();
        $("#updateStudent").toggle();
        $("#studentInformation").toggle();
        $("#deleteStudentBtn").toggle();
    });

    $("#editAdministratorBtn").click(function (e) {
        e.preventDefault();
        $("#updateAdministrator").toggle();
        $("#administratorInformation").toggle();
        $("#deleteAdministratorBtn").toggle();
    });

    $(".phone").mask("000-000-0000");

    $.validate({
        modules: "file",
        modules : "security",
        scrollToTopOnError: false,
    });

    $(".counter").each(function (index, value) {
        let number = $(value).text();
        $(value).animationCounter({
            start: 0,
            end: number,
            step: 1,
            delay: 50
        });
    });
});

function previewImages(input, image, div) {
    $(input).change(function(){
        readURL(this, image);
        $(div).show();
    });
}

function readURL(input, image) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $(image).attr("src", e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function checkForDelete(input) {
    $(input).click(function (e) {
        e.preventDefault();
        swal({
            title: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then(function () {
            $(input).parent().submit();
        })
    });
}

function parentSubmit(element) {
    $(element).click(function () {
        $(this).parent().submit();
    });
}

previewImages("#studentImageInpAdd", "#previewStudentImageSrcAdd", "#previewAddStudentImage");
previewImages("#courseImageInpAdd", "#previewCourseImageSrcAdd", "#previewAddCourseImage");

previewImages("#studentImageInpUpdate", "#previewStudentImageSrcUpdate", "#previewUpdateStudentImage");
previewImages("#courseImageInpUpdate", "#previewCourseImageSrcUpdate", "#previewUpdateCourseImage");

previewImages("#administratorImageInpAdd", "#previewAdministratorImageSrcAdd", "#previewAddAdministratorImage");
previewImages("#administratorImageInpUpdate", "#previewAdministratorImageSrcUpdate", "#previewUpdateAdministratorImage");

checkForDelete("#deleteCourseBtn");
checkForDelete("#deleteStudentBtn");
checkForDelete("#deleteAdministratorBtn");

parentSubmit(".courses");
parentSubmit(".students");
parentSubmit(".administrators");