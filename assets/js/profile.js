import axios from 'axios';

$(document).ready(function () {
    getProfileInformationAndEditForm('/profile/edit');

    $(document).on("click", "#saveChangeButton", function () {
        let formData = new FormData(document.getElementById("profileForm"));

        getProfileInformationAndEditForm('/profile/edit', 'post', formData);
    });

    $(document).on("click", "#deleteImageButton", function () {
        axios.get('/profile/deleteImage')
            .then(function () {
                getProfileInformationAndEditForm('/profile/edit');
            })
            .catch(function (error) {
                console.log(error);
            })
    });

    $(document).on("click", "#editImageButton", function () {
        $('#profile_form_image').toggleClass("d-none");
    });

    function getProfileInformationAndEditForm(url, method = 'get', data = null)
    {
        axios({ method: method, url: url, data: data})
            .then(function (response) {
                $("#profilePage").html(response.data.profileInformation);
                $("#exampleModal").html(response.data.editForm);

                if (response.data.message === 'formValid') {
                    $("#exampleModal").modal('toggle');
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    }
});
