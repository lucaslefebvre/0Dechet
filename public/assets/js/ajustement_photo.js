var uploadCrop = $('#prev_photo').croppie({
    enableExif: true,
    viewport: {
        width: 250,
        height: 250,
        type: 'circle'
    },
    boundary: { width: 300, height: 300 },
});

$('#user_image').on('change', function () {
    var input = $(this)[0];
    if (input.files && input.files[0]) {
        var reader = new FileReader();
                
        reader.onload = function (e) {
                            uploadCrop.croppie('bind', {
                                url: e.target.result
                            }).then(function(){
                                console.log('jQuery bind complete');
                            });
                        }
        reader.readAsDataURL(input.files[0]);
    }
    else {
        alert("Sorry - you're browser doesn't support the FileReader API");
    }
});

function showResult(result) {
    if (result.src) {
        var img = result.src;
        $('#prep_photo').attr('src', img);
    }
    if (result.blob) {
        var img = result.blob;
        $('#photocoupee').attr('value', img);
    }
}

$('#prev_photo').on('update.croppie', function(ev, cropData) {
    uploadCrop.croppie('result', {
                            type: 'canvas', 
                            size: 'viewport',
                        }).then(function (value) {
                            showResult({src: value});
                        });
});

$('#prev_photo').on('update.croppie', function(ev, cropData) {
    uploadCrop.croppie('result', {
                            type: 'canvas', 
                            size: 'viewport',
                        }).then(function (value) {
                            showResult({blob: value});
                        });
});