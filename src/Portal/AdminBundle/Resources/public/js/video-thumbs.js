(function () {
    var thumbWidth = 576;
    var imgRatio = 1.8;
    var video = document.getElementById('video');
    video.onloadedmetadata = function () {
        thumbWidth = (video.videoWidth > thumbWidth) ? thumbWidth : video.videoWidth;
        imgRatio = video.videoWidth / video.videoHeight;
    };

    // open video from local pc
    $('#video_report_attachment_file_file').on('change', function () {
        if (this.files && this.files[0]) {
            var file = this.files[0];
            var reader = new FileReader();
            reader.onload = function () {
                video.src = URL.createObjectURL(file);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#create_thumb').on('click', function () {
        var height = thumbWidth / imgRatio;
        var canvas = document.getElementById('canvas');
        canvas.width = thumbWidth;
        canvas.height = height;
        // create canvas
        canvas.getContext('2d').drawImage(video, 0, 0, thumbWidth, height);
        // set thumb input src
        document.getElementById('video_thumb').value = canvas.toDataURL('image/png');
    });

    // open thumb_image from local pc
    $('#open_thumb_btn').on('click', function () {
        $('#open_thumb').click();
    });
    $('#open_thumb').on('change', function () {
        var canvas = document.getElementById('canvas');
        var img = new Image();
        img.src = URL.createObjectURL(this.files[0]);
        img.onload = function () {
            canvas.width = thumbWidth;
            canvas.height = thumbWidth / imgRatio;
            var dW = canvas.width;
            var dH = img.height * (dW / img.width);
            var dy = (canvas.height - dH) / 2;
            // create canvas
            canvas.getContext('2d').drawImage(img, 0, 0, img.width, img.height, 0, dy, dW, dH);
            // set thumb input src
            document.getElementById('video_thumb').value = canvas.toDataURL('image/png');
        };
    });

    // set canvas height by existing video
    $('#video').on('load', function () {
        $('#canvas').attr({
            height: $(this).height(),
            width: $(this).width()
        });
    });
})();
