$(document).ready(function() {
    $(document).on("submit", "#savedata", async function (e) {
        e.preventDefault();
        loader();
        $('.err').remove();
        const token = await csrftoken();
        var fd = new FormData(this);
        fd.append("_token", token);
        $.ajax({
            url: route('store'),
            type: "POST",
            data: fd,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                loader('hide');
                if (response.status) {
                    sendmsg('success', response.message);
                } else {
                    sendmsg('error', response.message);
                }
            },
            error: function (xhr) {
                loader('hide');
                $('.err').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        const $field = $(`[name="${errors[field]['code']}"]`);

                        if ($field.hasClass('select2')) {
                            $field.next('.select2-container').after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        } else {
                            $field.after(`<div class="err text-danger">${errors[field]['message']}</div>`);
                        }

                        errorMessages.push(errors[field]['message']);
                    }
                    if (errorMessages.length > 0) {
                        sendmsg('error', errorMessages.join('<br>'));
                    }
                } else {
                    sendmsg('error', 'Something went wrong. Please try again later.');
                }
            }
        });
    });
});