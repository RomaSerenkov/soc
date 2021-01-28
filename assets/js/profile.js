$(document).ready(function () {
    getProfileInformation();

    function getProfileInformation()
    {
        fetch('/profile/edit')
            .then(response => response.json())
            .then(data => {
                $("#profilePage").html(data.profileInformation);
                $("#exampleModal").html(data.editForm);
            });
    }

    $(document).on("click", "#editModalButton", function () {
        $("#exampleModal").modal('toggle');
    });

    $(document).on("click", "#saveChangeButton", function () {
        let formData = new FormData(document.getElementById("profileForm"));

        fetch('/profile/edit', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                $("#profilePage").html(data.profileInformation);
                $("#exampleModal").modal('toggle');
            })
            .catch(
                error => console.log(error)
            );
    });

    $(document).on("click", "#editImage", function () {
        $('#profile_form_image').toggleClass("d-none");
    });

    $(document).on("click", "#deleteImage", function () {
        fetch('/profile/deleteImage')
            .then(response => response.json())
            .then(data => {
                $('#userImage').addClass("d-none");
                $('#profile_form_image').toggleClass("d-none");
            });
    });
});
