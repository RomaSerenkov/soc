import axios from 'axios';

$(document).ready(function () {
    let deleteButtonImageClick = false;
    getProfileInformation();
    getEditForm();

    $(document).on("submit", "#profileForm", function (event) {
        event.preventDefault();

        if (deleteButtonImageClick) {
            deleteProfileImage();
            deleteButtonImageClick = false;
        }

        axios.post('/profile/edit', new FormData(this))
            .then(function (response) {
                $(".modal-body").html(response.data.editForm);

                if (isEmpty(response.data)) {
                    getProfileInformation();
                    $("#profileModal").modal('toggle');
                    getEditForm();
                }
            })
            .catch(function (error) {
                console.log(error);
            });
    });

    $(document).on('hidden.bs.modal', '#profileModal', function () {
        if (deleteButtonImageClick) {
            $('#profile_form_image').toggleClass("d-none");
            $('#userImage').toggleClass("d-none");
        }

        deleteButtonImageClick = false;
    });

    $(document).on("click", "#deleteImageButton", function () {
        deleteButtonImageClick = true;
        $('#profile_form_image').toggleClass("d-none");
        $('#userImage').toggleClass("d-none");
    });

    $(document).on("click", "#editImageButton", function () {
        $('#profile_form_image').toggleClass("d-none");
    });

    function getProfileInformation()
    {
        axios.get('/profile/profileInformation')
            .then(function (response) {
                $("#profilePage").html(response.data.profileInformation);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function getEditForm()
    {
        axios.get('/profile/edit')
            .then(function (response) {
                $(".modal-body").html(response.data.editForm);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    function deleteProfileImage()
    {
        axios.get('/profile/deleteImage')
            .then(function (response) {
            })
            .catch(function (error) {
                console.log(error);
            })
    }

    function isEmpty(obj)
    {
        return Object.keys(obj).length === 0;
    }
});
